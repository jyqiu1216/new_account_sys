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
Class CRegisterLogic
{
	// ========================================================================================== //
	// function ==> 生成rid(标识用户帐号的唯一码)
	private static function gen_rid($Seq)
	{	
		$random = rand() % 100;
		return (string)((int)$Seq * 100 + (rand() % 100));
	}


// ========================================================================================== //
	// command = new_visitor_register
	// key0 = ${device}
	// key1 = ${pid}
	// key2 = ${game_platform}
	// key3 = ${sy}
	// key4 = ${vs}
	// key5 = ${idfa}
	// key6 = ${app_uid}
	// key7 = ${sid}
	public static function new_visitor_register($HttpParams, $Seq)
	{
		$BeginReqTime = CCommon::GetMicroSecond();
		$result = array();
		// 参数校验
		$device = $HttpParams['did'];
		$pid = $HttpParams['pid'];
		$game_platform = $HttpParams['platform'];
		$sy = $HttpParams['sy']; 
		$vs = $HttpParams['vs']; 
		$idfa = $HttpParams['idfa']; 
		$app_uid = $HttpParams['uid']; 
		$r_pid = $HttpParams['r_pid'];
		$sid = $HttpParams['sid'];
	

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
		if(null == $sy)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "sy is null"), $Seq);

	 		$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        // todo: 参数错误的返回码
	        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        // $result['retcode'] = -1;
	        $result['info'] = "sy is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}
		if(null == $vs)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "vs is null"), $Seq);

	 		$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        // todo: 参数错误的返回码
	        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        // $result['retcode'] = -1;
	        $result['info'] = "vs is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}
		if(null == $idfa)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "idfa is null"), $Seq);

	 		$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        // todo: 参数错误的返回码
	        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        // $result['retcode'] = -1;
	        $result['info'] = "idfa is null";
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



		// 查询device的是否有product数据
		try
		{
			$account_product_response =  $dbClient->query(array(
		        // "ConsistentRead" => true,
		        "TableName" => account_product_tbl,
		        "IndexName" => "device-index",
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
		if(0 == $account_product_response['Count'])
	    {
	    	CLog::LOG_INFO(array(__FILE__, __LINE__, "device = ".$device." not any product data"), $Seq);
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
				CLog::LOG_ERROR(array(__FILE__, __LINE__, "query product data exception(app_uid + r_pid): ".$e->getMessage()), $Seq);

				$EndReqTime = CCommon::GetMicroSecond();
	        	$CostTime = $EndReqTime - $BeginReqTime;
		        $result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
		  		$result['info'] = "query product data exception(app_uid + r_pid)";
				echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
				return false;
	        }
			if(0 != $account_product_response['Count'])
			{
		    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "app_uid = ".$app_uid.", r_pid = ".$r_pid." already exist"), $Seq);

		    	$EndReqTime = CCommon::GetMicroSecond();
	        	$CostTime = $EndReqTime - $BeginReqTime;
				$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
		  		$result['info'] = "app_uid = ".$app_uid.", r_pid = ".$r_pid." already exist";
				echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
		        return false;
		    }
	    }
	    else
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
	            if(1 == $product_response['status']['N'])
	            {
	            	continue;
	            }
		    	$product_exist_count++;
		    	$product_response_info = $product_response;
	        }
		    if(1 == $product_exist_count)
		    {
		    	CLog::LOG_INFO(array(__FILE__, __LINE__, "device = ".$device." already have product data"), $Seq);

		    	// 删除原有记录
				try
				{
					/*
					$dbClient->deleteItem(array(
	                        'TableName' => account_product_tbl,
	                        'Key' => array(
	                            'app_uid'  => array('S' => $product_response_info['app_uid']['S']),
	                            'r_pid' => array('S' => $product_response_info['r_pid']['S'])
	                            )
	                        ));
	                */

					$dbClient->updateItem(array(
			            'TableName' => account_product_tbl,
			            "Key" => array(
			                "app_uid" => array(
			                    Type::STRING => $product_response_info['app_uid']['S']
			                    ),
			                "r_pid" => array(
			                    Type::STRING => $product_response_info['r_pid']['S']
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


				}
				catch(DynamoDbException $e)
				{			
					CLog::LOG_ERROR(array(__FILE__, __LINE__, "delete product data exception(email): ".$e->getMessage()), $Seq);

					$EndReqTime = CCommon::GetMicroSecond();
	        		$CostTime = $EndReqTime - $BeginReqTime;
					// todo: 内部数据有误的返回码
			        $result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
			        // $result['retcode'] = -1;
			  		$result['info'] = "delete product data exception(email): ".$e->getMessage();
					echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			        return false;
				}

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


	    $product_info = array();
	    $product_info['pid'] = $pid;
	    $product_info['device'] = $device;
	    $product_info['idfa'] = $idfa;
	    $product_info['sy'] = $sy;
	    $product_info['vs'] = $vs;
	    $product_info['platform'] = $game_platform;
	    $product_info['binding_vs'] = null;
	    $product_info['binding_pid'] = null;
	    $product_info['binding_platform'] = null;

		// 更新account_product表
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
           		"device" => array(
                    "Action" => AttributeAction::PUT,
                    "Value" => array(
                        Type::STRING => $device
                        )
                    ),	         
              	"lgtime" => array(
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
               	"sid" => array(
                    "Action" => AttributeAction::PUT,
                    "Value" => array(
                        Type::NUMBER => (int)$sid
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
    

		CLog::LOG_INFO(array(__FILE__, __LINE__, "visitor register success"), $Seq);
        $EndReqTime = CCommon::GetMicroSecond();
        $CostTime = $EndReqTime - $BeginReqTime;
		$result['retcode'] = 0;
		$result['info'] = "visitor register success";
		echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "account_user_node", json_encode($result['info']), $CostTime, $result['retcode']);
		return true;

	}


	// ========================================================================================== //
	// command = visitor_register
	// key0 = ${device}
	// key1 = ${pid}
	// key2 = ${game_platform}
	// key3 = ${sy}
	// key4 = ${vs}
	// key5 = ${idfa}
	// key6 = ${app_uid}
	public static function visitor_register($HttpParams, $Seq)
	{
		$BeginReqTime = CCommon::GetMicroSecond();
		$result = array();
		// 参数校验
		$device = $HttpParams['did'];
		$pid = $HttpParams['pid'];
		$game_platform = $HttpParams['platform'];
		$sy = $HttpParams['sy']; 
		$vs = $HttpParams['vs']; 
		$idfa = $HttpParams['idfa']; 
		$app_uid = $HttpParams['uid']; 
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
		if(null == $sy)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "sy is null"), $Seq);

	 		$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        // todo: 参数错误的返回码
	        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        // $result['retcode'] = -1;
	        $result['info'] = "sy is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}
		if(null == $vs)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "vs is null"), $Seq);

	 		$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        // todo: 参数错误的返回码
	        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        // $result['retcode'] = -1;
	        $result['info'] = "vs is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}
		if(null == $idfa)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "idfa is null"), $Seq);

	 		$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        // todo: 参数错误的返回码
	        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        // $result['retcode'] = -1;
	        $result['info'] = "idfa is null";
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


		$dbClient = CAwsDb::GetInstance()->GetDbClient();
		$account_product_response = array();



		// 查询device的是否有product数据
		try
		{
			$account_product_response =  $dbClient->query(array(
		        // "ConsistentRead" => true,
		        "TableName" => account_product_tbl,
		        "IndexName" => "device-index",
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
		if(0 == $account_product_response['Count'])
	    {
	    	CLog::LOG_INFO(array(__FILE__, __LINE__, "device = ".$device." not any product data"), $Seq);
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
				CLog::LOG_ERROR(array(__FILE__, __LINE__, "query product data exception(app_uid + r_pid): ".$e->getMessage()), $Seq);

				$EndReqTime = CCommon::GetMicroSecond();
	        	$CostTime = $EndReqTime - $BeginReqTime;
		        $result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
		  		$result['info'] = "query product data exception(app_uid + r_pid)";
				echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
				return false;
	        }
			if(0 != $account_product_response['Count'])
			{
		    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "app_uid = ".$app_uid.", r_pid = ".$r_pid." already exist"), $Seq);

		    	$EndReqTime = CCommon::GetMicroSecond();
	        	$CostTime = $EndReqTime - $BeginReqTime;
				$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
		  		$result['info'] = "app_uid = ".$app_uid.", r_pid = ".$r_pid." already exist";
				echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
		        return false;
		    }
	    }
	    else
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
	            if(1 == $product_response['status']['N'])
	            {
	            	continue;
	            }
		    	$product_exist_count++;
		    	$product_response_info = $product_response;
	        }
		    if(1 == $product_exist_count)
		    {
		    	CLog::LOG_INFO(array(__FILE__, __LINE__, "device = ".$device." already have product data"), $Seq);

		    	// 删除原有记录
				try
				{
					$dbClient->deleteItem(array(
	                        'TableName' => account_product_tbl,
	                        'Key' => array(
	                            'app_uid'  => array('S' => $product_response_info['app_uid']['S']),
	                            'r_pid' => array('S' => $product_response_info['r_pid']['S'])
	                            )
	                        ));
				}
				catch(DynamoDbException $e)
				{			
					CLog::LOG_ERROR(array(__FILE__, __LINE__, "delete product data exception(email): ".$e->getMessage()), $Seq);

					$EndReqTime = CCommon::GetMicroSecond();
	        		$CostTime = $EndReqTime - $BeginReqTime;
					// todo: 内部数据有误的返回码
			        $result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
			        // $result['retcode'] = -1;
			  		$result['info'] = "delete product data exception(email): ".$e->getMessage();
					echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			        return false;
				}

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


	    $product_info = array();
	    $product_info['pid'] = $pid;
	    $product_info['device'] = $device;
	    $product_info['idfa'] = $idfa;
	    $product_info['sy'] = $sy;
	    $product_info['vs'] = $vs;
	    $product_info['platform'] = $game_platform;
	    $product_info['binding_vs'] = null;
	    $product_info['binding_pid'] = null;
	    $product_info['binding_platform'] = null;

		// 更新account_product表
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
           		"device" => array(
                    "Action" => AttributeAction::PUT,
                    "Value" => array(
                        Type::STRING => $device
                        )
                    ),	         
              	"lgtime" => array(
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
    

		CLog::LOG_INFO(array(__FILE__, __LINE__, "visitor register success"), $Seq);
        $EndReqTime = CCommon::GetMicroSecond();
        $CostTime = $EndReqTime - $BeginReqTime;
		$result['retcode'] = 0;
		$result['info'] = "visitor register success";
		echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "account_user_node", json_encode($result['info']), $CostTime, $result['retcode']);
		return true;

	}


	// ========================================================================================== //
	// command = register
	// key0 = ${email}
	// key1 = ${passwd}
	// key2 = ${th_id}
	// key3 = ${type}
	// key4 = ${register_platfrom}
	public static function register($HttpParams, $Seq)
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
		$th_id = $HttpParams['key2'];
		$type = $HttpParams['key3'];
		$register_platfrom = $HttpParams['key4']; 	// 格式(fd_id:facebook_snow)
		$device = $HttpParams['did'];
		$pid = $HttpParams['pid'];
		$game_platform = $HttpParams['platform'];
		$app_uid = $HttpParams['uid']; 
		$product_vs = $HttpParams['vs']; 
		$r_pid = $HttpParams['r_pid']; 

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
		if(null == $passwd)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "passwd is null"), $Seq);

	 		$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        // todo: 参数错误的返回码
	        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        // $result['retcode'] = -1;
			$result['info'] = "passwd is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}
		if(null == $register_platfrom)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "register_platfrom is null"), $Seq);

	 		$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        // todo: 参数错误的返回码
	        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        // $result['retcode'] = -1;
			$result['info'] = "register_platfrom is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}
		if(null == $type)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "type is null"), $Seq);

	 		$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        // todo: 参数错误的返回码
	        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        // $result['retcode'] = -1;
			$result['info'] = "type is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}


		$register_flag = false;
		if(null == $th_id)
		{
			CLog::LOG_INFO(array(__FILE__, __LINE__, "email register, email = ".$email), $Seq);
			$register_info = array();
			$register_info['email'] = $email;
	    	$register_info['passwd'] = $passwd;
	    	$register_info['register_platfrom'] = $register_platfrom;
	    	$register_info['type'] = $type;
			$register_info['device'] = $device;
			$register_info['pid'] = $pid;
			$register_info['game_platform'] = $game_platform;
			$register_info['app_uid'] = $app_uid;
			$register_info['product_vs'] = $product_vs;
			$register_info['r_pid'] = $r_pid;
			// $ret = CRegisterLogic::register_email($register_info, $RetCode, $Seq);
			$ret = CRegisterLogic::register_email($register_info, $result, $Seq);
			if(true == $ret)
			{
				$register_flag = true;
				CLog::LOG_INFO(array(__FILE__, __LINE__, "email register sucess, email = ".$email), $Seq);	
			}
		}
		else
		{
			CLog::LOG_INFO(array(__FILE__, __LINE__, "third register, th_id = ".$th_id), $Seq);
			$register_info = array();
			$register_info['email'] = $email;
	    	$register_info['passwd'] = $passwd;
	    	$register_info['register_platfrom'] = $register_platfrom;
	    	$register_info['type'] = $type;
	    	$register_info['th_id'] = $th_id;
			$register_info['device'] = $device;
			$register_info['pid'] = $pid;
			$register_info['game_platform'] = $game_platform;
			$register_info['app_uid'] = $app_uid;
			$register_info['product_vs'] = $product_vs;
			$register_info['r_pid'] = $r_pid;
			// $ret = CRegisterLogic::register_th_id($register_info, $RetCode, $Seq);
			$ret = CRegisterLogic::register_th_id($register_info, $result, $Seq);
			if(true == $ret)
			{
				$register_flag = true;
				CLog::LOG_INFO(array(__FILE__, __LINE__, "th_id register sucess, th_id = ".$th_id), $Seq);	  
			}
		}

		if(true == $register_flag)
		{			
			$active_url = "";					
			$rid = CRegisterLogic::gen_rid($Seq);
			CLog::LOG_INFO(array(__FILE__, __LINE__, "register success, send active mail"), $Seq);
			if(null == $device)
			{
				// 发送外部邮件
				CLog::LOG_INFO(array(__FILE__, __LINE__, "send normal active mail"), $Seq);
				$active_url_params = "";
				$active_url_params = "time=".time()."&pid=&platform=&did=&uid=&vs=&idfa=";
				$active_url_params = $active_url_params."&command=active";
				$active_url_params = $active_url_params."&key0=".$rid;
				$active_url_params = $active_url_params."&key1=".urlencode($email);
				$active_url_params = $active_url_params."&key2=".urlencode($passwd);
				$active_url_params = $active_url_params."&key3=".urlencode($th_id);
				$active_url_params = $active_url_params."&key4=".urlencode($type);
				$active_url_params = $active_url_params."&key5=".urlencode($register_platfrom);

				CLog::LOG_INFO(array(__FILE__, __LINE__, "active_url_params = ".$active_url_params), $Seq);
				if(0 == en_flag)
				{
					$active_url = account_de_prefix."request=".$active_url_params;
				}
				else
				{
					$active_url = account_en_prefix."request=".encrypt_url_php($active_url_params);
				}
				CLog::LOG_INFO(array(__FILE__, __LINE__, "active_url = ".$active_url), $Seq);

				$account_mail_json_path = CConf::GetAccountMailJson(0);
				$account_mail_json = json_decode(file_get_contents($account_mail_json_path), true);

				$mailtitle = "Subject: ".$account_mail_json['data']['1']['title'];;
				$mailcontent_raw = $account_mail_json['data']['1']['content'];
				$product_project_info = CConf::GetProject($r_pid);
				$mailcontent = str_replace("STRING0", leyi_name, $mailcontent_raw, $count);
				$mailcontent = str_replace("STRING1", $active_url, $mailcontent, $count);
				$sender = account_mail_sender;
				$receiver = $email;
				$mailtxtname = $email.".send.".time();
				file_put_contents("./register_mail/".$mailtxtname, $mailtitle."\n", FILE_APPEND);
				file_put_contents("./register_mail/".$mailtxtname, "From: ".$sender."\n", FILE_APPEND);
				file_put_contents("./register_mail/".$mailtxtname, "To: ".$receiver."\n", FILE_APPEND);
				file_put_contents("./register_mail/".$mailtxtname, "\n", FILE_APPEND);
				file_put_contents("./register_mail/".$mailtxtname, $mailcontent, FILE_APPEND);
			}
			else
			{
				// 发送外部邮件
				CLog::LOG_INFO(array(__FILE__, __LINE__, "send binding product active mail"), $Seq);
				$active_url_params = "";
				$active_url_params = "time=".time()."&pid=".$pid."&platform=".$game_platform."&did=".urlencode($device)."&uid=".$app_uid."&vs=".urlencode($product_vs)."&idfa=";
				$active_url_params = $active_url_params."&command=active";
				$active_url_params = $active_url_params."&key0=".$rid;
				$active_url_params = $active_url_params."&key1=".urlencode($email);
				$active_url_params = $active_url_params."&key2=".urlencode($passwd);
				$active_url_params = $active_url_params."&key3=".urlencode($th_id);
				$active_url_params = $active_url_params."&key4=".urlencode($type);
				$active_url_params = $active_url_params."&key5=".urlencode($register_platfrom);

				CLog::LOG_INFO(array(__FILE__, __LINE__, "active_url_params = ".$active_url_params), $Seq);
				if(0 == en_flag)
				{
					$active_url = account_de_prefix."request=".$active_url_params;
				}
				else
				{
					$active_url = account_en_prefix."request=".encrypt_url_php($active_url_params);
				}
				CLog::LOG_INFO(array(__FILE__, __LINE__, "active_url = ".$active_url), $Seq);


				$account_mail_json_path = CConf::GetAccountMailJson(0);
				$account_mail_json = json_decode(file_get_contents($account_mail_json_path), true);

				$mailtitle = "Subject: ".$account_mail_json['data']['1']['title'];;
				$mailcontent_raw = $account_mail_json['data']['1']['content'];
				$product_project_info = CConf::GetProject($r_pid);
				$mailcontent = str_replace("STRING0", $product_project_info[1], $mailcontent_raw, $count);
				$mailcontent = str_replace("STRING1", $active_url, $mailcontent, $count);
				$sender = account_mail_sender;
				$receiver = $email;
				$mailtxtname = $email.".send.".time();
				file_put_contents("./register_mail/".$mailtxtname, $mailtitle."\n", FILE_APPEND);
				file_put_contents("./register_mail/".$mailtxtname, "From: ".$sender."\n", FILE_APPEND);
				file_put_contents("./register_mail/".$mailtxtname, "To: ".$receiver."\n", FILE_APPEND);
				file_put_contents("./register_mail/".$mailtxtname, "\n", FILE_APPEND);
				file_put_contents("./register_mail/".$mailtxtname, $mailcontent, FILE_APPEND);


				// 发游戏内部邮件(邮件认证)
				CLog::LOG_INFO(array(__FILE__, __LINE__, "send validate account mail in game"), $Seq);
				$product_project_info = CConf::GetProject($r_pid);
				$game_mail_url_params = "";
				$game_mail_url_params = "ope=sendmail&version=1&did=System&lg=1&uid=225&sid=0&command=operate_mail_send";
				$game_mail_url_params = $game_mail_url_params."&key0=2";
				$game_mail_url_params = $game_mail_url_params."&key1=".$app_uid;
				$game_mail_url_params = $game_mail_url_params."&key2=System";
				$game_mail_url_params = $game_mail_url_params."&key3=30";
				$game_mail_url_params = $game_mail_url_params."&key4=";
				$game_mail_url_params = $game_mail_url_params."&key5=";
				$game_mail_url_params = $game_mail_url_params."&key6=0";
				$game_mail_url_params = $game_mail_url_params."&key7=";
				$game_mail_url = $product_project_info[2].$game_mail_url_params;
				CLog::LOG_INFO(array(__FILE__, __LINE__, "game_mail_url = ".$game_mail_url), $Seq);
				$gamemailtxtname = $email.".game_send.".time();
				$gamemailcontent = "wget -Ogame_mail.json ".'"'.$game_mail_url.'"';
				file_put_contents("./game_mail/".$product_project_info[0]."/".$gamemailtxtname, $gamemailcontent);

				// server request
				// 向后台记录帐号注册帐号操作
				CLog::LOG_INFO(array(__FILE__, __LINE__, "set register leyi account operate to server"), $Seq);
				$product_project_info = CConf::GetProject($r_pid);
				$server_account_url_params = "";
				$server_account_url_params = "lrct=10&did=account-system&sid=0&uid=".$app_uid."&command=operate_account_operate";
				$server_account_url_params = $server_account_url_params."&key0=0";
				$server_account_url = $product_project_info[2].$server_account_url_params;
				CLog::LOG_INFO(array(__FILE__, __LINE__, "server_account_url = ".$server_account_url), $Seq);
				$serverrequesttxtname = $email.".server_request.".time();
				$serverrequestcontent = "wget -Oserver_request.json ".'"'.$server_account_url.'"';
				file_put_contents("./server_request/".$product_project_info[0]."/".$serverrequesttxtname, $serverrequestcontent);

			}

			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        if(null == $th_id)
	        {	
				$result['info']['rid'] = $rid;
				$result['info']['active_url'] = $active_url;
				$result['retcode'] = 0;

	        	echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "account_user_node", json_encode($result['info']), $CostTime, $result['retcode']);
	        	return true;
	        }
			else
			{
				$result['info']['rid'] = $rid;
				$result['info']['active_url'] = $active_url;
				$result['retcode'] = 0;
 	
	        	echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "account_user_node", json_encode($result['info']), $CostTime, $result['retcode']);
				return true;
			}
		}
		else
		{
			CLog::LOG_INFO(array(__FILE__, __LINE__, "register failed, not send active mail"), $Seq);
			
	        $EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
		}
	}

	// ========================================================================================== //
	// function ==> 邮箱注册
	// private static function register_email($register_info, &$RetCode, $Seq)
	private static function register_email($register_info, &$result, $Seq)
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
		                    array(Type::STRING => $register_info['email'])
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
        if(1 < $account_user_email_response['Count'])
        {
        	CLog::LOG_ERROR(array(__FILE__, __LINE__, "multi email, ".$register_info['email']), $Seq);
			// todo: 内部数据有误的返回码
	        $result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	        // $result['retcode'] = -1;
	  		$result['info'] = "multi email, ".$register_info['email'];
	        return false;
        }


       	if(0 == $account_user_email_response['Count'])
        {
        	CLog::LOG_INFO(array(__FILE__, __LINE__, "email can register rquest"), $Seq);
        	// 产品内email注册
	        if(null != $register_info['device'])			
	        {
				// 检验所需参数
				if(null == $register_info['pid'])
				{
					CLog::LOG_ERROR(array(__FILE__, __LINE__, "pid is null"), $Seq);
					// todo: 参数错误的返回码
			        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
			        // $result['retcode'] = -1;
			  		$result['info'] = "pid is null";
			        return false;
				}
				if(null == $register_info['game_platform'])
				{
					CLog::LOG_ERROR(array(__FILE__, __LINE__, "game_platform is null"), $Seq);
					// todo: 参数错误的返回码
			        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
			        // $result['retcode'] = -1;
			  		$result['info'] = "game_platform is null";
			        return false;
				}
				if(null == $register_info['app_uid'])
				{
					CLog::LOG_ERROR(array(__FILE__, __LINE__, "app_uid is null"), $Seq);
					// todo: 参数错误的返回码
			        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
			        // $result['retcode'] = -1;
			  		$result['info'] = "app_uid is null";
			        return false;
				}
				if(null == $register_info['product_vs'])
				{
					CLog::LOG_ERROR(array(__FILE__, __LINE__, "product_vs is null"), $Seq);
					// todo: 参数错误的返回码
			        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
			        // $result['retcode'] = -1;
			  		$result['info'] = "product_vs is null";
			        return false;
				}
				if(null == $register_info['r_pid'])
				{
					CLog::LOG_ERROR(array(__FILE__, __LINE__, "r_pid is null"), $Seq);
					// todo: 参数错误的返回码
			        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
			        // $result['retcode'] = -1;
			  		$result['info'] = "r_pid is null";
			        return false;
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
	                                array(Type::STRING => $register_info['app_uid'])
	                            )
	                        ),
				            "r_pid" => array(
				                "ComparisonOperator" => ComparisonOperator::EQ,
				                "AttributeValueList" => array(
				                    array(Type::STRING => $register_info['r_pid'])
				                )
				            )
				        )
				    ));
				}
				catch(DynamoDbException $e)
		        {
					CLog::LOG_ERROR(array(__FILE__, __LINE__, "query product data exception (device): ".$e->getMessage()), $Seq);
					// todo: 内部数据有误的返回码
			        $result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
			        // $result['retcode'] = -1;
			  		$result['info'] = "query product data exception (device): ".$e->getMessage();
					return false;
		        }

		        // 不存在游戏记录
			    if(0 == $account_product_response['Count'])
			    {
			    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "device not in our product"), $Seq);
			    	// todo: 当前产品的信息找不到记录的返回码
			        $result['retcode'] = EN_RET_CODE__NOT_FIND_CURDATA;
			        // $result['retcode'] = -1;
			  		$result['info'] = "device not in our product";
			        return false;
			    }
		    	// 当前产品数据已被绑定
		    	if(1 == $account_product_response['Items'][0]['status']['N']) 		
		    	{
		    		CLog::LOG_ERROR(array(__FILE__, __LINE__, "product already binding, account_product_response = ".$account_product_response), $Seq);
			    	// todo: 当前产品数据已被绑定的返回码
			        $result['retcode'] = EN_RET_CODE__CURDATA_ALREADY_BINDING;
			        // $result['retcode'] = -1;
			  		$result['info'] = "product already binding";
			        return false;
		    	}
			    // 找到的游戏记录的设备标识与当前不一致
		    	if($register_info['device'] != $account_product_response['Items'][0]['device']['S'])
		    	{
		    		CLog::LOG_ERROR(array(__FILE__, __LINE__, "not same device, account_product_response = ".$account_product_response), $Seq);
					// todo: 非法的游戏内注册的返回码
			        $result['retcode'] = EN_RET_CODE__INVAIL_EMAIL_REGISTER;
			        // $result['retcode'] = -1;
			  		$result['info'] = "not same device";
					return false;
		    	}
	        }
	       	return true;
        }
        else
        {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "email already register"), $Seq);
			// todo: 该email已经被注册过了的返回码
			$result['retcode'] = EN_RET_CODE__EMAIL_ALREADY_REGISTER;
			// $result['retcode'] = -1;
	  		$result['info'] = "email already register";
			return false;
        }
	}

	// ========================================================================================== //
	// function ==> 第三方注册(与邮箱一起注册)
	private static function register_th_id($register_info, &$result, $Seq)
	{	
		// 检验第三方注册的参数正确性
		if(null == $register_info['th_id'])
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "th_id is null"), $Seq);
	        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	  		$result['info'] = "th_id is null";
	        return false;
		}

		$th_id_arry = explode(':', $register_info['th_id']);
		if(null == $th_id_arry[0]
			|| null == $th_id_arry[1])
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "th_id is invaild, th_id = ".$register_info['th_id']), $Seq);
	        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	  		$result['info'] = "th_id is invaild, th_id = ".$register_info['th_id'];
	        return false;
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
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query user data exception(th_id): ".$e->getMessage()), $Seq);
	        $result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	  		$result['info'] = "query user data exception(th_id)".$e->getMessage();
	        return false;
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
		                    array(Type::STRING => $register_info['email'])
		                )
		            )
		        )
		    ));
		}
		catch(DynamoDbException $e)
        {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query user data exception(email): ".$e->getMessage()), $Seq);
	        $result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	  		$result['info'] = "query user data exception(email)".$e->getMessage();
	        return false;
        }

     	if(1 < $account_user_thid_response['Count'])
        {
        	CLog::LOG_ERROR(array(__FILE__, __LINE__, "multi th_id, ".$register_info['th_id']), $Seq);
	        $result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	  		$result['info'] = "multi th_id, ".$register_info['th_id'];
	        return false;
        }
     	if(1 < $account_user_email_response['Count'])
        {
        	CLog::LOG_ERROR(array(__FILE__, __LINE__, "multi email, ".$register_info['email']), $Seq);
	        $result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
	  		$result['info'] = "multi email, ".$register_info['email'];
	        return false;
        }

		if(0 == $account_user_thid_response['Count'])
        {
        	if(0 == $account_user_email_response['Count'])
        	{
        		CLog::LOG_INFO(array(__FILE__, __LINE__, "th_id can register rquest"), $Seq);
				// 产品内注册
		        if(null != $register_info['device'])										
		        {
		        	// 检验所需参数
					if(null == $register_info['pid'])
					{
						CLog::LOG_ERROR(array(__FILE__, __LINE__, "pid is null"), $Seq);
				        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
				  		$result['info'] = "pid is null";
				        return false;
					}
					if(null == $register_info['game_platform'])
					{
						CLog::LOG_ERROR(array(__FILE__, __LINE__, "game_platform is null"), $Seq);
				  		$result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
				  		$result['info'] = "game_platform is null";
				        return false;
					}
					if(null == $register_info['app_uid'])
					{
						CLog::LOG_ERROR(array(__FILE__, __LINE__, "app_uid is null"), $Seq);
				  		$result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
				  		$result['info'] = "app_uid is null";
				        return false;
					}
					if(null == $register_info['product_vs'])
					{
						CLog::LOG_ERROR(array(__FILE__, __LINE__, "product_vs is null"), $Seq);
				      	// todo: 参数错误的返回码
				  		$result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
				  		$result['info'] = "product_vs is null";
				        return false;
					}
					if(null == $register_info['r_pid'])
					{
						CLog::LOG_ERROR(array(__FILE__, __LINE__, "r_pid is null"), $Seq);
				        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
				  		$result['info'] = "r_pid is null";
				        return false;
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
		                                array(Type::STRING => $register_info['app_uid'])
		                            )
		                        ),
					            "r_pid" => array(
					                "ComparisonOperator" => ComparisonOperator::EQ,
					                "AttributeValueList" => array(
					                    array(Type::STRING => $register_info['r_pid'])
					                )
					            )
					        )
					    ));
					}
					catch(DynamoDbException $e)
			        {
						CLog::LOG_ERROR(array(__FILE__, __LINE__, "query product data exception (app_uid + r_pid): ".$e->getMessage()), $Seq);
				  		$result['retcode'] = EN_RET_CODE__SYSTEM_ERROR;
				  		$result['info'] = "query product data exception (app_uid + r_pid): ".$e->getMessage();
				        return false;
			        }
				    if(0 == $account_product_response['Count'])
				    {
				    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "device not in our product"), $Seq);
				  		$result['retcode'] = EN_RET_CODE__NOT_FIND_CURDATA;
				  		$result['info'] = "device not in our product";
				        return false;
				    }	    	
				    if(1 == $account_product_response['Items'][0]['status']['N']) 							// 帐号已绑定
			    	{
			    		CLog::LOG_ERROR(array(__FILE__, __LINE__, "product already binding, account_product_response = ".$account_product_response), $Seq);
				  		$result['retcode'] = EN_RET_CODE__CURDATA_ALREADY_BINDING;
				  		$result['info'] = "product already binding";
						return false;
			    	}
			    	if($register_info['device'] != $account_product_response['Items'][0]['device']['S'])
			    	{
			    		CLog::LOG_ERROR(array(__FILE__, __LINE__, "not same device, account_product_response = ".$account_product_response), $Seq);
						$result['retcode'] = EN_RET_CODE__INVAIL_EMAIL_REGISTER;
				  		$result['info'] = "not same device";
						return false;
			    	}
		        }
        		return true;
        	}
        	else
        	{
				CLog::LOG_ERROR(array(__FILE__, __LINE__, "email already register, ".$register_info['email']), $Seq);
				// todo: 该email已经被注册过了
				$result['retcode'] = EN_RET_CODE__EMAIL_ALREADY_REGISTER;
				$result['info'] = "email already register, ".$register_info['email'];
				return false;
        	}
		}
		else
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "th_id already register, ".$register_info['th_id']), $Seq);
			// todo: 该th_id已经被注册过了
			$result['retcode'] = EN_RET_CODE__THIRD_ALREADY_REGISTER;
			$result['info'] = "th_id already register, ".$register_info['th_id'];
			return false;
		}
	}	

}



?>