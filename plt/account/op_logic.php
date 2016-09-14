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
Class COpLogic
{
	// ========================================================================================== //
	// command = change_sid
	// key0 = ${sid}
	public static function change_sid($HttpParams, $Seq)
	{
		$BeginReqTime = CCommon::GetMicroSecond();
		$result = array();

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


		// 参数校验
		$uid = $HttpParams['uid'];
		$r_pid = $HttpParams['r_pid'];
		$sid = $HttpParams['key0'];
	

		if(null == $uid)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "uid is null"), $Seq);

	 		$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        // todo: 参数错误的返回码
	        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        // $result['retcode'] = -1;
	        $result['info'] = "uid is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}
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
		if(null == $sid)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "sid is null"), $Seq);

	 		$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        // todo: 参数错误的返回码
	        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        // $result['retcode'] = -1;
	        $result['info'] = "sid is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}


		$dbClient = CAwsDb::GetInstance()->GetDbClient();
		$account_product_response = array();

		try
		{

			$dbClient->updateItem(array(
	            'TableName' => account_product_tbl,
	            "Key" => array(
	                "app_uid" => array(
	                    Type::STRING => $uid
	                    ),
	                "r_pid" => array(
	                    Type::STRING => $r_pid
	                    )
	                ),
	            "AttributeUpdates" => array(
	           		"sid" => array(
	                    "Action" => AttributeAction::PUT,
	                    "Value" => array(
	                        Type::NUMBER => intval($sid)
	                        )
	                    )
	                )
	            ));


		}
		catch(DynamoDbException $e)
		{			
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "update sid exception(email): ".$e->getMessage()), $Seq);

			$EndReqTime = CCommon::GetMicroSecond();
    		$CostTime = $EndReqTime - $BeginReqTime;
			// todo: 内部数据有误的返回码
	        $result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	        // $result['retcode'] = -1;
	  		$result['info'] = "update sid exception(email): ".$e->getMessage();
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}


		CLog::LOG_INFO(array(__FILE__, __LINE__, "chaneg sid success"), $Seq);
        $EndReqTime = CCommon::GetMicroSecond();
        $CostTime = $EndReqTime - $BeginReqTime;
		$result['retcode'] = 0;
		$result['info'] = "chaneg sid success";
		echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "account_user_node", json_encode($result['info']), $CostTime, $result['retcode']);
		return true;

	}




	// ========================================================================================== //
	// command = clear_account
	// key0 = ${app_uid}
	// key1 = ${pid}
	// key2 = ${game_platform}
	public static function clear_account($HttpParams, $Seq)
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
	        $result['retcode'] = -1;
	        $result['info'] = "params not incomplete";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}
		$app_uid = $HttpParams['key0'];
		$pid = $HttpParams['key1'];
		$game_platform = $HttpParams['key2'];

		$online_clear_flag = false;
		if(array_key_exists("key3", $HttpParams))
		{
			if("1" == $HttpParams['key3'])
			{
				$online_clear_flag = true;	
			}
		}

		if(null == $app_uid)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "app_uid is null"), $Seq);

	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        // todo: 参数错误的返回码
	        $result['retcode'] = -1;
	        $result['info'] = "app_uid is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}
		if(null == $pid)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "pid is null"), $Seq);

	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        // todo: 参数错误的返回码
	        $result['retcode'] = -1;
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
	        $result['retcode'] = -1;
	        $result['info'] = "game_platform is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}

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



		$dbClient = CAwsDb::GetInstance()->GetDbClient();

		// 线上清用户
		if(true == $online_clear_flag)
		{
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
	                            array(Type::STRING => $app_uid)
	                        )
	                    ),
			            "r_pid" => array(
			                "ComparisonOperator" => ComparisonOperator::EQ,
			                "AttributeValueList" => array(
			                    array(Type::STRING => $r_pid)
			                )
			            )
			        )
			    ));
			}
			catch(DynamoDbException $e)
	        {
				CLog::LOG_ERROR(array(__FILE__, __LINE__, "query product data exception (app_uid + r_pid): ".$e->getMessage()), $Seq);

				$EndReqTime = CCommon::GetMicroSecond();
		        $CostTime = $EndReqTime - $BeginReqTime;
		  		$result['retcode'] = -1;
		  		$result['info'] = "query product data exception (app_uid + r_pid)";
				echo CProtocol::ReturnJsonData("account_user_data", "error", "op_account_status", json_encode($result['info']), $CostTime, $result['retcode']);
				return false;
	        }

	    	// 当前产品数据已被绑定
	    	if(1 == $account_product_response['Items'][0]['status']['N']) 		
	    	{
				$EndReqTime = CCommon::GetMicroSecond();
		        $CostTime = $EndReqTime - $BeginReqTime;
		  		$result['retcode'] = 30001;
		  		$result['info'] = "query product data exception (app_uid + r_pid)";
				echo CProtocol::ReturnJsonData("account_user_data", "error", "op_account_status", json_encode($result['info']), $CostTime, $result['retcode']);
				return false;
	    	}
		}

		// 删除原有记录
		try
		{
			/*
			$dbClient->deleteItem(array(
                    'TableName' => account_product_tbl,
                    'Key' => array(
                        'app_uid'  => array('S' => $app_uid),
                        'r_pid' => array('S' =>$r_pid)
                        )
                    ));
            */
 			$dbClient->updateItem(array(
	            'TableName' => account_product_tbl,
	            "Key" => array(
	                "app_uid" => array(
	                    Type::STRING => $app_uid
	                    ),
	                "r_pid" => array(
	                    Type::STRING => $r_pid
	                    )
	                ),
	            "AttributeUpdates" => array(
	           		"rid" => array(
	                    "Action" => AttributeAction::PUT,
	                    "Value" => array(
	                        Type::STRING => "0"
	                        )
	                    ),	 
	             	"device" => array(
	                    "Action" => AttributeAction::PUT,
	                    "Value" => array(
	                        Type::STRING => "0"
	                        )
	                    ),	 
	            	"btime" => array(
	                    "Action" => AttributeAction::PUT,
	                    "Value" => array(
	                        Type::NUMBER => 0
	                        )
	                    ),	      
	           		"status" => array(
	                    "Action" => AttributeAction::PUT,
	                    "Value" => array(
	                        Type::NUMBER => 0
	                        )
	                    )
	                )
	            ));


			CLog::LOG_INFO(array(__FILE__, __LINE__, "delete product data success"), $Seq);

	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 内部数据有误的返回码
	  		$result['retcode'] = 0;
	  		$result['info'] = "delete product data success";
			echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "account_user_node", json_encode($result['info']), $CostTime, $result['retcode']);
			return true;
		}
		catch(DynamoDbException $e)
		{			
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "delete product data exception: ".$e->getMessage()), $Seq);
			
	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 内部数据有误的返回码
	  		$result['retcode'] = -1;
	  		$result['info'] = "delete product data exception";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
		}
	}


	// ========================================================================================== //
	// command = check_account_status
	public static function check_account_status($HttpParams, $Seq)
	{   
		$BeginReqTime = CCommon::GetMicroSecond();
		$result = array();

		// 参数校验
		$rid = $HttpParams['rid'];
		$app_uid = $HttpParams['uid'];
		$pid = $HttpParams['pid'];
		$game_platform = $HttpParams['platform'];
		$device = $HttpParams['did'];
		$r_pid = $HttpParams['r_pid'];

		if(null == $r_pid)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "r_pid is null"), $Seq);

	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        $result['retcode'] = -1;
	        $result['info'] = "r_pid is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_account_status", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}

		if(null == $app_uid)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "app_uid is null"), $Seq);

	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        $result['retcode'] = -1;
	        $result['info'] = "app_uid is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_account_status", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}

		if(null == $pid)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "pid is null"), $Seq);

	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        $result['retcode'] = -1;
	        $result['info'] = "pid is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_account_status", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}

		if(null == $game_platform)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "game_platform is null"), $Seq);

	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        $result['retcode'] = -1;
	        $result['info'] = "game_platform is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_account_status", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}

		if(null == $device)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "device is null"), $Seq);

	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        $result['retcode'] = -1;
	        $result['info'] = "device is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_account_status", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}


		$dbClient = CAwsDb::GetInstance()->GetDbClient();
		$account_user_response =  array();
		$account_product_response =  array();

		// 多设备登录检测
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
                            array(Type::STRING => $app_uid)
                        )
                    ),
		            "r_pid" => array(
		                "ComparisonOperator" => ComparisonOperator::EQ,
		                "AttributeValueList" => array(
		                    array(Type::STRING => $r_pid)
		                )
		            )
		        )
		    ));
		    if(0 == $account_product_response['Count'])
		    {
		    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "app_uid = ".$app_uid." ,r_pid = ".$r_pid." not find product data"), $Seq);
				$EndReqTime = CCommon::GetMicroSecond();
				$CostTime = $EndReqTime - $BeginReqTime;
				// 该帐号没有当前游戏数据
		  		$result['retcode'] = -1;
		  		$result['info'] = "app_uid = ".$app_uid." ,r_pid = ".$r_pid." not find product data";
		  		echo CProtocol::ReturnJsonData("account_user_data", "error", "op_account_status", json_encode($result['info']), $CostTime, $result['retcode']);
		        return false;
		    }

		    $product_info = json_decode($account_product_response['Items'][0]['product_info']['S'], true);
			if($device != $product_info['device']) 		
	    	{
		    	CLog::LOG_INFO(array(__FILE__, __LINE__, "not same device login, req_device = ".$device.", now_device = ".$product_info['device']), $Seq);

		        $EndReqTime = CCommon::GetMicroSecond();
		        $CostTime = $EndReqTime - $BeginReqTime;
		  		$result['retcode'] = 0;
		  		// 4. 多设备登录
		  		$result['info']['status'] = 4;
		  		$result['info']['msg'] = "not same device login, req_device = ".$device.", now_device = ".$account_product_response['Items'][0]['device']['S'];
		  		echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "op_account_status", json_encode($result['info']), $CostTime, $result['retcode']);
		        return true;
			}
		}
		catch(DynamoDbException $e)
        {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query product data exception (app_uid + r_pid): ".$e->getMessage()), $Seq);

			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	  		$result['retcode'] = -1;
	  		$result['info'] = "query product data exception (app_uid + r_pid)";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_account_status", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
        }


		if(null == $rid)
		{
			CLog::LOG_INFO(array(__FILE__, __LINE__, "rid is null"), $Seq);

 			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	  		$result['retcode'] = 0;
	  		// 0: 未注册
	  		$result['info']['status'] = 0;
	  		$result['info']['msg'] = "rid is null";
	  		echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "op_account_status", json_encode($result['info']), $CostTime, $result['retcode']);
	        return true;
		}


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
		                    array(Type::STRING => $rid)
		                )
		            )
		        )
		    ));

		    if(0 == $account_user_response['Count'])
		    {
		    	CLog::LOG_INFO(array(__FILE__, __LINE__, "rid = ".$rid." not find"), $Seq);

		        $EndReqTime = CCommon::GetMicroSecond();
		        $CostTime = $EndReqTime - $BeginReqTime;
		  		$result['retcode'] = 0;
		  		// 1: 未激活
		  		$result['info']['status'] = 1;
		  		$result['info']['msg'] = "rid = ".$rid." not find";
		  		echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "op_account_status", json_encode($result['info']), $CostTime, $result['retcode']);
		        return true;
		    }
		    else
		    {
		    	CLog::LOG_INFO(array(__FILE__, __LINE__, "rid = ".$rid." find"), $Seq);


		    	if(1 == $account_user_response['Items'][0]['pwd_flag']['N'])
		    	{
		    		$EndReqTime = CCommon::GetMicroSecond();
			        $CostTime = $EndReqTime - $BeginReqTime;
			  		$result['retcode'] = 0;
			  		// 3. 帐号已修改
			  		$result['info']['status'] = 3;
			  		$result['info']['msg'] = "rid = ".$rid." passwd change";
			  		echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "op_account_status", json_encode($result['info']), $CostTime, $result['retcode']);
			        return true;
		    	}
		    	else
		    	{
		    		$EndReqTime = CCommon::GetMicroSecond();
			        $CostTime = $EndReqTime - $BeginReqTime;
					// todo: rid帐号已激活
			  		$result['retcode'] = 0;
			  		// 2: 已激活
			  		$result['info']['status'] = 2;
			  		$result['info']['msg'] = "rid = ".$rid." find";
			  		echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "op_account_status", json_encode($result['info']), $CostTime, $result['retcode']);
			        return true;	
		    	}
		    }
		}
		catch(DynamoDbException $e)
        {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query user data exception(rid): ".$e->getMessage()), $Seq);

			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 内部数据有误的返回码
	  		$result['retcode'] = -1;
	  		$result['info'] = "query user data exception(rid)";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_account_status", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
        }
	}


	// ========================================================================================== //
	// command = new_game
	// key0 = ${app_uid}
	// key1 = ${pid}
	// key2 = ${game_platform}
	public static function new_game($HttpParams, $Seq)
	{   
		$BeginReqTime = CCommon::GetMicroSecond();
		$result = array();

		// 参数校验
		$device = $HttpParams['did'];
		$pid = $HttpParams['pid'];
		$game_platform = $HttpParams['platform'];
		$r_pid = $HttpParams['r_pid'];

		if(null == $r_pid)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "r_pid is null"), $Seq);

	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        $result['retcode'] = -1;
	        $result['info'] = "r_pid is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}
		if(null == $device)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "device is null"), $Seq);

	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        $result['retcode'] = -1;
	        $result['info'] = "device is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}
		if(null == $pid)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "pid is null"), $Seq);

	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        $result['retcode'] = -1;
	        $result['info'] = "pid is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}
		if(null == $game_platform)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "game_platform is null"), $Seq);

	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        $result['retcode'] = -1;
	        $result['info'] = "game_platform is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}

		$dbClient = CAwsDb::GetInstance()->GetDbClient();
		$account_product_response = array();

		// 查询device的是否有product数据
		try
		{
			$account_product_response =  $dbClient->query(array(
		        // "ConsistentRead" => true,
		        "TableName" => account_product_tbl,
		        "IndexName" => "glb_device",
		        // "AttributesToGet" => array("email"),
		        "KeyConditions" => array(
		            "device" => array(
		                "ComparisonOperator" => ComparisonOperator::EQ,
		                "AttributeValueList" => array(
		                    array(Type::STRING => $device)
		                )
		            )
		        )
		    ));
		}
		catch(DynamoDbException $e)
        {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query product data exception(device): ".$e->getMessage()), $Seq);
	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	  		$result['retcode'] = -1;
	  		$result['info'] = "query product data exception(device)";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
        }
		if(0 == $account_product_response['Count'])
	    {
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "device = ".$device." not any product data"), $Seq);
	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	  		$result['retcode'] = 1;
	  		$result['info'] = "device = ".$device." not any product data";
			echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "account_user_node", json_encode($result['info']), $CostTime, $result['retcode']);
			return true;
	    }

	    // 该设备是否存在当前产品的本机数据
	    $product_exist_count = 0;
		$product_response_info = array();
 		foreach($account_product_response['Items'] as $product_response)
 		{
            if($product_response['r_pid']['S'] != $r_pid)
            {
               	continue;
            }
            if(1 == $product_response['status']['N'])
            {
            	continue;
            }
	    	$product_exist_count++;
	    	$product_response_info = $product_response;
	    }
		if(1 == $product_exist_count)
	    {
	    	CLog::LOG_INFO(array(__FILE__, __LINE__, "device = ".$device." have product data"), $Seq);

            $EndReqTime = CCommon::GetMicroSecond();
            $CostTime = $EndReqTime - $BeginReqTime;
            $result['retcode'] = 0;
            $result['info'] = "get product data success";
            echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "account_user_node", json_encode($result['info']), $CostTime, $result['retcode']);
            return true;
            /*
	    	// 删除原有记录
			try
			{
				$dbClient->deleteItem(array(
	                    'TableName' => account_product_tbl,
	                    'Key' => array(
	                        'app_uid'  => array('S' => $product_response_info['app_uid']['S']),
	                        'r_pid' => array('S' => $r_pid)
	                        )
	                    ));
				CLog::LOG_INFO(array(__FILE__, __LINE__, "delete product data success"), $Seq);

		        $EndReqTime = CCommon::GetMicroSecond();
		        $CostTime = $EndReqTime - $BeginReqTime;
		  		$result['retcode'] = 0;
		  		$result['info'] = "delete product data success";
				echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "account_user_node", json_encode($result['info']), $CostTime, $result['retcode']);
				return true;
			}
			catch(DynamoDbException $e)
			{			
				CLog::LOG_ERROR(array(__FILE__, __LINE__, "delete product data exception: ".$e->getMessage()), $Seq);
				
		        $EndReqTime = CCommon::GetMicroSecond();
		        $CostTime = $EndReqTime - $BeginReqTime;
		  		$result['retcode'] = -1;
		  		$result['info'] = "delete product data exception";
				echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
				return false;
			}
            */
	    }
    	else if(0 == $product_exist_count)
    	{
    		CLog::LOG_INFO(array(__FILE__, __LINE__, "device = ".$device." not this product data"), $Seq);
	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	  		$result['retcode'] = 2;
	  		$result['info'] = "device = ".$device." not this product data";
			echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "account_user_node", json_encode($result['info']), $CostTime, $result['retcode']);
			return true;
    	}
    	else
    	{
    		CLog::LOG_ERROR(array(__FILE__, __LINE__, "multi product exist, product_exist_count = ".$product_exist_count), $Seq);
	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	  		$result['retcode'] = -1;
	  		$result['info'] = "multi product exist, product_exist_count = ".$product_exist_count;
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
    	}
	}

	// ========================================================================================== //
	// command = get_account_passwd_status
	public static function get_account_passwd_status($HttpParams, $Seq)
	{
		$BeginReqTime = CCommon::GetMicroSecond();
		$result = array();

		// 参数校验
		$rid = $HttpParams['rid'];

		$dbClient = CAwsDb::GetInstance()->GetDbClient();
		$account_user_response =  array();

		if(null == $rid)
		{
			CLog::LOG_INFO(array(__FILE__, __LINE__, "rid is null"), $Seq);

 			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	  		$result['retcode'] = -1;
	  		$result['info']['msg'] = "rid is null";
	  		echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return true;
		}


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
		                    array(Type::STRING => $rid)
		                )
		            )
		        )
		    ));

		    if(0 == $account_user_response['Count'])
		    {
		    	CLog::LOG_INFO(array(__FILE__, __LINE__, "rid = ".$rid." not find"), $Seq);

		        $EndReqTime = CCommon::GetMicroSecond();
		        $CostTime = $EndReqTime - $BeginReqTime;
		        // 0: 帐号的rid不存在
		  		$result['retcode'] = 0;
		  		$result['info']['msg'] = "rid = ".$rid." not find";
		  		echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "op_account_status", json_encode($result['info']), $CostTime, $result['retcode']);
		        return true;
		    }
		    else
		    {
		    	CLog::LOG_INFO(array(__FILE__, __LINE__, "rid = ".$rid." find"), $Seq);


		    	if(1 == $account_user_response['Items'][0]['pwd_flag']['N'])
		    	{
		    		CLog::LOG_INFO(array(__FILE__, __LINE__, "rid = ".$rid." passwd change"), $Seq);
		    		$EndReqTime = CCommon::GetMicroSecond();
			        $CostTime = $EndReqTime - $BeginReqTime;
			        // 1: 帐号的密码被修改过
			  		$result['retcode'] = 1;
			  		$result['info']['msg'] = "rid = ".$rid." passwd change";
			  		echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "op_account_status", json_encode($result['info']), $CostTime, $result['retcode']);
			        return true;
		    	}
		    	else if(1 == $account_user_response['Items'][0]['status']['N'])
		    	{
		    		CLog::LOG_INFO(array(__FILE__, __LINE__, "rid = ".$rid." black"), $Seq);
		    		$EndReqTime = CCommon::GetMicroSecond();
			        $CostTime = $EndReqTime - $BeginReqTime;
			        // 2: 帐号受限
			  		$result['retcode'] = 2;
			  		$result['info']['msg'] = "rid = ".$rid." black";
			  		echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "op_account_status", json_encode($result['info']), $CostTime, $result['retcode']);
			        return true;
		    	}
		    	else
		    	{
		    		CLog::LOG_INFO(array(__FILE__, __LINE__, "rid = ".$rid." normal state"), $Seq);
		    		$EndReqTime = CCommon::GetMicroSecond();
			        $CostTime = $EndReqTime - $BeginReqTime;
					// 2: 帐号的密码没被修改过
			  		$result['retcode'] = 3;
			  		$result['info']['msg'] = "rid = ".$rid." normal state";
			  		echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "op_account_status", json_encode($result['info']), $CostTime, $result['retcode']);
			        return true;	
		    	}
		    }
		}
		catch(DynamoDbException $e)
        {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query user data exception(rid): ".$e->getMessage()), $Seq);

			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 内部数据有误的返回码
	  		$result['retcode'] = -1;
	  		$result['info'] = "query user data exception(rid)";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_account_status", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
        }


	}


	// function ==> 帮助玩家用页面注册的邮箱绑定丢失的游戏数据
	public static function op_binding_product_email($HttpParams, $Seq)
	{
		$dbClient = CAwsDb::GetInstance()->GetDbClient();
		$account_user_email_esponse = array();
		$account_product_response = array();
		$result = array();

		$email = strtolower($HttpParams['key0']);
		$app_uid = $HttpParams['key1'];
		$r_pid = $HttpParams['key2'];

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

	    if(0 == $account_user_email_esponse['Count'])
	    {
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "email = ".$email." not register"), $Seq);
	    	$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: email没有注册的返回码
			$result['retcode'] = EN_RET_CODE__EMAIL_NOT_REGISTER;
	        // $result['retcode'] = -1;
	  		$result['info'] = "email = ".$email." not register";
	  		echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
	    }
	    if($account_user_email_esponse['Count'] > 1)
	    {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "multi email, account_user_email_esponse = ".$account_user_email_esponse), $Seq);
			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 内部数据有误的返回码
			$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	        // $result['retcode'] = -1;
	  		$result['info'] = "multi email";
	  		echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
	    }
	    CLog::LOG_INFO(array(__FILE__, __LINE__, "rid = ".$account_user_email_esponse['Items'][0]['rid']['S']), $Seq);

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
		                    array(Type::STRING => $account_user_email_esponse['Items'][0]['rid']['S'])
		                )
		            )
		        )
		    ));
		}
		catch(DynamoDbException $e)
        {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query product data exception(email): ".$e->getMessage()), $Seq);
			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 内部数据有误的返回码
			$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	        // $result['retcode'] = -1;
	  		$result['info'] = "query product data exception(email)";
	  		echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
        }
		if(0 < $account_product_response['Count'])
		{
			$product_exist_count = 0;
	 		foreach($account_product_response['Items'] as $product_response)
	 		{
	            if($product_response['r_pid']['S'] != $r_pid)
	            {
	               	continue;         	
	            }
		    	$product_exist_count++;
	        }
			CLog::LOG_INFO(array(__FILE__, __LINE__, "product_exist_count = ".$product_exist_count), $Seq);	        
		    if(0 != $product_exist_count)
		    {
		    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "rid = ".$account_user_email_esponse['Items'][0]['rid']['S']." already binding product data"), $Seq);
		    	$EndReqTime = CCommon::GetMicroSecond();
	        	$CostTime = $EndReqTime - $BeginReqTime;
		  		// todo: 该email已经绑定过该产品
		        $result['retcode'] = EN_RET_CODE__EMAIL_ALREADY_BINDING_DATA;
				$result['info'] = "rid = ".$account_user_email_esponse['Items'][0]['rid']['S']." already binding product data";
				echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
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
                            array(Type::STRING => $app_uid)
                        )
                    ),
		            "r_pid" => array(
		                "ComparisonOperator" => ComparisonOperator::EQ,
		                "AttributeValueList" => array(
		                    array(Type::STRING => $r_pid)
		                )
		            )
		        )
		    ));
        }
        catch(Exception $e)
        {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query product data exception: ".$e->getMessage()), $Seq);
			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 内部数据有误的返回码
			$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	        // $result['retcode'] = -1;
	  		$result['info'] = "query product data exception: ".$e->getMessage();
	  		echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
        }
		if(0 == $account_product_response['Count'])
		{
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "app_uid = ".$app_uid.", r_pid = ".$r_pid." not product data"), $Seq);
	    	$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 找不到相应的产品数据的返回码
			$result['retcode'] = EN_RET_CODE__NOT_FIND_CURDATA;
	        // $result['retcode'] = -1;
	  		$result['info'] = "app_uid = ".$app_uid.", r_pid = ".$r_pid." not product data";
	  		echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
	    }
		if(1 == $account_product_response['Items'][0]['status']['N'])
		{
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "app_uid = ".$app_uid.", r_pid = ".$r_pid."  product data already binding, rid = ".$account_user_email_esponse['Items'][0]['rid']['S']), $Seq);
			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 当前产品数据已经被绑定的返回码
			$result['retcode'] = EN_RET_CODE__CURDATA_ALREADY_BINDING;
	        // $result['retcode'] = -1;
	  		$result['info'] = "app_uid = ".$app_uid.", r_pid = ".$r_pid." product data already binding";
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
                        Type::STRING => "IOS"
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
			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 内部数据有误的返回码
			$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	        // $result['retcode'] = -1;
	  		$result['info'] = "update user data exception: ".$e->getMessage();
	  		echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
        }	


        // 更新account_game表(绑定产品)
    	$product_info = json_decode($account_product_response['Items'][0]['product_info']['S'], true);
    	$product_info['binding_vs'] = "0.9";
		$product_info['binding_pid'] = "1065845844";
		$product_info['binding_platform'] = "IOS";
		try
	    {
            $dbClient->updateItem(array(
            'TableName' => account_product_tbl,
            "Key" => array(
                "app_uid" => array(
                    Type::STRING => $app_uid
                    ),
                "r_pid" => array(
                    Type::STRING => $r_pid
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
			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			// todo: 内部数据有误的返回码
			$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	        // $result['retcode'] = -1;
	  		$result['info'] = "update product data exception: ".$e->getMessage();
	  		echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
        }	

        $EndReqTime = CCommon::GetMicroSecond();
	    $CostTime = $EndReqTime - $BeginReqTime;
      	$result['retcode'] = 0;
  		$result['info']['rid'] = $account_user_email_esponse['Items'][0]['rid']['S'];
		echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "account_user_node", json_encode($result['info']), $CostTime, $result['retcode']);

        return true;

	}

	// function ==> 根据did和邮箱获取玩家当前sid
	public static function get_player_now_sid($HttpParams, $Seq)
	{
		$dbClient = CAwsDb::GetInstance()->GetDbClient();
		$account_user_email_response = array();
		$account_product_response = array();
		$result = array();
		// 0: 没有找到uid; 1: did的uid; 2: email的uid
		$type = 0;
		$rid = "";
		$uid = "";

		$BeginReqTime = CCommon::GetMicroSecond();
	
		// 参数校验
		if(!array_key_exists("key1", $HttpParams))
	    {
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "params not incomplete"), $Seq);

	 		$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        // todo: 参数错误的返回码
	        $result['retcode'] = -1;
	        $result['info'] = "params not incomplete";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}
		$email = $HttpParams['key1'];
		$pid = $HttpParams['pid'];
		$device = $HttpParams['did'];



		if(null == $pid)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "pid is null"), $Seq);

	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        // todo: 参数错误的返回码
	        $result['retcode'] = -1;
	        $result['info'] = "pid is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}
        $r_pid = CConf::GetRealPid($pid);
        if("-1" == $r_pid)
        {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "r_pid not exist"), $Seq);

	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        $result['retcode'] = -1;
	        $result['info'] = "r_pid not exist";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
        }
		if(null == $device)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "device is null"), $Seq);

	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        // todo: 参数错误的返回码
	        $result['retcode'] = -1;
	        $result['info'] = "device is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}
		if(null == $email)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "email is null"), $Seq);
			$email = "";
		}

		if("" != $email)
		{
			CLog::LOG_DEBUG(array(__FILE__, __LINE__, "email get rid"), $Seq);
			// 通过email获取玩家信息
			try
			{
				$account_user_email_response =  $dbClient->query(array(
			        // "ConsistentRead" => true,
			        "TableName" => account_user_tbl,
			        "IndexName" => "email-index",
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
				CLog::LOG_ERROR(array(__FILE__, __LINE__, "query user data exception(email): ".$e->getMessage()), $Seq);
			
		        $result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
		  		$result['info'] = "query user data exception(email): ".$e->getMessage();
		  		echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
		        return false;
	        }
	       	if(1 < $account_user_email_response['Count'])
	        {
	        	$EndReqTime = CCommon::GetMicroSecond();
		        $CostTime = $EndReqTime - $BeginReqTime;
	        	CLog::LOG_ERROR(array(__FILE__, __LINE__, "multi email, ".$email), $Seq);
				
		        $result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
		  		$result['info'] = "multi email, ".$email;
		  		echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
		        return false;
	        }

	       	
			if(1 == $account_user_email_response['Count'])
	        {        	
				$rid = $account_user_email_response['Items'][0]['rid']['S'];
	        }
		}
		

        
        if("" != $rid)
        {
        	CLog::LOG_DEBUG(array(__FILE__, __LINE__, "rid get uid"), $Seq);
        	// 查询device的是否有product数据
			try
			{
				$account_product_response =  $dbClient->query(array(
			        "TableName" => account_product_tbl,
			        "IndexName" => "glb_rid",
			        "KeyConditions" => array(
			            "rid" => array(
			                "ComparisonOperator" => ComparisonOperator::EQ,
			                "AttributeValueList" => array(
			                    array(Type::STRING => $rid)
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
		        $result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
		  		$result['info'] = "query product data exception(rid)";
				echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
		        return false;
	        }
	        if(0 != $account_product_response['Count'])
		    {
				// 查找没有绑定过的本机数据
				$product_exist_count = 0;
				$product_response_info = array();
		 		foreach($account_product_response['Items'] as $product_response)
		 		{
		            if($product_response['r_pid']['S'] != $r_pid)
		            {
		               	continue;
		            }
			    	$product_exist_count++;
			    	$product_response_info = $product_response;
		        }

		        if(1 == $product_exist_count)
			    {
			    	CLog::LOG_INFO(array(__FILE__, __LINE__, "rid = ".$rid." have product data"), $Seq);
					$uid = $product_response_info['app_uid']['S'];
					$type = 1;
			    	
			    }
		    	else if(0 == $product_exist_count)
		    	{
		    		CLog::LOG_INFO(array(__FILE__, __LINE__, "rid = ".$rid." not this product data"), $Seq);
		    	}
		    	else
		    	{
		    		CLog::LOG_ERROR(array(__FILE__, __LINE__, "multi product exist, product_exist_count = ".$product_exist_count), $Seq);

		    		$EndReqTime = CCommon::GetMicroSecond();
		        	$CostTime = $EndReqTime - $BeginReqTime;
					// todo: 内部数据有误的返回码
					$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
					// $result['retcode'] = -1;
			  		$result['info'] = "multi product exist, product_exist_count = ".$product_exist_count;
					echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
		        	return false;
		    	}
			}

		}

		if("" == $uid)
		{
			CLog::LOG_DEBUG(array(__FILE__, __LINE__, "did get uid"), $Seq);
			// 查询device的是否有product数据
			try
			{
				$account_product_response =  $dbClient->query(array(
			        // "ConsistentRead" => true,
			        "TableName" => account_product_tbl,
			        "IndexName" => "glb_device",
			        "KeyConditions" => array(
			            "device" => array(
			                "ComparisonOperator" => ComparisonOperator::EQ,
			                "AttributeValueList" => array(
			                    array(Type::STRING => $device)
			                )
			            )
			        )
			    ));
			}
			catch(DynamoDbException $e)
	        {
				CLog::LOG_ERROR(array(__FILE__, __LINE__, "query product data exception(device): ".$e->getMessage()), $Seq);

				$EndReqTime = CCommon::GetMicroSecond();
		        $CostTime = $EndReqTime - $BeginReqTime;
				// todo: 内部数据有误的返回码
		        $result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
		  		$result['info'] = "query product data exception(device)";
				echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
		        return false;
	        }

			// 查找没有绑定过的本机数据
			$product_exist_count = 0;
			$product_response_info = array();
	 		foreach($account_product_response['Items'] as $product_response)
	 		{
	            if($product_response['r_pid']['S'] != $r_pid)
	            {
	               	continue;
	            }
	            if(1 == $product_response['status']['N'])
	            {
	            	continue;
	            }
		    	$product_exist_count++;
		    	$product_response_info = $product_response;
	        }
		    if(1 == $product_exist_count)
		    {
		    	CLog::LOG_INFO(array(__FILE__, __LINE__, "device = ".$device." have product data"), $Seq);
		    	$uid = $product_response_info['app_uid']['S'];
		    	$type = 1;
		    }
	    	else if(0 == $product_exist_count)
	    	{
	    		CLog::LOG_INFO(array(__FILE__, __LINE__, "device = ".$device." not this product data"), $Seq);
	    	}
	    	else
	    	{
	    		CLog::LOG_ERROR(array(__FILE__, __LINE__, "multi product exist, product_exist_count = ".$product_exist_count), $Seq);

	    		$EndReqTime = CCommon::GetMicroSecond();
	        	$CostTime = $EndReqTime - $BeginReqTime;
				// todo: 内部数据有误的返回码
				$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
				// $result['retcode'] = -1;
		  		$result['info'] = "multi product exist, product_exist_count = ".$product_exist_count;
				echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        	return false;
	    	}
		}

		$EndReqTime = CCommon::GetMicroSecond();
		$CostTime = $EndReqTime - $BeginReqTime;

		$result['info']['type'] = $type;
		$result['info']['uid'] = $uid;
		$result['retcode'] = 0;

		echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "account_user_node", json_encode($result['info']), $CostTime, $result['retcode']);
		return true;
	}
}



?>