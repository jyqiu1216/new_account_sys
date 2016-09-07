<?php 

    require_once dirname(__FILE__).'/seaslog.php';
    require_once dirname(__FILE__).'/conf.php';
    require_once dirname(__FILE__).'/common.php';
    require_once dirname(__FILE__).'/aws_s3_db.php';
    require_once dirname(__FILE__).'/register_logic.php';
    require_once dirname(__FILE__).'/active_logic.php';
    require_once dirname(__FILE__).'/landing_logic.php';
    require_once dirname(__FILE__).'/passwd_logic.php';
    require_once dirname(__FILE__).'/login_logic.php';
    require_once dirname(__FILE__).'/logout_logic.php';
    require_once dirname(__FILE__).'/binding_product_logic.php';
    require_once dirname(__FILE__).'/op_logic.php';

    // 该请求的seq号
    $Seq = CCommon::GetMicroSecond();

  	// 日志路径设置
    SeasLog::setBasePath(dirname(__FILE__));
    SeasLog::setLogger('seaslog');


    // 获取解密后的请求url
    $query_str = $_SERVER['QUERY_STRING'];
    $request = explode('request=',$query_str);
    $request_str = "";
    if(en_flag == 1)
    {
        $request_str = decrypt_url_php($request[1]);
    }
    else
    {
        $request_str = $request[1];
    }
    CLog::LOG_INFO(array(__FILE__, __LINE__, "originalurl=".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']."?request=".$request[1]), $Seq);
    CLog::LOG_INFO(array(__FILE__, __LINE__, "url=".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']."?request=".$request_str), $Seq);


    // 提取请求参数
    $request_params = explode('&', urldecode($request_str));
    $HttpParams = array();
    foreach($request_params as $key_value) 
    {
        $kv = explode('=', $key_value);
        $HttpParams[$kv[0]] = $kv[1];
    }

    if(null != $HttpParams['pid'])
    {
        $HttpParams['r_pid'] = CConf::GetRealPid($HttpParams['pid']);
        if("-1" == $HttpParams['r_pid'])
        {
            CCommon::consloe_out("Tag 1 Invalid Request! Please try again.");
            exit();
        }
    }


    $BeginReqTime = CCommon::GetMicroSecond();
    $result = array();
    $checkReqUrlFlag = false;
    $CheckReqUrlResult = 0;
    // 方便测试
    $CheckReqUrlResult = CHttpParam::CheckReqUrl($HttpParams, $Seq);
    switch($CheckReqUrlResult)
    {
        // 请求中不存在command字段
        case -1:
            $checkReqUrlFlag = false;
            // todo: 参数错误的返回码
            $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
            // $result['retcode'] = -1;
            $result['info'] = "not command define";
            break;
         // 请求中不存在time字段
        case -2:
            $checkReqUrlFlag = false;
            // todo: 参数错误的返回码
            $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
            // $result['retcode'] = -1;
            $result['info'] = "url not have time filed";
            break;
        // 请求中的time字段没有值
        case -3:
            $checkReqUrlFlag = false;
            // todo: 参数错误的返回码
            $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
            // $result['retcode'] = -1;
            $result['info'] = "time is null";
            break;
        // 请求是不可靠的
        case -4:
            $checkReqUrlFlag = false;
            // todo: 请求不可靠的的返回码
            $result['retcode'] = EN_RET_CODE__EMAIL_INVAILD_REQ;
            $result['info'] = "url unreliable";
            break;
        // 请求失效
        case -5:
            $checkReqUrlFlag = false;
            // todo: 请求过期的返回码
            $result['retcode'] = EN_RET_CODE__EMAIL_URL_EXPIRED;
            $result['info'] = "url expired";
            break;
        default:
            $checkReqUrlFlag = true;
            CLog::LOG_INFO(array(__FILE__, __LINE__, "check req_url success"), $Seq);
    }
    if(false == $checkReqUrlFlag)
    {
        CLog::LOG_ERROR(array(__FILE__, __LINE__, "CheckReqUrlResult = ".$CheckReqUrlResult), $Seq);
        /*
        $EndReqTime = CCommon::GetMicroSecond();
        $CostTime = $EndReqTime - $BeginReqTime;
        echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
        */
        if(EN_RET_CODE__EMAIL_URL_EXPIRED == $result['retcode'])
        {
            CCommon::consloe_out("The link has expired.");
        }
        else
        {
            CCommon::consloe_out("Tag 2 Invalid Request! Please try again.");
        }
        exit();
    }


    $AwsDbObject = CAwsDb::GetInstance();
    $AwsDbObject->InitAwsDb(aws_key, aws_scecret, aws_region, project);


	switch($HttpParams['command'])
	{
        // ****************************************** 登录 ****************************************** //
        // 帐号登录
        case "login":
            CLoginLogic::login($HttpParams, $Seq);
            break; 
        // 帐号注销(todo)
        case "logout":
            CLogoutLogic::logout($HttpParams, $Seq);
            break; 
        // ****************************************** 注册 ****************************************** //
        // 帐号注册
		case "register":
            CRegisterLogic::register($HttpParams, $Seq);
			break;  
        // 产品游客注册
        case "visitor_register":
            CRegisterLogic::visitor_register($HttpParams, $Seq);
            break;
        case "new_visitor_register":
            CRegisterLogic::new_visitor_register($HttpParams, $Seq);
            break;
        // ****************************************** 激活 ****************************************** //
        // 帐号激活
		case "active":
            CActiveLogic::active($HttpParams, $Seq);
			break;
        // ****************************************** 帐号关联 ****************************************** //
        // 绑定第三方到现有帐号(todo)
        case "binding_thrid_id":
            CActiveLogic::binding_thrid_id($HttpParams, $Seq);
            break;   
        // ****************************************** 产品登录 ****************************************** //
        // 产品登录
        case "landing":
            CLandingLogic::landing($HttpParams, $Seq);
        	break;
        // 登录状态更新
        case "landing_update":
            CLandingLogic::landing_update($HttpParams, $Seq);
            break;
        // ****************************************** 绑定产品 ****************************************** //
        // 绑定产品
        case "binding_product":
            CBindingProductLogic::binding_product($HttpParams, $Seq);
            break;
        // ****************************************** 密码相关 ****************************************** //            
        // 忘记密码         
        case "forget_passwd":
            CPasswdLogic::forget_passwd($HttpParams, $Seq);
        	break;
        // 更改密码 
		case "change_passwd":
            CPasswdLogic::change_passwd($HttpParams, $Seq);
			break;
        // 重置密码
        case "reset_passwd":
            CPasswdLogic::reset_passwd($HttpParams, $Seq);
            break;
        // ****************************************** OP操作 ****************************************** //  
        // 清除帐号
        case "clear_account":
            COpLogic::clear_account($HttpParams, $Seq);
            break;     
        // 检测帐号状态(0: 未注册,1: 未激活,2: 已激活,3. 帐号已修改,4. 多设备登录,是否有异常状态)
        case "check_account_status":
            COpLogic::check_account_status($HttpParams, $Seq);
            break;  
        // 获取帐号是否有修改密码
        case "get_account_passwd_status":
            COpLogic::get_account_passwd_status($HttpParams, $Seq);
            break; 
        // 清除当前设备的本机帐号
        case "new_game":
            COpLogic::new_game($HttpParams, $Seq);
            break;
        // 清除当前设备的本机帐号
        case "op_binding_product_email":
            COpLogic::op_binding_product_email($HttpParams, $Seq);
            break;
        case "get_player_now_sid":
            COpLogic::get_player_now_sid($HttpParams, $Seq);
            break;    
        case "change_sid":
            COpLogic::change_sid($HttpParams, $Seq);
            break;   
		default:
            CCommon::consloe_out("Invalid Request! Please try again.");
        /*
            $EndReqTime = CCommon::GetMicroSecond();
            $CostTime = $EndReqTime - $BeginReqTime;
            // todo: 参数错误的返回码
            $result['retcode'] = EN_RET_CODE__REQ_PARAMS_ERROR;
            // $result['retcode'] = -1;
            $result['info'] = "command not exist";
            echo CProtocol::ReturnJsonData("account_user_data", "error", "op_error", json_encode($result['info']), $CostTime, $result['retcode']);
        */


	}


?>