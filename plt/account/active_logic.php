<?php

require_once dirname(__FILE__).'/aws_s3_db.php';
require_once dirname(__FILE__).'/conf.php';
require_once dirname(__FILE__).'/seaslog.php';
require_once dirname(__FILE__).'/common.php';
require_once dirname(__FILE__).'/protocol.php';

require_once dirname(__FILE__).'/../modules/aws-autoloader.php';
use Aws\DynamoDb\Enum\Type;
use Aws\DynamoDb\Enum\ComparisonOperator;
use Aws\DynamoDb\Enum\AttributeAction;

// 业务逻辑的处理函数
Class CActiveLogic
{
	// ========================================================================================== //
	// command = active
	// key0 = ${rid}
	// key1 = ${email}
	// key2 = ${passwd}
	// key3 = ${th_id}
	// key4 = ${type}
	// key5 = ${register_platfrom}
	// key6 = ${device}
	// key7 = ${pid}
	// key8 = ${game_platform}
	// key9 = ${app_uid}
	// key10 = ${product_vs}
	public static function active($HttpParams, $Seq)
	{   
		// 参数校验
		if(!array_key_exists("key0", $HttpParams)
			|| !array_key_exists("key1", $HttpParams)
			|| !array_key_exists("key2", $HttpParams)
			|| !array_key_exists("key3", $HttpParams)
			|| !array_key_exists("key4", $HttpParams)
			|| !array_key_exists("key5", $HttpParams))
	    {
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "params not incomplete"), $Seq);
	   		// CCommon::consloe_out("params not incomplete");
	   		CCommon::consloe_out("Invalid Request! Please try again.");
	        return -1;
		}
		$rid = $HttpParams['key0'];
		$email = strtolower($HttpParams['key1']);
		$passwd = $HttpParams['key2'];
		$th_id = $HttpParams['key3'];	// 格式(fd_id:facebook_snow)
		$type = $HttpParams['key4'];
		$register_platfrom = $HttpParams['key5'];
		$device = $HttpParams['did'];
		$pid = $HttpParams['pid'];
		$game_platform = $HttpParams['platform'];
		$app_uid = $HttpParams['uid']; 	
		$product_vs = $HttpParams['vs']; 
		$r_pid = $HttpParams['r_pid'];


		if(null == $rid)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "rid is null"), $Seq);
			// CCommon::consloe_out("rid is null");
			CCommon::consloe_out("Invalid Request! Please try again.");
	        return -2;
		}
		if(null == $email)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "email is null"), $Seq);
			// CCommon::consloe_out("email is null");
			CCommon::consloe_out("Invalid Request! Please try again.");
	        return -2;
		}
		if(null == $passwd)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "passwd is null"), $Seq);
			// CCommon::consloe_out("passwd is null");
			CCommon::consloe_out("Invalid Request! Please try again.");
	        return -2;
		}
		if(null == $register_platfrom)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "register_platfrom is null"), $Seq);
			// CCommon::consloe_out("register_platfrom is null");
			CCommon::consloe_out("Invalid Request! Please try again.");
	        return -2;	
		}

		$active_flag = false;
		if(null == $th_id)
		{
			CLog::LOG_INFO(array(__FILE__, __LINE__, "email active, email = ".$email), $Seq);
			$active_info = array();
			$active_info['rid'] = $rid;
			$active_info['email'] = $email;
	    	$active_info['passwd'] = $passwd;
	    	$active_info['type'] = $type;
	    	$active_info['register_platfrom'] = $register_platfrom;
	    	$active_info['device'] = $device;
	    	$active_info['pid'] = $pid;
	    	$active_info['game_platform'] = $game_platform;
			$active_info['app_uid'] = $app_uid;
			$active_info['product_vs'] = $product_vs;
			$active_info['r_pid'] = $r_pid;
			$ret = CActiveLogic::active_email($active_info, $Seq);
			if(0 == $ret)
			{
				$active_flag = true;
				CLog::LOG_INFO(array(__FILE__, __LINE__, "email active sucess, email = ".$email), $Seq);			
			}
		}
		else
		{
			CLog::LOG_INFO(array(__FILE__, __LINE__, "third active, th_id = ".$th_id), $Seq);
			$active_info = array();
			$active_info['rid'] = $rid;
			$active_info['email'] = $email;
	    	$active_info['passwd'] = $passwd;
	    	$active_info['type'] = $type;
	    	$active_info['register_platfrom'] = $register_platfrom;
	    	$active_info['device'] = $device;
	    	$active_info['pid'] = $pid;
	    	$active_info['game_platform'] = $game_platform;
	    	$active_info['app_uid'] = $app_uid;
	    	$active_info['product_vs'] = $product_vs;
			$active_info['th_id'] = $th_id;
			$active_info['r_pid'] = $r_pid;
			$ret = CActiveLogic::active_th_id($active_info, $Seq);
			if(0 == $ret)
			{
				$active_flag = true;
				CLog::LOG_INFO(array(__FILE__, __LINE__, "th_id active sucess, th_id = ".$th_id), $Seq);			
			}
		}

		if(true == $active_flag)
		{
			CLog::LOG_INFO(array(__FILE__, __LINE__, "active success"), $Seq);
			if(null == $device)
			{
				CLog::LOG_INFO(array(__FILE__, __LINE__, "normal active success page"), $Seq);
				CCommon::consloe_out("You have successfully verified your Leyi Account.");


				$account_mail_json_path = CConf::GetAccountMailJson(0);
				$account_mail_json = json_decode(file_get_contents($account_mail_json_path), true);

				$mailtitle = "Subject: ".$account_mail_json['data']['2']['title'];;
				$mailcontent_raw = $account_mail_json['data']['2']['content'];
				$product_project_info = CConf::GetProject($r_pid);
				$mailcontent = str_replace("STRING0", leyi_name, $mailcontent_raw, $count);
				$sender = account_mail_sender;
				$receiver = $email;
				$mailtxtname = $email.".send.".time();
				file_put_contents("./active_mail/".$mailtxtname, $mailtitle."\n", FILE_APPEND);
				file_put_contents("./active_mail/".$mailtxtname, "From: ".$sender."\n", FILE_APPEND);
				file_put_contents("./active_mail/".$mailtxtname, "To: ".$receiver."\n", FILE_APPEND);
				file_put_contents("./active_mail/".$mailtxtname, "\n", FILE_APPEND);
				file_put_contents("./active_mail/".$mailtxtname, $mailcontent, FILE_APPEND);


			}
			else
			{
				CLog::LOG_INFO(array(__FILE__, __LINE__, "binding product active success page"), $Seq);
				CCommon::consloe_out("You have successfully verified your Leyi Account.");


				$account_mail_json_path = CConf::GetAccountMailJson(0);
				$account_mail_json = json_decode(file_get_contents($account_mail_json_path), true);

				$mailtitle = "Subject: ".$account_mail_json['data']['2']['title'];;
				$mailcontent_raw = $account_mail_json['data']['2']['content'];
				$product_project_info = CConf::GetProject($r_pid);
				$mailcontent = str_replace("STRING0", $product_project_info[1], $mailcontent_raw, $count);
				$sender = account_mail_sender;
				$receiver = $email;
				$mailtxtname = $email.".send.".time();
				file_put_contents("./active_mail/".$mailtxtname, $mailtitle."\n", FILE_APPEND);
				file_put_contents("./active_mail/".$mailtxtname, "From: ".$sender."\n", FILE_APPEND);
				file_put_contents("./active_mail/".$mailtxtname, "To: ".$receiver."\n", FILE_APPEND);
				file_put_contents("./active_mail/".$mailtxtname, "\n", FILE_APPEND);
				file_put_contents("./active_mail/".$mailtxtname, $mailcontent, FILE_APPEND);


				
				// 发游戏内部邮件(注册成功&绑定成功)
				CLog::LOG_INFO(array(__FILE__, __LINE__, "send create leyi account in game"), $Seq);
				$product_project_info = CConf::GetProject($r_pid);
				$game_mail_url_params = "";
				$game_mail_url_params = "ope=sendmail&version=1&did=System&lg=1&uid=225&sid=0&command=operate_mail_send";
				$game_mail_url_params = $game_mail_url_params."&key0=2";
				$game_mail_url_params = $game_mail_url_params."&key1=".$app_uid;
				$game_mail_url_params = $game_mail_url_params."&key2=System";
				$game_mail_url_params = $game_mail_url_params."&key3=31";
				$game_mail_url_params = $game_mail_url_params."&key4=";
				$game_mail_url_params = $game_mail_url_params."&key5=";
				$game_mail_url_params = $game_mail_url_params."&key6=0";
				$game_mail_url_params = $game_mail_url_params."&key7=";
				$game_mail_url = $product_project_info[2].$game_mail_url_params;
				CLog::LOG_INFO(array(__FILE__, __LINE__, "game_mail_url = ".$game_mail_url), $Seq);
				$gamemailtxtname = $email.".game_send.".time();
				$gamemailcontent = "wget -Ogame_mail.json ".'"'.$game_mail_url.'"';
				file_put_contents("./game_mail/".$product_project_info[0]."/".$gamemailtxtname, $gamemailcontent);
			}
		}
		else
		{
			CLog::LOG_INFO(array(__FILE__, __LINE__, "active failed"), $Seq);
			// CCommon::consloe_out("active failed");
		}
		return 0;
	}

	// ========================================================================================== //
	// function ==> 邮箱注册激活
	private static function active_email($active_info, $Seq)
	{
		// 查询email是否存在
		$dbClient = CAwsDb::GetInstance()->GetDbClient();
		$account_user_email_response = array();
		$account_product_response = array();

		// 查询email是否存在
		try
		{
			$account_user_email_response =  $dbClient->query(array(
		        // "ConsistentRead" => true,
		        "TableName" => account_user_tbl,
		        "IndexName" => "email-index",
		        // "AttributesToGet" => array("email"),
		        "KeyConditions" => array(
		            "email" => array(
		                "ComparisonOperator" => ComparisonOperator::EQ,
		                "AttributeValueList" => array(
		                    array(Type::STRING => $active_info['email'])
		                )
		            )
		        )
		    ));
		}
		catch(DynamoDbException $e)
        {
        	// CCommon::consloe_out("query user data exception(email)");
        	CCommon::consloe_out("Invalid Request! Please try again.");
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query user data exception(email): ".$e->getMessage()), $Seq);
			return -1;
        }
        if(1 < $account_user_email_response['Count'])
        {
        	// CCommon::consloe_out("multi email");
        	CCommon::consloe_out("Invalid Request! Please try again.");
        	CLog::LOG_ERROR(array(__FILE__, __LINE__, "multi email, ".$active_info['email']), $Seq);
			return -1;
        }


		if(0 == $account_user_email_response['Count'])
        {
        	CLog::LOG_INFO(array(__FILE__, __LINE__, "email can register"), $Seq);

        	// 产品内注册
	        if(null != $active_info['device'])										
	        {
	        	// 检验所需参数
				if(null == $active_info['pid'])
				{
					// CCommon::consloe_out("pid is null");
					CCommon::consloe_out("Invalid Request! Please try again.");
					CLog::LOG_ERROR(array(__FILE__, __LINE__, "pid is null"), $Seq);
			        return -7;
				}
				if(null == $active_info['game_platform'])
				{
					// CCommon::consloe_out("game_platform is null");
					CCommon::consloe_out("Invalid Request! Please try again.");
					CLog::LOG_ERROR(array(__FILE__, __LINE__, "game_platform is null"), $Seq);
			        return -8;
				}
				if(null == $active_info['app_uid'])
				{
					// CCommon::consloe_out("app_uid is null");
					CCommon::consloe_out("Invalid Request! Please try again.");
					CLog::LOG_ERROR(array(__FILE__, __LINE__, "app_uid is null"), $Seq);
			        return -9;
				}
				if(null == $active_info['product_vs'])
				{
					// CCommon::consloe_out("product_vs is null");
					CCommon::consloe_out("Invalid Request! Please try again.");
					CLog::LOG_ERROR(array(__FILE__, __LINE__, "product_vs is null"), $Seq);
			        return -9;
				}
				if(null == $active_info['r_pid'])
				{
					// CCommon::consloe_out("r_pid is null");
					CCommon::consloe_out("Invalid Request! Please try again.");
					CLog::LOG_ERROR(array(__FILE__, __LINE__, "r_pid is null"), $Seq);
			        return -9;
				}

		    	// 查询app_uid + r_pid是否存在
				try
				{
					$account_product_response =  $dbClient->query(array(
				        // "ConsistentRead" => true,
				        "TableName" => account_product_tbl,
				        // "AttributesToGet" => array("email"),
				        "KeyConditions" => array(
			        		'app_uid' => array(
	                            'ComparisonOperator' => 'EQ',
	                            'AttributeValueList' => array(
	                                array(Type::STRING => $active_info['app_uid'])
	                            )
	                        ),
				            "r_pid" => array(
				                "ComparisonOperator" => ComparisonOperator::EQ,
				                "AttributeValueList" => array(
				                    array(Type::STRING => $active_info['r_pid'])
				                )
				            )
				        )
				    ));
				}
				catch(DynamoDbException $e)
		        {
		        	CCommon::consloe_out("Invalid Request! Please try again.");
		        	// CCommon::consloe_out("query product data exception (app_uid + r_pid): ".$e->getMessage());
					CLog::LOG_ERROR(array(__FILE__, __LINE__, "query product data exception (app_uid + r_pid): ".$e->getMessage()), $Seq);
					return -6;
		        }
			    if(0 == $account_product_response['Count'])
			    {
			    	CCommon::consloe_out("Invalid Request! Please try again.");
			    	// CCommon::consloe_out("device not in our product");
			    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "device not in our product"), $Seq);
			    	return -7;
			    }	    	
			    if(1 == $account_product_response['Items'][0]['status']['N']) 							// 帐号已绑定
		    	{
		    		CCommon::consloe_out("Invalid Request! Please try again.");
		    		// CCommon::consloe_out("product already binding, account_product_response");
		    		CLog::LOG_ERROR(array(__FILE__, __LINE__, "product already binding, account_product_response = ".$account_product_response), $Seq);
		    		return -9;
		    	}
		    	if($active_info['device'] != $account_product_response['Items'][0]['device']['S'])
		    	{
		    		CCommon::consloe_out("Invalid Request! Please try again.");
		    		// CCommon::consloe_out("not same device");
		    		CLog::LOG_ERROR(array(__FILE__, __LINE__, "not same device, account_product_response = ".$account_product_response), $Seq);
		    		return -8;
		    	}
	        }

	        $login_platform = "";
	        if(null != $active_info['device'])
	        {
 				$login_platform	= $active_info['game_platform'];
	        }
	        else
	        {
	        	$login_platform = "web";
	        }


	    	$account_user_email_update_response = array();
			$account_product_update_response = array();

	        // 生成account_user记录
			try
		    {
	            $account_user_email_update_response = $dbClient->updateItem(array(
	            'TableName' => account_user_tbl,
	            "Key" => array(
	                "rid" => array(
	                    Type::STRING => $active_info['rid']
	                    )
	                ),
	            "AttributeUpdates" => array(
	           		"email" => array(
	                    "Action" => AttributeAction::PUT,
	                    "Value" => array(
	                        Type::STRING => $active_info['email']
	                        )
	                    ),	 
	           		"passwd" => array(
	                    "Action" => AttributeAction::PUT,
	                    "Value" => array(
	                        Type::STRING => $active_info['passwd']
	                        )
	                    ),	 
	           		"type" => array(
	                    "Action" => AttributeAction::PUT,
	                    "Value" => array(
	                        Type::STRING => $active_info['type']
	                        )
	                    ),	 
	           		"register_platfrom" => array(
	                    "Action" => AttributeAction::PUT,
	                    "Value" => array(
	                        Type::STRING => $active_info['register_platfrom']
	                        )
	                    ),	
	           		"login_platform" => array(
	                    "Action" => AttributeAction::PUT,
	                    "Value" => array(
	                        Type::STRING => $login_platform
	                        )
	                    ),	
	           		"ctime" => array(
	                    "Action" => AttributeAction::PUT,
	                    "Value" => array(
	                        Type::NUMBER => time()
	                        )
	                    ),
	           		"utime" => array(
	                    "Action" => AttributeAction::PUT,
	                    "Value" => array(
	                        Type::NUMBER => time()
	                        )
	                    ),
	           		"status" => array(
	                    "Action" => AttributeAction::PUT,
	                    "Value" => array(
	                        Type::NUMBER => 0
	                        )
	                    ),
	           		"logstatus" => array(
	                    "Action" => AttributeAction::PUT,
	                    "Value" => array(
	                        Type::NUMBER => 0
	                        )
	                    ),
	           		"pwd_flag" => array(
	                    "Action" => AttributeAction::PUT,
	                    "Value" => array(
	                        Type::NUMBER => 0
	                        )
	                    ),
         			"pwd_seq" => array(
	                    "Action" => AttributeAction::PUT,
	                    "Value" => array(
	                        Type::NUMBER => 0
	                        )
	                    )
	                )
	            ));
	        }
	        catch(Exception $e)
	        {
	        	CCommon::consloe_out("Invalid Request! Please try again.");
	        	// CCommon::consloe_out("update user data exception: ".$e->getMessage());
				CLog::LOG_ERROR(array(__FILE__, __LINE__, "update user data exception: ".$e->getMessage()), $Seq);
				return -5;
	        }	



			// 更新account_game表(绑定产品)(产品内注册会绑定产品)
	       	if(null != $active_info['device'])			
	        {
	        	$product_info = json_decode($account_product_response['Items'][0]['product_info']['S'], true);
	        	$product_info['binding_vs'] = $active_info['product_vs'];
	        	$product_info['binding_pid'] = $active_info['pid'];
	        	$product_info['binding_platform'] = $active_info['game_platform'];
				try
			    {
		            $account_product_update_response = $dbClient->updateItem(array(
		            'TableName' => account_product_tbl,
		            "Key" => array(
		                "app_uid" => array(
		                    Type::STRING => $active_info['app_uid']
		                    ),
		                "r_pid" => array(
		                    Type::STRING => $active_info['r_pid']
		                    )
		                ),
		            "AttributeUpdates" => array(
		           		"btime" => array(
		                    "Action" => AttributeAction::PUT,
		                    "Value" => array(
		                        Type::NUMBER => time()
		                        )
		                    ),	       
		           		"rid" => array(
		                    "Action" => AttributeAction::PUT,
		                    "Value" => array(
		                        Type::STRING => $active_info['rid']
		                        )
		                    ),
		           		"status" => array(
		                    "Action" => AttributeAction::PUT,
		                    "Value" => array(
		                        Type::NUMBER => 1
		                        )
		                    ),
	           			"product_info" => array(
		                    "Action" => AttributeAction::PUT,
		                    "Value" => array(
		                        Type::STRING => json_encode($product_info)
		                        )
		                    )
		                )
		            ));
		        }
		        catch(Exception $e)
		        {
		        	CCommon::consloe_out("Invalid Request! Please try again.");
		        	// CCommon::consloe_out("update product data exception: ".$e->getMessage());
					CLog::LOG_ERROR(array(__FILE__, __LINE__, "update product data exception: ".$e->getMessage()), $Seq);
					return -5;
		        }	
	        }
	  		return 0;
		}
		else
		{
			CCommon::consloe_out("You have already verified your Leyi Account.");
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "email already be register"), $Seq);
	        return -9;
		}
	}

	// ========================================================================================== //
	// function ==> 第三方注册激活(与邮箱一起注册激活)
	private static function active_th_id($active_info, $Seq)
	{	
		// 检验第三方注册的参数正确性
		if(null == $active_info['th_id'])
		{
			CCommon::consloe_out("Invalid Request! Please try again.");
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "th_id is null"), $Seq);
	        return -1;
		}

		$th_id_arry = explode(':', $active_info['th_id']);
		if(null == $th_id_arry[0]
			|| null == $th_id_arry[1])
		{
			CCommon::consloe_out("Invalid Request! Please try again.");
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "th_id is invaild, th_id = ".$active_info['th_id']), $Seq);
        	return -2;
		}
		$th_id_name = $th_id_arry[0];
		$th_id_id = $th_id_arry[1];


		$dbClient = CAwsDb::GetInstance()->GetDbClient();
		$account_user_email_response = array();
		$account_user_thid_response = array();
		$account_product_response = array();

		// 查询th_id是否存在
		try
		{
			$account_user_thid_response =  $dbClient->query(array(
		        // "ConsistentRead" => true,
		        "TableName" => account_user_tbl,
		        "IndexName" => $th_id_name."-index",
		        // "AttributesToGet" => array("email"),
		        "KeyConditions" => array(
		            $th_id_name => array(
		                "ComparisonOperator" => ComparisonOperator::EQ,
		                "AttributeValueList" => array(
		                    array(Type::STRING => $th_id_id)
		                )
		            )
		        )
		    ));
		}
		catch(DynamoDbException $e)
        {
        	CCommon::consloe_out("Invalid Request! Please try again.");
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query user data exception(th_id): ".$e->getMessage()), $Seq);
			return -2;
        }

		// 查询email是否存在
		try
		{
			$account_user_email_response =  $dbClient->query(array(
		        // "ConsistentRead" => true,
		        "TableName" => account_user_tbl,
		        "IndexName" => "email-index",
		        // "AttributesToGet" => array("email"),
		        "KeyConditions" => array(
		           	"email" => array(
		                "ComparisonOperator" => ComparisonOperator::EQ,
		                "AttributeValueList" => array(
		                    array(Type::STRING => $active_info['email'])
		                )
		            )
		        )
		    ));
		}
		catch(DynamoDbException $e)
        {
        	CCommon::consloe_out("Invalid Request! Please try again.");
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query user data exception(email): ".$e->getMessage()), $Seq);
			return -2;
        }


     	if(1 < $account_user_thid_response['Count'])
        {
        	CCommon::consloe_out("Invalid Request! Please try again.");
        	CLog::LOG_ERROR(array(__FILE__, __LINE__, "multi th_id, ".$active_info['th_id']), $Seq);
			return -1;
        }
     	if(1 < $account_user_email_response['Count'])
        {
        	CCommon::consloe_out("Invalid Request! Please try again.");
        	CLog::LOG_ERROR(array(__FILE__, __LINE__, "multi email, ".$active_info['email']), $Seq);
			return -1;
        }


		if(0 == $account_user_thid_response['Count'])
        {
        	if(0 == $account_user_email_response['Count'])
        	{
        		CLog::LOG_INFO(array(__FILE__, __LINE__, "th_id can register rquest"), $Seq);
				// 产品内注册
		        if(null != $active_info['device'])										
		        {
		        	// 检验所需参数
					if(null == $active_info['pid'])
					{
						CCommon::consloe_out("Invalid Request! Please try again.");
						CLog::LOG_ERROR(array(__FILE__, __LINE__, "pid is null"), $Seq);
				        return -7;
					}
					if(null == $active_info['game_platform'])
					{
						CCommon::consloe_out("Invalid Request! Please try again.");
						CLog::LOG_ERROR(array(__FILE__, __LINE__, "game_platform is null"), $Seq);
				        return -8;
					}
					if(null == $active_info['app_uid'])
					{
						CCommon::consloe_out("Invalid Request! Please try again.");
						CLog::LOG_ERROR(array(__FILE__, __LINE__, "app_uid is null"), $Seq);
				        return -9;
					}
					if(null == $active_info['product_vs'])
					{
						CCommon::consloe_out("Invalid Request! Please try again.");
						CLog::LOG_ERROR(array(__FILE__, __LINE__, "product_vs is null"), $Seq);
				        return -9;
					}
					if(null == $active_info['r_pid'])
					{
						CCommon::consloe_out("Invalid Request! Please try again.");
						CLog::LOG_ERROR(array(__FILE__, __LINE__, "r_pid is null"), $Seq);
				        return -9;
					}

			    	// 查询app_uid + r_pid是否存在
					try
					{
						$account_product_response =  $dbClient->query(array(
					        // "ConsistentRead" => true,
					        "TableName" => account_product_tbl,
					        // "AttributesToGet" => array("email"),
					        "KeyConditions" => array(
				        		'app_uid' => array(
		                            'ComparisonOperator' => 'EQ',
		                            'AttributeValueList' => array(
		                                array(Type::STRING => $active_info['app_uid'])
		                            )
		                        ),
					            "r_pid" => array(
					                "ComparisonOperator" => ComparisonOperator::EQ,
					                "AttributeValueList" => array(
					                    array(Type::STRING => $active_info['r_pid'])
					                )
					            )
					        )
					    ));
					}
					catch(DynamoDbException $e)
			        {
			        	CCommon::consloe_out("Invalid Request! Please try again.");
						CLog::LOG_ERROR(array(__FILE__, __LINE__, "query product data exception (app_uid + r_pid): ".$e->getMessage()), $Seq);
						return -6;
			        }
				    if(0 == $account_product_response['Count'])
				    {
				    	CCommon::consloe_out("Invalid Request! Please try again.");
				    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "device not in our product"), $Seq);
				    	return -7;
				    }	    	
				    if(1 == $account_product_response['Items'][0]['status']['N']) 							// 帐号已绑定
			    	{
			    		CCommon::consloe_out("product already binding, account_product_response = ".$account_product_response);
			    		CLog::LOG_ERROR(array(__FILE__, __LINE__, "product already binding, account_product_response = ".$account_product_response), $Seq);
			    		return -9;
			    	}
			    	if($active_info['device'] != $account_product_response['Items'][0]['device']['S'])
			    	{
			    		CCommon::consloe_out("Invalid Request! Please try again.");
			    		CLog::LOG_ERROR(array(__FILE__, __LINE__, "not same device, account_product_response = ".$account_product_response), $Seq);
			    		return -8;
			    	}
		        }

		  		$login_platform = "";
		        if(null != $active_info['device'])
		        {
	 				$login_platform	= $active_info['game_platform'];
		        }
		        else
		        {
		        	$login_platform = "web";
		        }

				$account_user_rid_update_response = array();
				$account_product_update_response = array();

		        // 生成account_user记录
				try
			    {
		            $account_user_rid_update_response = $dbClient->updateItem(array(
		            'TableName' => account_user_tbl,
		            "Key" => array(
		                "rid" => array(
		                    Type::STRING => $active_info['rid']
		                    )
		                ),
		            "AttributeUpdates" => array(
		           		"email" => array(
		                    "Action" => AttributeAction::PUT,
		                    "Value" => array(
		                        Type::STRING => $active_info['email']
		                        )
		                    ),	 
		           		"passwd" => array(
		                    "Action" => AttributeAction::PUT,
		                    "Value" => array(
		                        Type::STRING => $active_info['passwd']
		                        )
		                    ),	 
		           		$th_id_name => array(
		                    "Action" => AttributeAction::PUT,
		                    "Value" => array(
		                        Type::STRING => $th_id_id
		                        )
		                    ),	 
		           		"type" => array(
		                    "Action" => AttributeAction::PUT,
		                    "Value" => array(
		                        Type::STRING => "leyi:".$active_info['type']
		                        )
		                    ),	 
		           		"register_platfrom" => array(
		                    "Action" => AttributeAction::PUT,
		                    "Value" => array(
		                        Type::STRING => $active_info['register_platfrom']
		                        )
		                    ),	
		           		"login_platform" => array(
		                    "Action" => AttributeAction::PUT,
		                    "Value" => array(
		                        Type::STRING => $login_platform
		                        )
		                    ),	
		           		"ctime" => array(
		                    "Action" => AttributeAction::PUT,
		                    "Value" => array(
		                        Type::NUMBER => time()
		                        )
		                    ),
		           		"utime" => array(
		                    "Action" => AttributeAction::PUT,
		                    "Value" => array(
		                        Type::NUMBER => time()
		                        )
		                    ),
		           		"status" => array(
		                    "Action" => AttributeAction::PUT,
		                    "Value" => array(
		                        Type::NUMBER => 0
		                        )
		                    ),
		           		"logstatus" => array(
		                    "Action" => AttributeAction::PUT,
		                    "Value" => array(
		                        Type::NUMBER => 0
		                        )
		                    ),
		           		"pwd_flag" => array(
		                    "Action" => AttributeAction::PUT,
		                    "Value" => array(
		                        Type::NUMBER => 0
		                        )
		                    ),
	         			"pwd_seq" => array(
		                    "Action" => AttributeAction::PUT,
		                    "Value" => array(
		                        Type::NUMBER => 0
		                        )
		                    )
		                )
		            ));
		        }
		        catch(Exception $e)
		        {
		        	CCommon::consloe_out("Invalid Request! Please try again.");
					CLog::LOG_ERROR(array(__FILE__, __LINE__, "update user data exception: ".$e->getMessage()), $Seq);
					return -5;
		        }	



				// 更新account_game表(绑定产品)(产品内注册会绑定产品)
		       	if(null != $active_info['device'])			
		        {
		        	$product_info = json_decode($account_product_response['Items'][0]['product_info']['S'], true);
		        	$product_info['binding_vs'] = $active_info['product_vs'];
					$product_info['binding_pid'] = $active_info['pid'];
					$product_info['binding_platform'] = $active_info['game_platform'];
					try
				    {
			            $account_product_update_response = $dbClient->updateItem(array(
			            'TableName' => account_product_tbl,
			            "Key" => array(
			                "app_uid" => array(
			                    Type::STRING => $active_info['app_uid']
			                    ),
			                "r_pid" => array(
			                    Type::STRING => $active_info['r_pid']
			                    )
			                ),
			            "AttributeUpdates" => array(
			           		"btime" => array(
			                    "Action" => AttributeAction::PUT,
			                    "Value" => array(
			                        Type::NUMBER => time()
			                        )
			                    ),	       
			           		"rid" => array(
			                    "Action" => AttributeAction::PUT,
			                    "Value" => array(
			                        Type::STRING => $active_info['rid']
			                        )
			                    ),
			           		"status" => array(
			                    "Action" => AttributeAction::PUT,
			                    "Value" => array(
			                        Type::NUMBER => 1
			                        )
			                    ),
		           			"product_info" => array(
			                    "Action" => AttributeAction::PUT,
			                    "Value" => array(
			                        Type::STRING => json_encode($product_info)
			                        )
			                    )
			                )
			            ));
			        }
			        catch(Exception $e)
			        {
			        	CCommon::consloe_out("Invalid Request! Please try again.");
						CLog::LOG_ERROR(array(__FILE__, __LINE__, "update product data exception: ".$e->getMessage()), $Seq);
						return -5;
			        }	
		        }
        		return 0;
        	}
        	else
        	{
        		CCommon::consloe_out("You have already verified your Leyi Account.");
				CLog::LOG_ERROR(array(__FILE__, __LINE__, "email already register, ".$active_info['email']), $Seq);
	        	return -9;
        	}
		}
		else
		{
			CCommon::consloe_out("You have already verified your Leyi Account.");
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "th_id already register, ".$active_info['th_id']), $Seq);
	        return -9;
		}
	}	


}



?>