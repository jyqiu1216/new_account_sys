<?php


// 日志输出: $OutPutArray数组格式(文件的绝对路径+名字(__FILE__), 当前行号(__LINE__), 待输出的字符串)
Class CLog
{
	public static function LOG_INFO($OutPutArray, $seq)
	{   
	    SeasLog::log(SEASLOG_INFO, $OutPutArray[0].":".$OutPutArray[1]." ".$OutPutArray[2]." [seq=".$seq."]");
	}
	public static function LOG_DEBUG($OutPutArray, $seq)
	{   
	    SeasLog::log(SEASLOG_DEBUG, $OutPutArray[0].":".$OutPutArray[1]." ".$OutPutArray[2]." [seq=".$seq."]");
	}
	public static function LOG_ERROR($OutPutArray, $seq)
	{   
	    SeasLog::log(SEASLOG_ERROR, $OutPutArray[0].":".$OutPutArray[1]." ".$OutPutArray[2]." [seq=".$seq."]");
	}
}



?>
