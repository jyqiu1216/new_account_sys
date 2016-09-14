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
Class CLoginLogic
{
	// ========================================================================================== //
	// command = register
	// key0 = ${email}
	// key1 = ${passwd}
	// key2 = ${th_id}
	// key3 = ${login_platform}
	// key4 = ${pid}
	// key5 = ${game_platform}
	public static function login($HttpParams, $Seq)
	{   
		$BeginReqTime = CCommon::GetMicroSecond();
		$result = array();
		
		// 参数校验
		if(!array_key_exists("key0", $HttpParams)
			|| !array_key_exists("key1", $HttpParams)
			|| !array_key_exists("key2", $HttpParams)
			|| !array_key_exists("key3", $HttpParams))
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
		$login_platform = $HttpParams['key3'];
		$pid = $HttpParams['pid'];
		$game_platform = $HttpParams['platform'];
		$r_pid = $HttpParams['r_pid'];

		if(null == $login_platform)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "login_platform is null"), $Seq);

	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        // todo: 参数错误的返回码
			$result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        // $result['retcode'] = -1;
	        $result['info'] = "login_platform is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}

		$login_flag = false;
		if(null != $email 				// email登陆
			&& null != $passwd
			&& null == $th_id)
		{
			CLog::LOG_INFO(array(__FILE__, __LINE__, "email login, email = ".$email), $Seq);
			$login_info = array();
			$login_info['email'] = $email;
	    	$login_info['passwd'] = $passwd;
	    	$login_info['login_platform'] = $login_platform;
			$login_info['pid'] = $pid;
			$login_info['game_platform'] = $game_platform;
			$login_info['r_pid'] = $r_pid;
			$ret = CLoginLogic::login_email($login_info, $result, $Seq);
			if(true == $ret)
			{
				$login_flag = true;
				CLog::LOG_INFO(array(__FILE__, __LINE__, "email login sucess, email = ".$email), $Seq);		
			}
		}
		if(null == $email  		// 第三方登陆
			&& null == $passwd
			&& null != $th_id)
		{
			CLog::LOG_INFO(array(__FILE__, __LINE__, "th_id login, th_id = ".$th_id), $Seq);
			$login_info = array();
	    	$login_info['th_id'] = $th_id;
	    	$login_info['login_platform'] = $login_platform;
			$login_info['pid'] = $pid;
			$login_info['game_platform'] = $game_platform;
			$login_info['r_pid'] = $r_pid;
			$ret = CLoginLogic::login_th_id($login_info, $result, $Seq);
			if(true == $ret)
			{
				$login_flag = true;
				CLog::LOG_INFO(array(__FILE__, __LINE__, "th_id login sucess, th_id = ".$th_id), $Seq);		
			}
		}

		if(true == $login_flag)
		{
			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        if(null == $th_id)
	        {	
	        	echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "account_user_node", json_encode($result['info']), $CostTime, $result['retcode']);
	        	return true;
	        }
			else
			{ 	
	        	echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "account_user_node", json_encode($result['info']), $CostTime, $result['retcode']);
	        	return true;
			}
		}
		else
		{
			CLog::LOG_INFO(array(__FILE__, __LINE__, "login failed"), $Seq);
	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
		}

	}


	// ========================================================================================== //
	// function ==> 邮箱登录
	private static function login_email($login_info, &$result, $Seq)
	{

		if(null == $login_info['email'])
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "email is null"), $Seq);
			// todo: 参数错误的返回码
			$result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        // $result['retcode'] = -1;
	  		$result['info'] = "email is null";
	        return false;
		}
		if(null == $login_info['passwd'])
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "passwd is null"), $Seq);
			// todo: 参数错误的返回码
			$result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        // $result['retcode'] = -1;
	  		$result['info'] = "passwd is null";
	        return false;
		}

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
		                    array(Type::STRING => $login_info['email'])
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

	    if(0 == $account_user_email_response['Count'])
	    {
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "email = ".$login_info['email']." not register"), $Seq);
	    	// todo: email没有注册的返回码
			$result['retcode'] = EN_RET_CODE__EMAIL_NOT_REGISTER;
	        // $result['retcode'] = -1;
	  		$result['info'] = "email = ".$login_info['email']." not register";
	        return false;
	    }
	    if($account_user_email_response['Count'] > 1)
	    {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "multi email, account_user_email_response = ".$account_user_email_response), $Seq);
    		// todo: 内部数据有误的返回码
			$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	        // $result['retcode'] = -1;
	  		$result['info'] = "multi email";
	        return false;
	    }
		if($login_info['passwd'] != $account_user_email_response['Items'][0]['passwd']['S'])
	    {
    		CLog::LOG_ERROR(array(__FILE__, __LINE__, "email passwd wrong, account_user_email_response = ".$account_user_email_response), $Seq);
    		// todo: email的密码不一致的返回码
			$result['retcode'] = EN_RET_CODE__PASSWD_ERROR;
	        // $result['retcode'] = -1;
	  		$result['info'] = "email passwd wrong";
	        return false;
	    } 
	    if(1 == $account_user_email_response['Items'][0]['status']['N'])
	    {
    		CLog::LOG_ERROR(array(__FILE__, __LINE__, "account restricted, account_user_email_response = ".$account_user_email_response), $Seq);
    		// todo: email帐号受限的返回码
			$result['retcode'] = EN_RET_CODE__INVAILD_LOGIN;
	        // $result['retcode'] = -1;
	  		$result['info'] = "account restricted";
	        return false;
	    }


	    if(null != $login_info['pid'])
	    {
			if(null == $login_info['game_platform'])
			{
				CLog::LOG_ERROR(array(__FILE__, __LINE__, "game_platform is null"), $Seq);
				// todo: 参数错误的返回码
				$result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
		        // $result['retcode'] = -1;
		  		$result['info'] = "game_platform is null";
		        return false;
			}
			if(null == $login_info['r_pid'])
			{
				CLog::LOG_ERROR(array(__FILE__, __LINE__, "r_pid is null"), $Seq);
				// todo: 参数错误的返回码
				$result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
		        // $result['retcode'] = -1;
		  		$result['info'] = "r_pid is null";
		        return false;
			}

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
			                    array(Type::STRING => $account_user_email_response['Items'][0]['rid']['S'])
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
		  		$result['info'] = "query product data exception(email): ".$e->getMessage();
		        return false;
	        }
			if(0 == $account_product_response['Count'])
			{
		    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "rid = ".$account_product_response['Items'][0]['rid']['S']." not have product data"), $Seq);
		    	// todo: email没有任何产品数据的返回码
				$result['retcode'] = EN_RET_CODE__EMAIL_NOT_BINDING_ANYDATA;
		        // $result['retcode'] = -1;
		  		$result['info'] = "rid = ".$account_product_response['Items'][0]['rid']['S']." not have product data";
		        return false;
		    }


			$product_exist_count = 0;
	 		foreach($account_product_response['Items'] as $product_response)
	 		{
	            if($product_response['r_pid']['S'] != $login_info['r_pid'])
	            {
	               	continue;         	
	            }
		    	$product_exist_count++;
	        }
		    if(0 == $product_exist_count)
		    {
		    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "email = ". $login_info['email']." not have this product data"), $Seq);
		    	// todo: email没有当前产品数据的返回码
				$result['retcode'] = EN_RET_CODE__EMAIL_NOT_BINDING_CURDATA;
		        // $result['retcode'] = -1;
		  		$result['info'] = "email = ". $login_info['email']." not have this product data";
		        return false;
		    }

	    }

		$account_user_update_response = array();
		// 更新account_user表(登录帐号)
		try
	    {
            $account_user_update_response = $dbClient->updateItem(array(
            'TableName' => account_user_tbl,
            "Key" => array(
                "rid" => array(
                    Type::STRING => $account_user_email_response['Items'][0]['rid']['S']
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
                        Type::STRING => $login_info['login_platform']
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
	      	$result['retcode'] = 0;
	  		$result['info']['rid'] = $account_user_email_response['Items'][0]['rid']['S'];
	        return true;
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
	}

	// ========================================================================================== //
	// function ==> 第三方登录激活
	private static function login_th_id($login_info, &$result, $Seq)
	{	
		// 检验第三方登录的参数正确性
		if(null == $login_info['th_id'])
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "th_id is null"), $Seq);
	        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	  		$result['info'] = "th_id is null";
	        return false;
		}

		$th_id_arry = explode(':', $login_info['th_id']);
		if(null == $th_id_arry[0]
			|| null == $th_id_arry[1])
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "th_id is invaild, th_id = ".$login_info['th_id']), $Seq);
	        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	  		$result['info'] = "th_id is invaild, th_id = ".$login_info['th_id'];
	        return false;
		}
		$th_id_name = $th_id_arry[0];
		$th_id_id = $th_id_arry[1];

		$dbClient = CAwsDb::GetInstance()->GetDbClient();
		$account_user_th_id_response = array();

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
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "th_id = ".$login_info['th_id']." not register"), $Seq);
	        $result['retcode'] = EN_RET_CODE__THIRD_NOT_REGISTER;
	        $result['info'] = "th_id = ".$login_info['th_id']." not register";
	        return false;
	    }
	    if($account_user_th_id_response['Count'] > 1)
	    {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "multi email, account_user_th_id_response = ".$account_user_th_id_response), $Seq);
	        $result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	        $result['info'] = "multi th_id";
	        return false;
	    }
	    if($th_id_id != $account_user_th_id_response['Items'][0][$th_id_name]['S'])
	    {
    		CLog::LOG_ERROR(array(__FILE__, __LINE__, "th_id is not the rid th_id, account_user_th_id_response = ".$account_user_th_id_response), $Seq);
	  		$result['retcode'] = EN_RET_CODE__THIRD_STRING_ERROR;
	  		$result['info'] = "th_id is not the rid th_id";
	        return true;
	    }
	    if(1 == $account_user_th_id_response['Items'][0]['status']['N'])
	    {
    		CLog::LOG_ERROR(array(__FILE__, __LINE__, "account restricted, account_user_th_id_response = ".$account_user_th_id_response), $Seq);
			$result['retcode'] = EN_RET_CODE__INVAILD_LOGIN;
	  		$result['info'] = "account restricted";
	        return false;
	    }


	    if(null != $login_info['pid'])
	    {
			if(null == $login_info['game_platform'])
			{
				CLog::LOG_ERROR(array(__FILE__, __LINE__, "game_platform is null"), $Seq);
	        	// todo: 参数错误的返回码
		        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
		        $result['info'] = "game_platform is null";
		        return false;
			}

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
			                    array(Type::STRING => $account_user_th_id_response['Items'][0]['rid']['S'])
			                )
			            )
			        )
			    ));
			}
			catch(DynamoDbException $e)
	        {
				CLog::LOG_ERROR(array(__FILE__, __LINE__, "query product data exception(rid): ".$e->getMessage()), $Seq);
		        $result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
		        $result['info'] = "query product data exception(rid)";
		        return false;
	        }
			if(0 == $account_product_response['Count'])
			{
		    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "rid = ".$account_product_response['Items'][0]['rid']['S']." not have product data"), $Seq);
		        $result['retcode'] = EN_RET_CODE__THIRD_NOT_BINDING_ANYDATA;
		        $result['info'] = "rid = ".$account_product_response['Items'][0]['rid']['S']." not have product data";
		        return false;
		    }


			$product_exist_count = 0;
	 		foreach($account_product_response['Items'] as $product_response)
	 		{
	            if($product_response['r_pid']['S'] != $login_info['r_pid'])
	            {
	               	continue;         	
	            }
		    	$product_exist_count++;
	        }
		    if(0 == $product_exist_count)
		    {
		    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "th_id = ". $login_info['th_id']." not have this product data"), $Seq);
		        $result['retcode'] = EN_RET_CODE__THIRD_NOT_BINDING_CURDATA;
		        $result['info'] = "th_id = ". $login_info['th_id']." not have this product data";
		        return false;
		    }

	    }

		$account_user_update_response = array();
		// 更新account_user表(登录帐号)
		try
	    {
            $account_user_update_response = $dbClient->updateItem(array(
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
                        Type::STRING => $login_info['login_platform']
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
	        $result['retcode'] = 0;
	        $result['info']['rid'] = $account_user_th_id_response['Items'][0]['rid']['S'];
			return true;
        }
        catch(Exception $e)
        {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "update user data exception: ".$e->getMessage()), $Seq);
	        $result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	        $result['info'] = "update user data exception: ".$e->getMessage();
			return false;
        }	
	}

}



?>