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
Class CPasswdLogic
{
	// ========================================================================================== //
	// function ==> 忘记密码
	public static function forget_passwd($HttpParams, $Seq)
	{   
		$BeginReqTime = CCommon::GetMicroSecond();
		$result = array();
		// 参数校验
		if(!array_key_exists("key0", $HttpParams))
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
		$pid = $HttpParams['pid'];
		$game_platform = $HttpParams['platform'];

		if(null == $email)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "email is null"), $Seq);
	   		
	    	$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	    	// todo: 参数错误的返回码
			$result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        // $result['retcode'] = -1;
	        $result['info'] = "email is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
		}


		$account_user_email_response = array();
		// 查询email是否存在
		$dbClient = CAwsDb::GetInstance()->GetDbClient();
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
		                    array(Type::STRING => $email)
		                )
		            )
		        )
		    ));
		}
		catch(DynamoDbException $e)
        {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query user data exception(email): ".$e->getMessage()), $Seq);

	    	$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 内部数据有误的返回码
			$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	        // $result['retcode'] = -1;
	  		$result['info'] = "query user data exception(email): ".$e->getMessage();
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
        }
	    if(0 == $account_user_email_response['Count'])
	    {
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "email = ".$email." not register"), $Seq);

	    	$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: email没有注册
			$result['retcode'] = EN_RET_CODE__EMAIL_NOT_REGISTER;
	        // $result['retcode'] = -1;
			$result['info'] = "email = ".$email." not register";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
	    }
	    if($account_user_email_response['Count'] > 1)
	    {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "multi email, account_user_email_response = ".$account_user_email_response), $Seq);

	    	$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 内部数据有误的返回码
			$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	        // $result['retcode'] = -1;
	  		$result['info'] = "multi email";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
	    }


		// 发送外部邮件
		CLog::LOG_INFO(array(__FILE__, __LINE__, "send reset passwd mail"), $Seq);

		$reset_passwd_prefix = web_account_prefix."controller=account&action=resetpasswd&";
		$reset_passwd_url_params = "";
		$reset_passwd_url_params = $reset_passwd_url_params."key0=".urlencode($email);
		$reset_passwd_url_params = $reset_passwd_url_params."&key1=".$account_user_email_response['Items'][0]['pwd_seq']['N'];
		$reset_passwd_url_params = $reset_passwd_url_params."&key2=".$pid;
		$reset_passwd_url_params = $reset_passwd_url_params."&key3=".$game_platform;

		if(0 == en_flag)
		{
			$reset_passwd_url = $reset_passwd_prefix."request=".$reset_passwd_url_params;
		}
		else
		{
			$reset_passwd_url = $reset_passwd_prefix."request=".encrypt_url_php($reset_passwd_url_params);
		}
		CLog::LOG_INFO(array(__FILE__, __LINE__, "reset_passwd_url = ".$reset_passwd_url), $Seq);


		$account_mail_json_path = CConf::GetAccountMailJson(0);
		$account_mail_json = json_decode(file_get_contents($account_mail_json_path), true);

		$mailtitle = "Subject: ".$account_mail_json['data']['3']['title'];;
		$mailcontent_raw = $account_mail_json['data']['3']['content'];

		if(null == $pid)
		{
			$mailcontent = str_replace("STRING0", leyi_name, $mailcontent_raw, $count);
		}
		else
		{
			$r_pid = CConf::GetRealPid($pid);
			if("-1" == $r_pid)
			{
				CLog::LOG_ERROR(array(__FILE__, __LINE__, "r_pid not exist"), $Seq);
				$mailcontent = str_replace("STRING0", leyi_name, $mailcontent_raw, $count);
			}
			else
			{
				$product_project_info = CConf::GetProject($r_pid);
				$mailcontent = str_replace("STRING0", $product_project_info[1], $mailcontent_raw, $count);
			}
		}

		$mailcontent = str_replace("STRING1", $reset_passwd_url, $mailcontent, $count);
		$sender = account_mail_sender;
		$receiver = $email;
		$mailtxtname = $email.".send.".time();
		file_put_contents("./reset_passwd/".$mailtxtname, $mailtitle."\n", FILE_APPEND);
		file_put_contents("./reset_passwd/".$mailtxtname, "From: ".$sender."\n", FILE_APPEND);
		file_put_contents("./reset_passwd/".$mailtxtname, "To: ".$receiver."\n", FILE_APPEND);
		file_put_contents("./reset_passwd/".$mailtxtname, "\n", FILE_APPEND);
		file_put_contents("./reset_passwd/".$mailtxtname, $mailcontent, FILE_APPEND);


		CLog::LOG_INFO(array(__FILE__, __LINE__, "send forget passwd mail, email = ".$email), $Seq);
		$EndReqTime = CCommon::GetMicroSecond();
        $CostTime = $EndReqTime - $BeginReqTime;

        $result['retcode'] = 0;
        $result['info'] = "send forget passwd mail, email = ".$email;
		echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "account_user_node", json_encode($result['info']), $CostTime, $result['retcode']);
		return true;

	}

	// ========================================================================================== //
	// function ==> 更改密码
	public static function change_passwd($HttpParams, $Seq)
	{   
		$BeginReqTime = CCommon::GetMicroSecond();
		$result = array();
		// 参数校验
		if(!array_key_exists("key0", $HttpParams))
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
		$pid = $HttpParams['pid'];
		$game_platform = $HttpParams['platform'];

		if(null == $email)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "email is null"), $Seq);

	    	$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	    	// todo: 参数错误的返回码
			$result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        // $result['retcode'] = -1;
	        $result['info'] = "email is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
		}

		// 查询email是否存在
		$dbClient = CAwsDb::GetInstance()->GetDbClient();
		$account_user_email_response = array();
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
		                    array(Type::STRING => $email)
		                )
		            )
		        )
		    ));
		}
		catch(DynamoDbException $e)
        {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query user data exception(email): ".$e->getMessage()), $Seq);

			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 内部数据有误的返回码
			$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	        // $result['retcode'] = -1;
	  		$result['info'] = "query user data exception(email): ".$e->getMessage();
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
        }
	    if(0 == $account_user_email_response['Count'])
	    {
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "email = ".$email." not register"), $Seq);

			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: email没有注册
			$result['retcode'] = EN_RET_CODE__EMAIL_NOT_REGISTER;
	        // $result['retcode'] = -1;
			$result['info'] = "email = ".$email." not register";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
	    }
	    if($account_user_email_response['Count'] > 1)
	    {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "multi email, account_user_email_response = ".$account_user_email_response), $Seq);

			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 内部数据有误的返回码
			$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	        // $result['retcode'] = -1;
	  		$result['info'] = "multi email";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
	    }


		// 发送外部邮件
		CLog::LOG_INFO(array(__FILE__, __LINE__, "send reset passwd mail"), $Seq);

		$reset_passwd_prefix = web_account_prefix."controller=account&action=resetpasswd&";
		$reset_passwd_url_params = "";
		$reset_passwd_url_params = $reset_passwd_url_params."key0=".urlencode($email);
		$reset_passwd_url_params = $reset_passwd_url_params."&key1=".$account_user_email_response['Items'][0]['pwd_seq']['N'];
		$reset_passwd_url_params = $reset_passwd_url_params."&key2=".$pid;
		$reset_passwd_url_params = $reset_passwd_url_params."&key3=".$game_platform;

		if(0 == en_flag)
		{
			$reset_passwd_url = $reset_passwd_prefix."request=".$reset_passwd_url_params;
		}
		else
		{
			$reset_passwd_url = $reset_passwd_prefix."request=".encrypt_url_php($reset_passwd_url_params);
		}
		CLog::LOG_INFO(array(__FILE__, __LINE__, "reset_passwd_url = ".$reset_passwd_url), $Seq);


		$account_mail_json_path = CConf::GetAccountMailJson(0);
		$account_mail_json = json_decode(file_get_contents($account_mail_json_path), true);

		$mailtitle = "Subject: ".$account_mail_json['data']['4']['title'];;
		$mailcontent_raw = $account_mail_json['data']['4']['content'];
		if(null == $pid)
		{
			$mailcontent = str_replace("STRING0", leyi_name, $mailcontent_raw, $count);
		}
		else
		{
			$r_pid = CConf::GetRealPid($pid);
			if("-1" == $r_pid)
			{
				CLog::LOG_ERROR(array(__FILE__, __LINE__, "r_pid not exist"), $Seq);
				$mailcontent = str_replace("STRING0", leyi_name, $mailcontent_raw, $count);
			}
			else
			{
				$product_project_info = CConf::GetProject($r_pid);
				$mailcontent = str_replace("STRING0", $product_project_info[1], $mailcontent_raw, $count);
			}
		}

		$mailcontent = str_replace("STRING1", $reset_passwd_url, $mailcontent, $count);
		$sender = account_mail_sender;
		$receiver = $email;
		$mailtxtname = $email.".send.".time();
		file_put_contents("./reset_passwd/".$mailtxtname, $mailtitle."\n", FILE_APPEND);
		file_put_contents("./reset_passwd/".$mailtxtname, "From: ".$sender."\n", FILE_APPEND);
		file_put_contents("./reset_passwd/".$mailtxtname, "To: ".$receiver."\n", FILE_APPEND);
		file_put_contents("./reset_passwd/".$mailtxtname, "\n", FILE_APPEND);
		file_put_contents("./reset_passwd/".$mailtxtname, $mailcontent, FILE_APPEND);


		CLog::LOG_INFO(array(__FILE__, __LINE__, "send change passwd, email = ".$email), $Seq);

		$EndReqTime = CCommon::GetMicroSecond();
        $CostTime = $EndReqTime - $BeginReqTime;
		
        $result['retcode'] = 0;
        $result['info'] = "send change passwd, email = ".$email;
		echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "account_user_node", json_encode($result['info']), $CostTime, $result['retcode']);

		return true;
	}

	// ========================================================================================== //
	// function ==> 重置密码
	public static function reset_passwd($HttpParams, $Seq)
	{   
		$BeginReqTime = CCommon::GetMicroSecond();
		$result = array();
		// 参数校验
		if(!array_key_exists("key0", $HttpParams)
			|| !array_key_exists("key1", $HttpParams)
			|| !array_key_exists("key2", $HttpParams)
			|| !array_key_exists("key3", $HttpParams)
			|| !array_key_exists("key4", $HttpParams))
		{
	    	$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	    	// todo: 参数错误的返回码
	        $result['retcode'] = -1;
	        $result['info'] = "req params not incomplete";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
		}
		$email = strtolower($HttpParams['key0']);
		$reset_passwd = $HttpParams['key1'];
		$passwd_seq = $HttpParams['key2'];
		$pid = $HttpParams['key3'];
		$game_platform = $HttpParams['key4'];

		if(null == $email)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "email is null"), $Seq);
	       	$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	    	// todo: 参数错误的返回码
	        $result['retcode'] = -1;
	        $result['info'] = "email is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
		}
		if(null == $reset_passwd)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "reset_passwd is null"), $Seq);
			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	    	// todo: 参数错误的返回码
	        $result['retcode'] = -1;
	        $result['info'] = "reset_passwd is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
		}
		if(null == $passwd_seq)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "passwd_seq is null"), $Seq);
			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	    	// todo: 参数错误的返回码
	        $result['retcode'] = -1;
	        $result['info'] = "passwd_seq is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
		}


		$account_user_response = array();
		// 查询email是否存在
		$dbClient = CAwsDb::GetInstance()->GetDbClient();
		try
		{
			$account_user_response =  $dbClient->query(array(
		        // "ConsistentRead" => true,
		        "TableName" => account_user_tbl,
		        "IndexName" => "email-index",
		        // "AttributesToGet" => array("email"),
		        "KeyConditions" => array(
		            "email" => array(
		                "ComparisonOperator" => ComparisonOperator::EQ,
		                "AttributeValueList" => array(
		                    array(Type::STRING => $email)
		                )
		            )
		        )
		    ));
		}
		catch(DynamoDbException $e)
        {
			$EndReqTime = CCommon::GetMicroSecond();
			$CostTime = $EndReqTime - $BeginReqTime;
			// todo: 内部数据有误的返回码
			$result['retcode'] = -1;
	  		$result['info'] = "query user data exception(email): ".$e->getMessage();
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
        }

	    if(0 == $account_user_response['Count'])
	    {
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "email = ".$email." not register"), $Seq);
			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: email没有注册
			$result['retcode'] = -1;
			$result['info'] = "email = ".$email." not register";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
	    }
	    if($account_user_response['Count'] > 1)
	    {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "multi email, account_user_response = ".$account_user_response), $Seq);
			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 内部数据有误的返回码
			$result['retcode'] = -1;
	  		$result['info'] = "multi email";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
	    }
	    if(0 != $account_user_response['Items'][0]['status']['N'])
	    {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "account status error, account_ status = ".$account_user_response['Items'][0]['status']['N']), $Seq);
			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 帐号受限的返回码
			$result['retcode'] = -1;
	  		$result['info'] = "account status error, account_ status = ".$account_user_response['Items'][0]['status']['N'];
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
	    }
	    if((int)$passwd_seq != $account_user_response['Items'][0]['pwd_seq']['N'])
	    {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "reset passwd seq error, req_reset_passwd_seq = ".(int)$passwd_seq.", db_reset_passwd_seq = ".$account_user_response['Items'][0]['pwd_seq']['N']), $Seq);
	   		$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 重置密码请求过期返回码
			$result['retcode'] = -1;
	  		$result['info'] = "reset passwd seq error, req_reset_passwd_seq = ".(int)$passwd_seq.", db_reset_passwd_seq = ".$account_user_response['Items'][0]['pwd_seq']['N'];
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
	    }


	    // 更新account_user表
		try
	    {
            $dbClient->updateItem(array(
            'TableName' => account_user_tbl,
            "Key" => array(
                "rid" => array(
                    Type::STRING => $account_user_response['Items'][0]['rid']['S']
                    )
                ),
            "AttributeUpdates" => array(
                "passwd" => array(
                    "Action" => AttributeAction::PUT,
                    "Value" => array(
                        Type::STRING => $reset_passwd
                        )
                    ),
           		"utime" => array(
                    "Action" => AttributeAction::PUT,
                    "Value" => array(
                        Type::NUMBER => time()
                        )
                    ),	       
                "pwd_flag" => array(
                    "Action" => AttributeAction::PUT,
                    "Value" => array(
                        Type::NUMBER => 1
                        )
                    ),
                "pwd_seq" => array(
                    "Action" => AttributeAction::ADD,
                    "Value" => array(
                        Type::NUMBER => 1
                        )
                    )
                )
            ));
        }
        catch(Exception $e)
        {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "update user data exception: ".$e->getMessage()), $Seq);
			$EndReqTime = CCommon::GetMicroSecond();
			$CostTime = $EndReqTime - $BeginReqTime;
			// todo: 内部数据有误的返回码
			$result['retcode'] = -1;
	  		$result['info'] = "update user data exception: ".$e->getMessage();
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
        }	

      

        if(null != $pid)
        {

	        $r_pid = CConf::GetRealPid($pid);
	        if("-1" == $r_pid)
	        {
				CLog::LOG_ERROR(array(__FILE__, __LINE__, "r_pid not exist"), $Seq);

		        $EndReqTime = CCommon::GetMicroSecond();
		        $CostTime = $EndReqTime - $BeginReqTime;
		        // todo: 参数错误的返回码
		        $result['retcode'] = -1;
		        $result['info'] = "r_pid not exist";
				echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
		        return false;
	        }

        	
			$account_product_response = array();
			// 查询rid的是否有product数据
			try
			{
				$account_product_response =  $dbClient->query(array(
			        // "ConsistentRead" => true,
			        "TableName" => account_product_tbl,
			        "IndexName" => "glb_rid",
			        // "AttributesToGet" => array("email"),
			        "KeyConditions" => array(
			            "rid" => array(
			                "ComparisonOperator" => ComparisonOperator::EQ,
			                "AttributeValueList" => array(
			                    array(Type::STRING => $account_user_response['Items'][0]['rid']['S'])
			                )
			            )
			        )
			    ));
			}
			catch(DynamoDbException $e)
	        {
				CLog::LOG_ERROR(array(__FILE__, __LINE__, "query product data exception(rid): ".$e->getMessage()), $Seq);
				$EndReqTime = CCommon::GetMicroSecond();
				$CostTime = $EndReqTime - $BeginReqTime;
				// todo: 内部数据有误的返回码
				$result['retcode'] = -1;
		  		$result['info'] = "query product data exception(rid)";
				echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
				return false;
	        }

			if(0 < $account_product_response['Count'])
		    {
		    	$product_response_info = array();
			    $product_exist_count = 0;
		 		foreach($account_product_response['Items'] as $product_response)
		 		{
		            if($product_response['r_pid']['S'] != $r_pid)
		            {
		               	continue;         	
		            }	
			    	$product_exist_count++;
			    	$product_response_info = $product_response;
		        }
		      	if($product_exist_count > 1)
		        {
					CLog::LOG_ERROR(array(__FILE__, __LINE__, "one rid banding same product exist more, product_exist_count = ".$product_exist_count), $Seq);
					$EndReqTime = CCommon::GetMicroSecond();
					$CostTime = $EndReqTime - $BeginReqTime;
					// todo: 内部数据有误的返回码
					$result['retcode'] = -1;
			  		$result['info'] = "one rid banding same product exist more, product_exist_count = ".$product_exist_count;
					echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
					return false;

		        }
		        if(1 == $product_exist_count)
		        {
					// 发游戏内部邮件(修改密码成功)
					CLog::LOG_INFO(array(__FILE__, __LINE__, "send reset password in game"), $Seq);
					$product_project_info = CConf::GetProject($r_pid);
					$game_mail_url_params = "";
					$game_mail_url_params = "ope=sendmail&version=1&did=System&lg=1&uid=225&sid=0&command=operate_mail_send";
					$game_mail_url_params = $game_mail_url_params."&key0=2";
					$game_mail_url_params = $game_mail_url_params."&key1=".$product_response_info['app_uid']['S'];
					$game_mail_url_params = $game_mail_url_params."&key2=System";
					$game_mail_url_params = $game_mail_url_params."&key3=33";
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
				CLog::LOG_ERROR(array(__FILE__, __LINE__, "not find product data"), $Seq);
		    }
        }




		CLog::LOG_INFO(array(__FILE__, __LINE__, "reset passwd, email = ".$email), $Seq);
		
		$EndReqTime = CCommon::GetMicroSecond();
        $CostTime = $EndReqTime - $BeginReqTime;
 		$result['retcode'] = 0;
        $result['info'] = "reset passwd, email = ".$email;
		echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "account_user_node", json_encode($result['info']), $CostTime, $result['retcode']);
		return true;

	}

}



?>