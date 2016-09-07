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
Class CBindingProductLogic
{
	// ========================================================================================== //
	// command = binding
	// key0 = ${email}
	// key1 = ${passwd}
	// key2 = ${th_id}
	// key3 = ${device}
	// key4 = ${pid}
	// key5 = ${game_platform}
	// key6 = ${app_uid}
	// key7 = ${product_vs}
	public static function binding_product($HttpParams, $Seq)
	{   
		$BeginReqTime = CCommon::GetMicroSecond();
		$result = array();
		// 参数校验
		if(!array_key_exists("key0", $HttpParams)
			|| !array_key_exists("key1", $HttpParams)
			|| !array_key_exists("key2", $HttpParams))
	    {
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "params not incomplete"), $Seq);

	    	$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	    	// todo: 参数错误的返回码
	        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        // $result['retcode'] = -1;
	        $result['info'] = "params not incomplete";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
		}
		$email = strtolower($HttpParams['key0']);
		$passwd = $HttpParams['key1'];
		$th_id = $HttpParams['key2'];	// 格式(fd_id:facebook_snow)
		$device = $HttpParams['did'];
		$pid = $HttpParams['pid'];
		$game_platform = $HttpParams['platform'];
		$app_uid = $HttpParams['uid']; 	
		$product_vs = $HttpParams['vs']; 
		$r_pid = $HttpParams['r_pid'];

		if(null == $r_pid)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "r_pid is null"), $Seq);

	    	$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 参数错误的返回码
			$result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        // $result['retcode'] = -1;
	        $result['info'] = "r_pid is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
		}
		if(null == $device)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "device is null"), $Seq);

	    	$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 参数错误的返回码
			$result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        // $result['retcode'] = -1;
	        $result['info'] = "device is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
		}
		if(null == $pid)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "pid is null"), $Seq);

	    	$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 参数错误的返回码
			$result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        // $result['retcode'] = -1;
	        $result['info'] = "pid is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
		}
		if(null == $game_platform)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "game_platform is null"), $Seq);

	    	$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 参数错误的返回码
			$result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        // $result['retcode'] = -1;
	        $result['info'] = "game_platform is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
		}
		if(null == $app_uid)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "app_uid is null"), $Seq);

	    	$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 参数错误的返回码
			$result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        // $result['retcode'] = -1;
	        $result['info'] = "app_uid is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
		}
		if(null == $product_vs)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "product_vs is null"), $Seq);

	    	$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 参数错误的返回码
			$result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        // $result['retcode'] = -1;
	        $result['info'] = "product_vs is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
		}

		$binding_flag = false;
		if(null == $th_id)
		{
			CLog::LOG_INFO(array(__FILE__, __LINE__, "email binding, email = ".$email), $Seq);
			$binding_info = array();
			$binding_info['email'] = $email;
	    	$binding_info['passwd'] = $passwd;
	    	$binding_info['device'] = $device;
	    	$binding_info['pid'] = $pid;
	    	$binding_info['game_platform'] = $game_platform;
			$binding_info['app_uid'] = $app_uid;
			$binding_info['product_vs'] = $product_vs;
			$binding_info['r_pid'] = $r_pid;
			$ret = CBindingProductLogic::binding_product_email($binding_info, $result, $Seq);
			if(true == $ret)
			{
				$binding_flag = true;
				CLog::LOG_INFO(array(__FILE__, __LINE__, "email binding sucess, email = ".$email), $Seq);	
			}
		}
		else
		{
			CLog::LOG_INFO(array(__FILE__, __LINE__, "third binding, th_id = ".$th_id), $Seq);
			$binding_info = array();
			$binding_info['th_id'] = $th_id;
	    	$binding_info['device'] = $device;
	    	$binding_info['pid'] = $pid;
	    	$binding_info['game_platform'] = $game_platform;
	    	$binding_info['app_uid'] = $app_uid;
	    	$binding_info['product_vs'] = $product_vs;
	    	$binding_info['r_pid'] = $r_pid;
			$ret = CBindingProductLogic::binding_product_th_id($binding_info, $result, $Seq);
			if(true == $ret)
			{
				$binding_flag = true;
				CLog::LOG_INFO(array(__FILE__, __LINE__, "th_id binding sucess, th_id = ".$th_id), $Seq);
			}
		}
		if(true == $binding_flag)
		{
			
			// 发游戏内部邮件(注册成功&绑定成功)
			CLog::LOG_INFO(array(__FILE__, __LINE__, "send bind with leyi account in game"), $Seq);
			$product_project_info = CConf::GetProject($r_pid);
			$game_mail_url_params = "";
			$game_mail_url_params = "ope=sendmail&version=1&did=System&lg=1&uid=225&sid=0&command=operate_mail_send";
			$game_mail_url_params = $game_mail_url_params."&key0=2";
			$game_mail_url_params = $game_mail_url_params."&key1=".$app_uid;
			$game_mail_url_params = $game_mail_url_params."&key2=System";
			$game_mail_url_params = $game_mail_url_params."&key3=32";
			$game_mail_url_params = $game_mail_url_params."&key4=";
			$game_mail_url_params = $game_mail_url_params."&key5=";
			$game_mail_url_params = $game_mail_url_params."&key6=0";
			$game_mail_url_params = $game_mail_url_params."&key7=";
			$game_mail_url = $product_project_info[2].$game_mail_url_params;
			CLog::LOG_INFO(array(__FILE__, __LINE__, "game_mail_url = ".$game_mail_url), $Seq);
			$gamemailtxtname = $email.".game_send.".time();
			$gamemailcontent = "wget -Ogame_mail.json ".'"'.$game_mail_url.'"';
			file_put_contents("./game_mail/".$product_project_info[0]."/".$gamemailtxtname, $gamemailcontent);
			


			$EndReqTime = CCommon::GetMicroSecond();
			$CostTime = $EndReqTime - $BeginReqTime;
			if(null == $th_id)
	        {
	        	echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "account_user_node", json_encode($result['info']), $CostTime, $result['retcode']);
	        }
			else
			{
	        	echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "account_user_node", json_encode($result['info']), $CostTime, $result['retcode']);
			}

			// server request
			// 向后台记录帐号绑定的状态(绑定成功)
			CLog::LOG_INFO(array(__FILE__, __LINE__, "set leyi account binding game data operate to server"), $Seq);
			$product_project_info = CConf::GetProject($r_pid);
			$server_account_url_params = "";
			$server_account_url_params = "lrct=10&did=account-system&sid=0&uid=".$app_uid."&command=operate_account_operate";
			$server_account_url_params = $server_account_url_params."&key0=1";
			$server_account_url = $product_project_info[2].$server_account_url_params;
			CLog::LOG_INFO(array(__FILE__, __LINE__, "server_account_url = ".$server_account_url), $Seq);
			$serverrequesttxtname = $email.".server_request.".time();
			$serverrequestcontent = "wget -Oserver_request.json ".'"'.$server_account_url.'"';
			file_put_contents("./server_request/".$product_project_info[0]."/".$serverrequesttxtname, $serverrequestcontent);


			return true;

		}
		else
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "binding product failed"), $Seq);
		
			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
		}
	}

	// ========================================================================================== //
	// function ==> 邮箱绑定产品
	private static function binding_product_email($binding_info, &$result, $Seq)
	{
		$dbClient = CAwsDb::GetInstance()->GetDbClient();
		$account_user_email_esponse = array();
		$account_product_response = array();

		// 查询email是否存在
		try
		{
			$account_user_email_esponse =  $dbClient->query(array(
		        // "ConsistentRead" => true,
		        "TableName" => account_user_tbl,
		        "IndexName" => "email-index",
		        // "AttributesToGet" => array("email"),
		        "KeyConditions" => array(
		            "email" => array(
		                "ComparisonOperator" => ComparisonOperator::EQ,
		                "AttributeValueList" => array(
		                    array(Type::STRING => $binding_info['email'])
		                )
		            )
		        )
		    ));
		}
		catch(DynamoDbException $e)
        {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query user data exception(email): ".$e->getMessage()), $Seq);
			// todo: 内部数据有误的返回码
			$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	        // $result['retcode'] = -1;
	  		$result['info'] = "query user data exception(email): ".$e->getMessage();
	        return false;
        }

	    if(0 == $account_user_email_esponse['Count'])
	    {
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "email = ".$binding_info['email']." not register"), $Seq);
			// todo: email没有注册的返回码
			$result['retcode'] = EN_RET_CODE__EMAIL_NOT_REGISTER;
	        // $result['retcode'] = -1;
	  		$result['info'] = "email = ".$binding_info['email']." not register";
	        return false;
	    }
	    if($account_user_email_esponse['Count'] > 1)
	    {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "multi email, account_user_email_esponse = ".$account_user_email_esponse), $Seq);
			// todo: 内部数据有误的返回码
			$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	        // $result['retcode'] = -1;
	  		$result['info'] = "multi email";
	        return false;
	    }
	  	if($binding_info['passwd'] != $account_user_email_esponse['Items'][0]['passwd']['S'])
	    {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "email passwd error"), $Seq);
			// todo: email密码有误的返回码
			$result['retcode'] = EN_RET_CODE__PASSWD_ERROR;
	        // $result['retcode'] = -1;
	  		$result['info'] = "email passwd error";
	        return false; 
	    }
	    CLog::LOG_INFO(array(__FILE__, __LINE__, "rid = ".$account_user_email_esponse['Items'][0]['rid']['S']), $Seq);

		// 查询rid的是否有product数据
		try
		{
			$account_product_response =  $dbClient->query(array(
		        // "ConsistentRead" => true,
		        "TableName" => account_product_tbl,
		        "IndexName" => "rid-index",
		        // "AttributesToGet" => array("email"),
		        "KeyConditions" => array(
		            "rid" => array(
		                "ComparisonOperator" => ComparisonOperator::EQ,
		                "AttributeValueList" => array(
		                    array(Type::STRING => $account_user_email_esponse['Items'][0]['rid']['S'])
		                )
		            )
		        )
		    ));
		}
		catch(DynamoDbException $e)
        {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query product data exception(email): ".$e->getMessage()), $Seq);
			// todo: 内部数据有误的返回码
			$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	        // $result['retcode'] = -1;
	  		$result['info'] = "query product data exception(email)";
	        return false;
        }
		if(0 < $account_product_response['Count'])
		{
			$product_exist_count = 0;
	 		foreach($account_product_response['Items'] as $product_response)
	 		{
	            if($product_response['r_pid']['S'] != $binding_info['r_pid'])
	            {
	               	continue;         	
	            }
		    	$product_exist_count++;
	        }
			CLog::LOG_INFO(array(__FILE__, __LINE__, "product_exist_count = ".$product_exist_count), $Seq);	        
		    if(0 != $product_exist_count)
		    {
		    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "rid = ".$account_user_email_esponse['Items'][0]['rid']['S']." already binding product data"), $Seq);
		  		// todo: 该email已经绑定过该产品
		        $result['retcode'] = EN_RET_CODE__EMAIL_ALREADY_BINDING_DATA;
				$result['info'] = "rid = ".$account_user_email_esponse['Items'][0]['rid']['S']." already binding product data";
		        return false;
		    }
	    }

		// 查询app_uid + r_pid的是否有product数据
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
                            array(Type::STRING => $binding_info['app_uid'])
                        )
                    ),
		            "r_pid" => array(
		                "ComparisonOperator" => ComparisonOperator::EQ,
		                "AttributeValueList" => array(
		                    array(Type::STRING => $binding_info['r_pid'])
		                )
		            )
		        )
		    ));
        }
        catch(Exception $e)
        {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query product data exception: ".$e->getMessage()), $Seq);
			// todo: 内部数据有误的返回码
			$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	        // $result['retcode'] = -1;
	  		$result['info'] = "query product data exception: ".$e->getMessage();
	        return false;
        }
		if(0 == $account_product_response['Count'])
		{
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "app_uid = ".$binding_info['app_uid'].", r_pid = ".$binding_info['r_pid']." not product data"), $Seq);
			// todo: 找不到相应的产品数据的返回码
			$result['retcode'] = EN_RET_CODE__NOT_FIND_CURDATA;
	        // $result['retcode'] = -1;
	  		$result['info'] = "app_uid = ".$binding_info['app_uid'].", r_pid = ".$binding_info['r_pid']." not product data";
	        return false;
	    }
		if(1 == $account_product_response['Items'][0]['status']['N'])
		{
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "app_uid = ".$binding_info['app_uid'].", r_pid = ".$binding_info['r_pid']."  product data already binding, rid = ".$account_user_email_esponse['Items'][0]['rid']['S']), $Seq);
			// todo: 当前产品数据已经被绑定的返回码
			$result['retcode'] = EN_RET_CODE__CURDATA_ALREADY_BINDING;
	        // $result['retcode'] = -1;
	  		$result['info'] = "app_uid = ".$binding_info['app_uid'].", r_pid = ".$binding_info['r_pid']." product data already binding";
	        return false;
	    }


		// 更新account_user表
		try
	    {
            $dbClient->updateItem(array(
            'TableName' => account_user_tbl,
            "Key" => array(
                "rid" => array(
                    Type::STRING => $account_user_email_esponse['Items'][0]['rid']['S']
                    )
                ),
            "AttributeUpdates" => array(
           		"utime" => array(
                    "Action" => AttributeAction::PUT,
                    "Value" => array(
                        Type::NUMBER => time()
                        )
                    ),	
    			"login_platform" => array(
                    "Action" => AttributeAction::PUT,
                    "Value" => array(
                        Type::STRING => $binding_info['game_platform']
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
                    )
                )
            ));
        }
        catch(Exception $e)
        {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "update user data exception: ".$e->getMessage()), $Seq);
			// todo: 内部数据有误的返回码
			$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	        // $result['retcode'] = -1;
	  		$result['info'] = "update user data exception: ".$e->getMessage();
	        return false;
        }	


        // 更新account_game表(绑定产品)
    	$product_info = json_decode($account_product_response['Items'][0]['product_info']['S'], true);
    	$product_info['binding_vs'] = $binding_info['product_vs'];
		$product_info['binding_pid'] = $binding_info['pid'];
		$product_info['binding_platform'] = $binding_info['game_platform'];
		try
	    {
            $dbClient->updateItem(array(
            'TableName' => account_product_tbl,
            "Key" => array(
                "app_uid" => array(
                    Type::STRING => $binding_info['app_uid']
                    ),
                "r_pid" => array(
                    Type::STRING => $binding_info['r_pid']
                    )
                ),
            "AttributeUpdates" => array(
           		"rid" => array(
                    "Action" => AttributeAction::PUT,
                    "Value" => array(
                        Type::STRING => $account_user_email_esponse['Items'][0]['rid']['S']
                        )
                    ),	 
           		"btime" => array(
                    "Action" => AttributeAction::PUT,
                    "Value" => array(
                        Type::NUMBER => time()
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
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "update product data exception: ".$e->getMessage()), $Seq);
			// todo: 内部数据有误的返回码
			$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	        // $result['retcode'] = -1;
	  		$result['info'] = "update product data exception: ".$e->getMessage();
	        return false;
        }	
    
      	$result['retcode'] = 0;
  		$result['info']['rid'] = $account_user_email_esponse['Items'][0]['rid']['S'];
        return true;

	}

	// ========================================================================================== //
	// function ==> 第三方绑定产品
	private static function binding_product_th_id($binding_info, &$result, $Seq)
	{	
		// 检验第三方注册的参数正确性
		if(null == $binding_info['th_id'])
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "th_id is null"), $Seq);
	        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        $result['info'] = "th_id is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
		}

		$th_id_arry = explode(':', $binding_info['th_id']);
		if(null == $th_id_arry[0]
			|| null == $th_id_arry[1])
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "th_id is invaild, th_id = ".$binding_info['th_id']), $Seq);
			$result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        $result['info'] = "th_id is invaild, th_id = ".$binding_info['th_id'];
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
		}
		$th_id_name = $th_id_arry[0];
		$th_id_id = $th_id_arry[1];


		$dbClient = CAwsDb::GetInstance()->GetDbClient();
		$account_user_th_id_response = array();
		$account_product_response = array();
		// 查询th_id是否存在
		try
		{
			$account_user_th_id_response =  $dbClient->query(array(
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
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query user data exception(th_id): ".$e->getMessage()), $Seq);
	  		$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	  		$result['info'] = "query user data exception(th_id): ".$e->getMessage();
	        return false;
        }
		if(0 == $account_user_th_id_response['Count'])
	    {
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "th_id_name = ".$th_id_name." not register"), $Seq);
	  		$result['retcode'] = EN_RET_CODE__THIRD_NOT_REGISTER;
	  		$result['info'] = "th_id_name = ".$th_id_name." not register";
	        return false;
	    }
	    if($account_user_th_id_response['Count'] > 1)
	    {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "multi th_id_name, account_user_th_id_response = ".$account_user_th_id_response), $Seq);
	  		$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	  		$result['info'] = "multi th_id_name";
	        return false;
	    }

		// 查询rid的是否有product数据
		try
		{
			$account_product_response =  $dbClient->query(array(
		        // "ConsistentRead" => true,
		        "TableName" => account_product_tbl,
		        "IndexName" => "rid-index",
		        // "AttributesToGet" => array("email"),
		        "KeyConditions" => array(
		            "rid" => array(
		                "ComparisonOperator" => ComparisonOperator::EQ,
		                "AttributeValueList" => array(
		                    array(Type::STRING => $account_user_th_id_response['Items'][0]['rid']['S'])
		                )
		            )
		        )
		    ));
		}
		catch(DynamoDbException $e)
        {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query product data exception(rid): ".$e->getMessage()), $Seq);
			// todo: 内部数据有误的返回码
	  		$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	  		$result['info'] = "query product data exception(rid)";
	        return false;
        }
		if(0 < $account_product_response['Count'])
		{
			$product_exist_count = 0;
	 		foreach($account_product_response['Items'] as $product_response)
	 		{
	            if($product_response['r_pid']['S'] != $binding_info['r_pid'])
	            {
	               	continue;         	
	            }
		    	$product_exist_count++;
	        }
	        CLog::LOG_INFO(array(__FILE__, __LINE__, "product_exist_count = ".$product_exist_count), $Seq);	 
		    if(0 != $product_exist_count)
		    {
		    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "rid = ".$account_user_th_id_response['Items'][0]['rid']['S']." already binding product data"), $Seq);
		  		// todo: 该th_id已经绑定过该产品
		  		$result['retcode'] = EN_RET_CODE__THIRD_ALREADY_BINDING_DATA;
		  		$result['info'] = "rid = ".$account_user_th_id_response['Items'][0]['rid']['S']." already binding product data";
		        return false;
		    }
	    }

		// 查询app_uid + r_pid的是否有product数据
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
                            array(Type::STRING => $binding_info['app_uid'])
                        )
                    ),
		            "r_pid" => array(
		                "ComparisonOperator" => ComparisonOperator::EQ,
		                "AttributeValueList" => array(
		                    array(Type::STRING => $binding_info['r_pid'])
		                )
		            )
		        )
		    ));
        }
        catch(Exception $e)
        {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query product data exception(app_uid + r_pid): ".$e->getMessage()), $Seq);
	  		$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	  		$result['info'] = "query product data exception(app_uid + r_pid): ".$e->getMessage();
	        return false;
        }
		if(0 == $account_product_response['Count'])
		{
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "app_uid = ".$app_uid.", r_pid = ".$binding_info['r_pid']." not product data"), $Seq);
	  		$result['retcode'] = EN_RET_CODE__NOT_FIND_CURDATA;
	  		$result['info'] = "app_uid = ".$app_uid.", r_pid = ".$binding_info['r_pid']." not product data";
	        return false;
	    }
		if(1 == $account_product_response['Items'][0]['status']['N'])
		{
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "app_uid = ".$binding_info['app_uid'].", r_pid = ".$$binding_info['r_pid']."  product data already binding, rid = ".$account_user_th_id_response['Items'][0]['rid']['S']), $Seq);
			$result['retcode'] = EN_RET_CODE__CURDATA_ALREADY_BINDING;
	  		$result['info'] = "app_uid = ".$binding_info['app_uid'].", r_pid = ".$binding_info['r_pid']." product data already binding";
	        return false;
	    }

		// 更新account_user表
		try
	    {
            $dbClient->updateItem(array(
            'TableName' => account_user_tbl,
            "Key" => array(
                "rid" => array(
                    Type::STRING => $account_user_th_id_response['Items'][0]['rid']['S']
                    )
                ),
            "AttributeUpdates" => array(
           		"utime" => array(
                    "Action" => AttributeAction::PUT,
                    "Value" => array(
                        Type::NUMBER => time()
                        )
                    ),	
    			"login_platform" => array(
                    "Action" => AttributeAction::PUT,
                    "Value" => array(
                        Type::STRING => $binding_info['game_platform']
                        )
                    ),	  
           		"logstatus" => array(
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
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "update user data exception: ".$e->getMessage()), $Seq);
			// todo: 内部数据有误的返回码
	  		$result['retcode'] = -1;
	  		$result['info'] = "update user data exception: ".$e->getMessage();
	        return false;
        }

        // 更新account_game表(绑定产品)
    	$product_info = json_decode($account_product_response['Items'][0]['product_info']['S'], true);
    	$product_info['binding_vs'] = $binding_info['product_vs'];
		$product_info['binding_pid'] = $binding_info['pid'];
		$product_info['binding_platform'] = $binding_info['game_platform'];
		try
	    {
            $dbClient->updateItem(array(
            'TableName' => account_product_tbl,
            "Key" => array(
                "app_uid" => array(
                    Type::STRING => $binding_info['app_uid']
                    ),
                "r_pid" => array(
                    Type::STRING => $binding_info['r_pid']
                    )
                ),
            "AttributeUpdates" => array(
       			"rid" => array(
                    "Action" => AttributeAction::PUT,
                    "Value" => array(
                        Type::STRING => $account_user_th_id_response['Items'][0]['rid']['S']
                        )
                    ),	
           		"btime" => array(
                    "Action" => AttributeAction::PUT,
                    "Value" => array(
                        Type::NUMBER => time()
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
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "update product data exception: ".$e->getMessage()), $Seq);
			// todo: 内部数据有误的返回码
	  		$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	  		$result['info'] = "update product data exception: ".$e->getMessage();
	        return false;
        }	
    

      	$result['retcode'] = 0;
  		$result['info']['rid'] = $account_user_th_id_response['Items'][0]['rid']['S'];
        return true;


	}	


}



?>