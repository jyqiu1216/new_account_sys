<?php

class CCommon
{
	public static function GetMicroSecond() 
	{
	    list($t1, $t2) = explode(' ', microtime());     
	    return sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000000);  
	}	

	public static function consloe_out($consloe_msg)
	{
		echo "<br>";
		echo $consloe_msg;
	}
	
	public static function Encrypt($content)
	{
	    $rsp = $content;
	    if (en_flag == 1)
	    {
	        $rsp = '"'.encrypt_content_php($content).'"';
	    }
	    return $rsp;
	}	
}

class CHttpParam
{
	public static function CheckReqUrl($HttpParams, $Seq)
	{
		// 1. 检验请求是否有command字段
		if(!array_key_exists("command", $HttpParams))
	    {
	    	CLog::LOG_ERROR(array(__FILE__, __LINE__, "not command define"), $Seq);
	        return -1;
		}

		// 2. 检验请求是否存在time字段
		if(!array_key_exists("time", $HttpParams))
	    {
	   		CLog::LOG_ERROR(array(__FILE__, __LINE__, "url not have time filed"), $Seq);
	        return -2;
		}
		if(null == $HttpParams['time'])
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "time is null"), $Seq);
	        return -3;
		}

		// 3. url的md5校验,检查url来源是可靠性
		// to do
		if(false)
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "url unreliable"), $Seq);
	        return -4;
		}

		// 4. url的过期性检测
		// 4.2 普通请求的超时和超前
		if("login" == $HttpParams['command']
			|| "forget_passwd" == $HttpParams['command'])
		{	

		}
		else if((int)time() - 86400 > (int)$HttpParams['time'])
		{
			CLog::LOG_ERROR(array(__FILE__, __LINE__, "url expired"), $Seq);
	        return -5;
		}

		return 0;

	}
}

?>