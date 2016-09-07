<?php

require_once dirname(__FILE__).'/conf.php';

class Controller_Account extends Controller_Abstract
{

    function GetService($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$url");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        $Resultdata = json_decode($result);

        if(null == $Resultdata)
        {
            $ret_code = 10000;
            $data = "";
        }
        else
        {            
            $ret_code = $Resultdata->{"res_header"}->{"ret_code"};
        }    
        return array("ret_code" => $ret_code);

    }

    function actionSignup()
    {
        if($this->_context->isPOST()) 
        {
            $vaild_flag = true;
            $email = strtolower($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            $pattern = "/^([a-zA-Z0-9][\w\.\-]*)@(((([\w]+[\-]*[\w]+)|([\w]))\.)+)([a-zA-Z]+)$/";
            $password_pattern = "/^[0-9a-zA-Z\_]*$/";
            if(0 == preg_match($pattern, $email))
            {
                $this->_view['signup_result'] = "email invaild";
                $vaild_flag = false;
            }
            if(!(0 != preg_match($password_pattern, $password) && (6 <= strlen($password) && strlen($password) <= 15)))
            {
                $this->_view['signup_result'] = "password invaild, Please enter 6 to 15 characters with letters,numbers or underline.";
                $vaild_flag = false;
            }
            if($password != $confirm_password)
            {
                $this->_view['signup_result'] = "password not same";
                $vaild_flag = false;
            }
            if(true == $vaild_flag)
            {
                $param = "time=".time()."&pid=&platform=&did=&uid=&vs=&sy=&idfa=&command=register&key0=".urlencode($email)."&key1=".urlencode(strtoupper(md5($password."123!@#")))."&key2=&key3=leyi&key4=web";
                $register_url = account_en_prefix."request=".encrypt_url_php($param);
                $result = Controller_Account::GetService($register_url);
            
                usleep(500000);
                if(0 == $result['ret_code'])
                {
                    $this->_view['signup_result'] = "register success, check your mail and active the account!";
                }
                else if(40005 == $result['ret_code'])
                {
                    $this->_view['signup_result'] = "email already be register, please try again.";   
                }
                else
                {
                    $this->_view['signup_result'] = "register failed because of internal error, please try again.";
                }
            }
        }
        else
        {
            $this->_view['signup_result'] = "";
        }
    }


    function actionResetPasswd()
    {
        // 获取解密后的请求url
        $query_str = $_SERVER['QUERY_STRING'];

        if($this->_context->isPOST())
        {
            $vaild_flag = true;
            $email = strtolower($_POST['email']);
            $password = $_POST['password'];
            $passwordseq = $_POST['seq'];
            $pid = $_POST['pid'];
            $game_platform = $_POST['game_platform'];

            $password_pattern = "^[0-9a-zA-Z\_]*$";
            if(!(0 != ereg($password_pattern, $password) && (6 <= strlen($password) && strlen($password) <= 15)))
            {
                $this->_view['resetpasswd_result'] = "password invaild, Please enter 6 to 15 characters with letters,numbers or underline.";
                $this->_view['show_flag'] = 1;
                $vaild_flag = false;
            }

            if(true == $vaild_flag)
            {
                $param = "time=".time()."&command=reset_passwd&key0=".urlencode($email)."&key1=".urlencode(strtoupper(md5($password."123!@#")))."&key2=".$passwordseq."&key3=".$pid."&key4=".$game_platform;

                $reset_passwd_url = account_de_prefix."request=".$param;
                $result = Controller_Account::GetService($reset_passwd_url);
                usleep(500000);

                if(0 == $result['ret_code'])
                {
                    $this->_view['resetpasswd_result'] = "reset password success";
                }
                else
                {
                    $this->_view['resetpasswd_result'] = "reset password failed because of internal error, please try again.";
                }
                $this->_view['show_flag'] = 1;
            }
        }
        else
        {
            $request = explode('request=',$query_str);
            $request_str = "";

            if(null == strpos($request[1], "key1="))
            {
                $request_str = decrypt_url_php($request[1]);
            }
            else
            {
                $request_str = $request[1];
            }

            // 提取请求参数
            $request_params = explode('&', urldecode($request_str));
            $HttpParams = array();
            foreach($request_params as $key_value) 
            {
                $kv = explode('=', $key_value);
                $HttpParams[$kv[0]] = $kv[1];
            }

            if(null == $HttpParams['key0'])
            {
                $this->_view['resetpasswd_result'] = "reset password failed because of internal error, please try again.";
            }
            if(null == $HttpParams['key1'])
            {
                $this->_view['resetpasswd_result'] = "reset password failed because of internal error, please try again.";
            }

            $this->_view['email'] = strtolower($HttpParams['key0']);
            $this->_view['seq'] = $HttpParams['key1'];
            $this->_view['pid'] = $HttpParams['key2'];
            $this->_view['game_platform'] = $HttpParams['key3'];
            $this->_view['show_flag'] = 0;

        }

    }


}

?>
