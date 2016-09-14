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
Class CLandingLogic
{

	// ========================================================================================== //
	public static function landing_update($HttpParams, $Seq)
	{
		$BeginReqTime = CCommon::GetMicroSecond();
		$result = array();	

		$app_uid = $HttpParams['uid'];
		$pid = $HttpParams['pid'];
		$device = $HttpParams['did'];
		$idfa = $HttpParams['idfa'];
		$sy = $HttpParams['sy'];
		$vs = $HttpParams['vs'];
		$game_platform = $HttpParams['platform'];
		$r_pid = $HttpParams['r_pid'];

		if(null == $r_pid)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "r_pid is null"), $Seq);

	    	$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	    	// todo: 参数错误的返回码
	        $result['retcode'] = -1;
	        $result['info'] = "r_pid is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
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
		if(null == $idfa)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "idfa is null"), $Seq);

	    	$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	    	// todo: 参数错误的返回码
	        $result['retcode'] = -1;
	        $result['info'] = "idfa is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
		}
		if(null == $sy)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "sy is null"), $Seq);

	    	$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	    	// todo: 参数错误的返回码
	        $result['retcode'] = -1;
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
	        $result['retcode'] = -1;
	        $result['info'] = "vs is null";
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



		$dbClient = CAwsDb::GetInstance()->GetDbClient();
		$account_product_response = array();

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
			// todo: 内部数据有误的返回码
	  		$result['retcode'] = -1;
	  		$result['info'] = "query product data exception: ".$e->getMessage();
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
        }
		if(0 == $account_product_response['Count'])
		{
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "app_uid = ".$app_uid.", r_pid = ".$pid."_".$game_platform." not product data"), $Seq);
			// todo: 找不到相应的产品数据的返回码
	  		$result['retcode'] = -1;
	  		$result['info'] = "app_uid = ".$app_uid.", r_pid = ".$pid."_".$game_platform." not product data";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
	    }


		$product_info = json_decode($account_product_response['Items'][0]['product_info']['S'], true);
    	$product_info['pid'] = $pid;
    	$product_info['device'] = $device;
    	$product_info['idfa'] = $idfa;
    	$product_info['sy'] = $sy;
    	$product_info['vs'] = $vs;
    	$product_info['platform'] = $game_platform;
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
           		"lgtime" => array(
                    "Action" => AttributeAction::PUT,
                    "Value" => array(
                        Type::NUMBER => time()
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
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "upadte product data exception: ".$e->getMessage()), $Seq);
			// todo: 内部数据有误的返回码
	  		$result['retcode'] = -1;
	  		$result['info'] = "upadte product data exception: ".$e->getMessage();
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
        }	    

		CLog::LOG_INFO(array(__FILE__, __LINE__, "landing update success"), $Seq);
        $EndReqTime = CCommon::GetMicroSecond();
        $CostTime = $EndReqTime - $BeginReqTime;
		$result['retcode'] = 0;
		$result['info'] = "landing update success";
		echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "account_user_node", json_encode($result['info']), $CostTime, $result['retcode']);
		return true;

	}

	// ========================================================================================== //
	// command = landing
	// key0 = ${rid}
	// key1 = ${email}
	// key2 = ${passwd}
	// key3 = ${th_id}
	// key4 = ${device}
	// key5 = ${pid}
	// key6 = ${game_platform}
	// key7 = ${sy}
	// key8 = ${vs}
	// key9 = ${idfa}
	public static function landing($HttpParams, $Seq)
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
	        $result['retcode'] = -1;
	        $result['info'] = "params not incomplete";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;
		}
		$rid = $HttpParams['key0'];
		$email = strtolower($HttpParams['key1']);
		$passwd = $HttpParams['key2'];
		$th_id = $HttpParams['key3'];
		$device = $HttpParams['did'];
		$pid = $HttpParams['pid'];
		$game_platform = $HttpParams['platform'];
		$sy = $HttpParams['sy'];
		$vs = $HttpParams['vs'];
		$idfa = $HttpParams['idfa'];
		$r_pid = $HttpParams['r_pid'];

		if(null == $r_pid)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "r_pid is null"), $Seq);
	    	
	 		$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
	        // todo: 参数错误的返回码
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
	        // todo: 参数错误的返回码
	        $result['retcode'] = -1;
	        $result['info'] = "device is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;	
		}
		if(null == $pid)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "pid is null"), $Seq);
	        // todo: 参数错误的返回码
	        $result['retcode'] = -1;
	        $result['info'] = "pid is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;		
		}
		if(null == $game_platform)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "game_platform is null"), $Seq);
	        // todo: 参数错误的返回码
	        $result['retcode'] = -1;
	        $result['info'] = "game_platform is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;	
		}
		if(null == $sy)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "sy is null"), $Seq);
	        // todo: 参数错误的返回码
	        $result['retcode'] = -1;
	        $result['info'] = "sy is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;		
		}
		if(null == $vs)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "vs is null"), $Seq);
	        // todo: 参数错误的返回码
	        $result['retcode'] = -1;
	        $result['info'] = "vs is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;		
		}
		if(null == $idfa)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "idfa is null"), $Seq);
	        // todo: 参数错误的返回码
	        $result['retcode'] = -1;
	        $result['info'] = "idfa is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
	        return false;		
		}

		$landing_flag = false;
		if(null != $email 				// email登录
			&& null != $passwd
			&& null != $rid
			&& null == $th_id)
		{
			CLog::LOG_INFO(array(__FILE__, __LINE__, "email landing, email = ".$email), $Seq);
			$landing_info = array();
			$landing_info['email'] = $email;
	    	$landing_info['passwd'] = $passwd;
	    	$landing_info['rid'] = $rid;
	    	$landing_info['device'] = $device;
	    	$landing_info['pid'] = $pid;
	    	$landing_info['game_platform'] = $game_platform;
	    	$landing_info['sy'] = $sy;
	    	$landing_info['vs'] = $vs;
	    	$landing_info['idfa'] = $idfa;
	    	$landing_info['r_pid'] = $r_pid;
			$ret = CLandingLogic::landing_email($landing_info, $result, $Seq);
			if(true == $ret)
			{
				$landing_flag = true;
				CLog::LOG_INFO(array(__FILE__, __LINE__, "email landing sucess, email = ".$email), $Seq);			
			}
		}
		else if(null == $email 			// 第三方登录
			&& null == $passwd
			&& null != $rid
			&& null != $th_id)
		{
			CLog::LOG_INFO(array(__FILE__, __LINE__, "th_id landing, th_id = ".$th_id), $Seq);
			$landing_info = array();
	    	$landing_info['th_id'] = $th_id;
	    	$landing_info['rid'] = $rid;
	    	$landing_info['device'] = $device;
	    	$landing_info['pid'] = $pid;
	    	$landing_info['game_platform'] = $game_platform;
	    	$landing_info['sy'] = $sy;
	    	$landing_info['vs'] = $vs;
	    	$landing_info['idfa'] = $idfa;
	    	$landing_info['r_pid'] = $r_pid;
			$ret = CLandingLogic::landing_th_id($landing_info, $Seq);
			if(0 == $ret)
			{
				$landing_flag = true;
				CLog::LOG_INFO(array(__FILE__, __LINE__, "th_id landing sucess, th_id = ".$th_id), $Seq);			
			}
		}
		else 							// 本机登录
		{
			CLog::LOG_INFO(array(__FILE__, __LINE__, "local landing"), $Seq);
			$landing_info = array();
	    	$landing_info['device'] = $device;
	    	$landing_info['pid'] = $pid;
	    	$landing_info['game_platform'] = $game_platform;
	    	$landing_info['sy'] = $sy;
	    	$landing_info['vs'] = $vs;
	    	$landing_info['idfa'] = $idfa;
	    	$landing_info['r_pid'] = $r_pid;
			$ret = CLandingLogic::landing_local($landing_info, $result, $Seq);
			if(true == $ret)
			{
				$landing_flag = true;
				CLog::LOG_INFO(array(__FILE__, __LINE__, "local landing"), $Seq);			
			}
		}

		if(true == $landing_flag)
		{
			CLog::LOG_INFO(array(__FILE__, __LINE__, "landing success"), $Seq);
			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			echo CProtocol::ReturnJsonData("account_user_data", "account_user_tbl", "account_user_node", json_encode($result['info']), $CostTime, $result['retcode']);
			return true;
		}
		else
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "landing failed"), $Seq);
			$EndReqTime = CCommon::GetMicroSecond();
	        $CostTime = $EndReqTime - $BeginReqTime;
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
		}


	}

	// ========================================================================================== //
	// function ==> email登录
	private static function landing_email($landing_info, &$result, $Seq)
	{
		$dbClient = CAwsDb::GetInstance()->GetDbClient();
		$account_user_response = array();
		$account_product_response = array();

		// 查询email是否存在
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
		                    array(Type::STRING => $landing_info['rid'])
		                )
		            )
		        )
		    ));
		}
		catch(DynamoDbException $e)
        {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query user data exception(email): ".$e->getMessage()), $Seq);
			// todo: 内部数据有误的返回码
	  		$result['retcode'] = -1;
	  		$result['info'] = "query user data exception(email): ".$e->getMessage();
	        return false;
        }

	    if(0 == $account_user_response['Count'])
	    {
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "rid = ".$landing_info['rid']." not find"), $Seq);
			// todo: rid帐号没找到的返回码
	  		$result['retcode'] = 1;
	  		$result['info'] = "rid = ".$landing_info['rid']." not find";
	        return true;
	    }
	    if($landing_info['email'] != $account_user_response['Items'][0]['email']['S'])
	    {
    		CLog::LOG_ERROR(array(__FILE__, __LINE__, "email is not the rid email, account_user_response = ".$account_user_response), $Seq);
			// todo: email与帐号不一致的返回码
	  		$result['retcode'] = 2;
	  		$result['info'] = "email is not the rid email";
	        return true;
	    }
	 	if($landing_info['passwd'] != $account_user_response['Items'][0]['passwd']['S'])
	    {
    		CLog::LOG_ERROR(array(__FILE__, __LINE__, "email passwd wrong, account_user_response = ".$account_user_response), $Seq);
			// todo: email的密码不正确的返回码
	  		$result['retcode'] = 3;
	  		$result['info'] = "email passwd wrong";
	        return true;
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
		                    array(Type::STRING => $landing_info['rid'])
		                )
		            )
		        )
		    ));
		}
		catch(DynamoDbException $e)
        {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query product data exception(email): ".$e->getMessage()), $Seq);
			// todo: 内部数据有误的返回码
	  		$result['retcode'] = -1;
	  		$result['info'] = "query product data exception(email): ".$e->getMessage();
	        return false;
        }
	    if(0 == $account_product_response['Count'])
	    {
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "rid = ".$landing_info['rid']." not have product data"), $Seq);
			// todo: 该帐号没有任何游戏数据的返回码
	  		$result['retcode'] = 4;
	  		$result['info'] = "rid = ".$landing_info['rid']." not have product data";
	        return true;
	    }
	    $product_response_info = array();
	   	$product_exist_count = 0;
 		foreach($account_product_response['Items'] as $product_response)
 		{
            if($product_response['r_pid']['S'] != $landing_info['r_pid'])
            {
               	continue;         	
            }
     		if(null == $product_response['product_info']['S'])
	    	{
	    		continue; 
	    	}
	    	// 被绑定过的帐号不属于本机登录
	    	if(0 == $product_response['status']['N'])
	    	{
	    		continue; 
	    	}
	    	$product_exist_count++;
	    	$product_response_info = $product_response;
        }
      	if($product_exist_count > 1)
        {
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "banding product data exist more, product_exist_count = ".$product_exist_count), $Seq);
			// todo: 内部数据有误的返回码
	  		$result['retcode'] = -1;
	  		$result['info'] = "banding product data exist more, product_exist_count = ".$product_exist_count;
	        return false;
        }

        if(1 == $product_exist_count)
        {
	    	CLog::LOG_INFO(array(__FILE__, __LINE__, "get product data"), $Seq);

			// 去后台拿用户数据
	    	// 返回uid
	  		$result['retcode'] = 5;
	  		$result['info']['app_uid'] = $product_response_info['app_uid']['S'];
	  		$result['info']['r_pid'] = $product_response_info['r_pid']['S'];
	  		$result['info']['msg'] = "get product data";
	        return true;

	    
        }
        else
        {
	    	// 去后台拿fake数据
			// todo: 该帐号没有当前游戏数据的返回码
	  		$result['retcode'] = 6;
	  		$result['info'] = "rid = ".$landing_info['rid']." not have product data";
	        return true;

        }
	}

	// ========================================================================================== //
	// function ==> 第三方登录
	private static function landing_th_id($landing_info, $Seq)
	{
		// 检验第三方注册的参数正确性
		if(null == $landing_info['th_id'])
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "th_id is null"), $Seq);
	        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        $result['info'] = "th_id is null";
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;

		}

		$th_id_arry = explode(':', $landing_info['th_id']);
		if(null == $th_id_arry[0]
			|| null == $th_id_arry[1])
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "th_id is invaild, th_id = ".$landing_info['th_id']), $Seq);
	        $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
	        $result['info'] = "th_id is invaild, th_id = ".$landing_info['th_id'];
			echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
			return false;
		}
		$th_id_name = $th_id_arry[0];
		$th_id_id = $th_id_arry[1];


		$dbClient = CAwsDb::GetInstance()->GetDbClient();
		$account_user_response = array();
		$account_product_response = array();

		// 查询th_id是否存在
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
		                    array(Type::STRING => $landing_info['rid'])
		                )
		            )
		        )
		    ));
		}
		catch(DynamoDbException $e)
        {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query user data exception(rid): ".$e->getMessage()), $Seq);
			// todo: 内部数据有误的返回码
	  		$result['retcode'] = -1;
	  		$result['info'] = "query user data exception(rid): ".$e->getMessage();
	        return false;
        }

	    if(0 == $account_user_response['Count'])
	    {
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "rid = ".$landing_info['rid']." not found"), $Seq);
			// todo: rid帐号没找到的返回码
	  		$result['retcode'] = 1;
	  		$result['info'] = "rid = ".$landing_info['rid']." not find";
	        return true;
	    }
	    if(null == $account_user_response['Items'][0][$th_id_name])
	    {
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "th_id = ".$landing_info['th_id']." not register"), $Seq);
			// todo: rid没有绑th_id的返回码
	  		$result['retcode'] = 2;
	  		$result['info'] = "email = ".$landing_info['email']." not register";
	        return true;
	    }
	    if($th_id_id != $account_user_response['Items'][0][$th_id_name]['S'])
	    {
    		CLog::LOG_ERROR(array(__FILE__, __LINE__, "th_id is not the rid th_id, account_user_response = ".$account_user_response), $Seq);
			// todo: third不一致的返回码
	  		$result['retcode'] = 3;
	  		$result['info'] = "th_id is not the rid th_id";
	        return true;
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
		                    array(Type::STRING => $landing_info['rid'])
		                )
		            )
		        )
		    ));
		}
		catch(DynamoDbException $e)
        {
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "query product data exception(email): ".$e->getMessage()), $Seq);
			// todo: 内部数据有误的返回码
	  		$result['retcode'] = -1;
	  		$result['info'] = "query product data exception(email): ".$e->getMessage();
	        return false;
        }
	    if(0 == $account_product_response['Count'])
	    {
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "rid = ".$landing_info['rid']." not have product data"), $Seq);
			// todo: 该帐号没有任何游戏数据的返回码
	  		$result['retcode'] = 4;
	  		$result['info'] = "rid = ".$landing_info['rid']." not have product data";
	        return true;
	    }
	    $product_response_info = array();
	    $product_exist_count = 0;
 		foreach($account_product_response['Items'] as $product_response)
 		{
            if($product_response['r_pid']['S'] != $landing_info['r_pid'])
            {
               	continue;         	
            }
     		if(null == $product_response['product_info']['S'])
	    	{
	    		continue; 
	    	}
	    	// 被绑定过的帐号不属于本机登录
	  		if(0 == $product_response['status']['N'])
	    	{
	    		continue; 
	    	}
	    	$product_exist_count++;
	    	$product_response_info = $product_response;
        }
      	if($product_exist_count > 1)
        {
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "banding product data exist more, product_exist_count = ".$product_exist_count), $Seq);
			// todo: 内部数据有误的返回码
	  		$result['retcode'] = -1;
	  		$result['info'] = "banding product data exist more, product_exist_count = ".$product_exist_count;
	        return false;
        }

        if(1 == $product_exist_count)
        {
	    	CLog::LOG_INFO(array(__FILE__, __LINE__, "get product data"), $Seq);

			// 去后台拿用户数据
	    	// 返回uid
	  		$result['retcode'] = 5;
	  		$result['info']['app_uid'] = $product_response_info['app_uid']['S'];
	  		$result['info']['r_pid'] = $product_response_info['r_pid']['S'];
	  		$result['info']['msg'] = "get product data";
	        return true;

        }
        else
        {
	    	// 去后台拿fake数据
			// todo: 该帐号没有当前游戏数据的返回码
	  		$result['retcode'] = 6;
	  		$result['info'] = "rid = ".$landing_info['rid']." not have product data";
	        return true;
        }
	}

	// ========================================================================================== //
	// function ==> 本机登录
	private static function landing_local($landing_info, &$result, $Seq)
	{
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
		                    array(Type::STRING => $landing_info['device'])
		                )
		            )
		        )
		    ));
		}
		catch(DynamoDbException $e)
        {
        	CLog::LOG_ERROR(array(__FILE__, __LINE__, "query product data exception(email): ".$e->getMessage()), $Seq);
			// todo: 内部数据有误的返回码
	  		$result['retcode'] = -1;
	  		$result['info'] = "query product data exception(email): ".$e->getMessage();
	        return false;
        }

       	$product_exist_count = 0;
       	$product_response_info = array();
	    if(0 == $account_product_response['Count'])
	    {
	    	CLog::LOG_INFO(array(__FILE__, __LINE__, "device = ".$landing_info['device']." not register"), $Seq);
	    }
	    else
	    {
	    	foreach($account_product_response['Items'] as $product_response)
	 		{
	            if($product_response['r_pid']['S'] != $landing_info['r_pid'])
	            {
	               	continue;         	
	            }
	     		if(null == $product_response['product_info']['S'])
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
	    }

        if($product_exist_count > 1)
        {
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "not banding product data exist more, product_exist_count = ".$product_exist_count), $Seq);
			// todo: 内部数据有误的返回码
	  		$result['retcode'] = -1;
	  		$result['info'] = "not banding product data exist more, product_exist_count = ".$product_exist_count;
	        return false;
        }

        if(1 == $product_exist_count)
        {
	    	CLog::LOG_INFO(array(__FILE__, __LINE__, "get product data"), $Seq);

	    	// 去后台拿用户数据
	    	// 返回uid
	  		$result['retcode'] = 1;
	  		$result['info']['app_uid'] = $product_response_info['app_uid']['S'];
	  		$result['info']['r_pid'] = $product_response_info['r_pid']['S'];
	  		$result['info']['msg'] = "get product data";
	        return true;
        }
        else
        {
	    	CLog::LOG_INFO(array(__FILE__, __LINE__, "create product data"), $Seq);


			// 拿后台fake数据标识
	    	$result['retcode'] = 2;
	  		$result['info']['msg'] = "create product data";
	        return true;
        }
	

	}

}



?>