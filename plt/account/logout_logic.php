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
Class CLogoutLogic
{
	// ========================================================================================== //
	// command = logout
	// key0 = ${rid}
	// key1 = ${email}
	// key2 = ${passwd}
	// key3 = ${th_id}
	public static function logout($HttpParams, $Seq)
	{   
		$BeginReqTime = CCommon::GetMicroSecond();
		$RetCode = 0;
		// 参数校验
		/*
		if(!array_key_exists("key0", $HttpParams)
			|| !array_key_exists("key1", $HttpParams)
			|| !array_key_exists("key2", $HttpParams)
			|| !array_key_exists("key3", $HttpParams))
	    {
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "params not incomplete"), $Seq);
	   		CCommon::consloe_out("params not incomplete");
	        return -1;
		}
		$rid = $HttpParams['key0'];
		$email = $HttpParams['key1'];
		$passwd = $HttpParams['key2'];
		$th_id = $HttpParams['key3'];	// 格式(fd_id:facebook_snow)
		*/
		if(!array_key_exists("key0", $HttpParams)
			|| !array_key_exists("key1", $HttpParams)
			|| !array_key_exists("key2", $HttpParams)
			|| !array_key_exists("key3", $HttpParams))
	    {
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "params not incomplete"), $Seq);
	   		CCommon::consloe_out("params not incomplete");

	        // todo: 参数错误的返回码
	        $RetCode = -1;
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode("params not incomplete"), $CostTime, $RetCode);
	        return false;
		}
		$rid = $HttpParams['key0'];
		$email = strtolower($HttpParams['key1']);
		$passwd = $HttpParams['key2'];
		$th_id = $HttpParams['key3'];	// 格式(fd_id:facebook_snow)

		if(null == $rid)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "rid is null"), $Seq);
			CCommon::consloe_out("rid is null");

	        // todo: 参数错误的返回码
	        $RetCode = -1;
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode("rid is null"), $CostTime, $RetCode);
	        return false;
		}

		$logout_flag = false;
		if(null != $email 				// email注销
			&& null != $passwd
			&& null == $th_id)
		{
			CLog::LOG_INFO(array(__FILE__, __LINE__, "email logout, email = ".$email), $Seq);
			$logout_info = array();
	    	$logout_info['rid'] = $rid;
			$logout_info['email'] = $email;
	    	$logout_info['passwd'] = $passwd;
			$ret = CLogoutLogic::logout_email($logout_info, $RetCode, $Seq);
			if(true == $ret)
			{
				$logout_flag = true;
				CLog::LOG_INFO(array(__FILE__, __LINE__, "email logout sucess, email = ".$email), $Seq);			
			}

		}
		else if(null == $email  		// 第三方注销
			&& null == $passwd
			&& null != $th_id)
		{
			CLog::LOG_INFO(array(__FILE__, __LINE__, "th_id logout, th_id = ".$th_id), $Seq);
			$logout_info = array();
	    	$logout_info['rid'] = $rid;
	    	$logout_info['th_id'] = $th_id;
			$ret = CLogoutLogic::logout_th_id($logout_info, $RetCode, $Seq);
			if(true == $ret)
			{
				$logout_flag = true;
				CLog::LOG_INFO(array(__FILE__, __LINE__, "th_id logout sucess, th_id = ".$th_id), $Seq);			
			}
		}

		if(true == $logout_flag)
		{
			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "account_user_node", json_encode("logout successs"), $CostTime, $RetCode);
			return true;
		}
		else
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "invail logout"), $Seq);
			CCommon::consloe_out("invail logout");

	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode("invail logout"), $CostTime, $RetCode);
			return false;
		}

	}

	// ========================================================================================== //
	// function ==> 邮箱登录
	private static function logout_email($logout_info, $RetCode, $Seq)
	{

		$dbClient = CAwsDb::GetInstance()->GetDbClient();
		$account_user_response = array();

		// 查询rid是否存在
		try
		{
			$account_user_response =  $dbClient->query(array(
		        // "ConsistentRead" => true,
		        "TableName" => account_user_tbl,
		        // "AttributesToGet" => array("email"),
		        "KeyConditions" => array(
		            "rid" => array(
		                "ComparisonOperator" => ComparisonOperator::EQ,
		                "AttributeValueList" => array(
		                    array(Type::STRING => $logout_info['rid'])
		                )
		            )
		        )
		    ));
		}
		catch(DynamoDbException $e)
        {
        	CCommon::consloe_out("query user data exception(email)");
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query user data exception(email): ".$e->getMessage()), $Seq);
			// todo: 内部数据有误的返回码
	  		$RetCode = -1;
	        return false;
        }

	    if(0 == $account_user_response['Count'])
	    {
	    	CCommon::consloe_out("email = ".$active_info['email']." not register");
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "email = ".$active_info['email']." not register"), $Seq);
			// todo: 找不到该帐号的返回码
	  		$RetCode = -1;
	        return false;
	    }
	    if($account_user_response['Count'] > 1)
	    {
	    	CCommon::consloe_out("multi email, account_user_response = ".$account_user_response);
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "multi email, account_user_response = ".$account_user_response), $Seq);
			// todo: 内部数据有误的返回码
	  		$RetCode = -1;
	        return false;
	    }
		if($logout_info['email'] != $account_user_response['Items'][0]['email']['S'])
	    {
    		CCommon::consloe_out("email wrong, account_user_response = ".$account_user_response);
    		CLog::LOG_ERROR(array(__FILE__, __LINE__, "email wrong, account_user_response = ".$account_user_response), $Seq);
  			// todo: 注销的email不一致的返回码
	  		$RetCode = -1;
	        return false;
	    } 
		if($logout_info['passwd'] != $account_user_response['Items'][0]['passwd']['S'])
	    {
    		CCommon::consloe_out("email passwd wrong, account_user_response = ".$account_user_response);
    		CLog::LOG_ERROR(array(__FILE__, __LINE__, "email passwd wrong, account_user_response = ".$account_user_response), $Seq);
			// todo: 验证密码失败的返回码
	  		$RetCode = -1;
	        return false;
	    } 
		

		// 更新account_user表(登录帐号)
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
           		"utime" => array(
                    "Action" => AttributeAction::PUT,
                    "Value" => array(
                        Type::NUMBER => time()
                        )
                    ),	       
           		"logstatus" => array(
                    "Action" => AttributeAction::PUT,
                    "Value" => array(
                        Type::NUMBER => 1
                        )
                    )
                )
            ));
        }
        catch(Exception $e)
        {
        	CCommon::consloe_out("update user data exception: ".$e->getMessage());
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "update user data exception: ".$e->getMessage()), $Seq);
			// todo: 内部数据有误的返回码
	  		$RetCode = -1;
	        return false;
        }	

        return true;
	}

	// ========================================================================================== //
	// function ==> 第三方登录激活
	private static function logout_th_id($logout_info, $RetCode, $Seq)
	{	
		// 检验第三方登录的参数正确性
		if(null == $logout_info['th_id'])
		{
			CCommon::consloe_out("th_id is null");
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "th_id is null"), $Seq);
			// todo: 参数错误的返回码
	  		$RetCode = -1;
	        return false;
		}

		$th_id_arry = explode(':', $logout_info['th_id']);
		if(null == $th_id_arry[0]
			|| null == $th_id_arry[1])
		{
			CCommon::consloe_out("th_id is invaild, th_id = ".$logout_info['th_id']);
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "th_id is invaild, th_id = ".$logout_info['th_id']), $Seq);
			// todo: 参数错误的返回码
	  		$RetCode = -1;
	        return false;
		}
		$th_id_name = $th_id_arry[0];
		$th_id_id = $th_id_arry[1];


		$dbClient = CAwsDb::GetInstance()->GetDbClient();
		$account_user_response = array();
		$account_product_response = array();
		// 查询rid是否存在
		try
		{
			$account_user_response =  $dbClient->query(array(
		        // "ConsistentRead" => true,
		        "TableName" => account_user_tbl,
		        // "AttributesToGet" => array("email"),
		        "KeyConditions" => array(
		            "rid" => array(
		                "ComparisonOperator" => ComparisonOperator::EQ,
		                "AttributeValueList" => array(
		                    array(Type::STRING => $logout_info['rid'])
		                )
		            )
		        )
		    ));
		}
		catch(DynamoDbException $e)
        {
        	CCommon::consloe_out("query user data exception: ".$e->getMessage());
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query user data exception: ".$e->getMessage()), $Seq);
			// todo: 内部数据有误的返回码
	  		$RetCode = -1;
	        return false;
        }
		if(0 == $response['Count'])
	    {
	    	CCommon::consloe_out("th_id_name = ".$th_id_name." not register");
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "th_id_name = ".$th_id_name." not register"), $Seq);
			// todo: 找不到该帐号的返回码
	  		$RetCode = -1;
	        return false;
	    }
	    if($response['Count'] > 1)
	    {
	    	CCommon::consloe_out("multi th_id_name, response = ".$response);
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "multi th_id_name, response = ".$response), $Seq);
			// todo: 内部数据有误的返回码
	  		$RetCode = -1;
	        return false;
	    }
		if($th_id_id != $account_user_response['Items'][0][$th_id_name]['S'])
	    {
    		CCommon::consloe_out("th_id wrong, account_user_response = ".$account_user_response);
    		CLog::LOG_ERROR(array(__FILE__, __LINE__, "email wrong, account_user_response = ".$account_user_response), $Seq);
  			// todo: 注销的th_id不一致的返回码
	  		$RetCode = -1;
	        return false;
	    } 
		
		// 更新account_user表(登录帐号)
		try
	    {
            $dbClient->updateItem(array(
            'TableName' => account_user_tbl,
            "Key" => array(
                "rid" => array(
                    Type::STRING => $account_user_response['Items'][0]['rid']['0']
                    )
                ),
            "AttributeUpdates" => array(
           		"utime" => array(
                    "Action" => AttributeAction::PUT,
                    "Value" => array(
                        Type::NUMBER => time()
                        )
                    ),	       
           		"logstatus" => array(
                    "Action" => AttributeAction::PUT,
                    "Value" => array(
                        Type::NUMBER => 1
                        )
                    )
                )
            ));
        }
        catch(Exception $e)
        {
        	CCommon::consloe_out("update user data exception: ".$e->getMessage());
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "update user data exception: ".$e->getMessage()), $Seq);
			// todo: 内部数据有误的返回码
	  		$RetCode = -1;
	        return false;
        }	

        return true;
	}

}



?>