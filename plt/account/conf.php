<?php

define("project", "account");

// aws相关配置
define("aws_key", "AKIAPSCE56RMEYAIMHBA");
define("aws_scecret", "fnoc/jFS6ZimF08DI+ntku90P6szYIqcAxtrobdz");
define("aws_region", "cn-north-1");

define("account_mail_sender", "account@leyinetwork.com");

// 帐号系统所需的表名
define("account_user_tbl", 		project."_user");
define("account_product_tbl", 	project."_product");

// 帐号系统的前缀
define("account_en_prefix", "http://54.223.156.94:9090/op.simpysam.com/account_sys/account/index_en.php?");
define("account_de_prefix", "http://54.223.156.94:9090/op.simpysam.com/account_sys/account/index_de.php?");

// 帐号系统页面版的前缀
define("web_account_prefix", "http://54.223.156.94:9090/op.simpysam.com/account_sys/page/public/index.php?");

// 发送邮箱的文案
define("account_mail_json_path", serialize(array(0 => "./data/account_mail_english.json")));

// 返回码
define("EN_RET_CODE__SYSTEM_ERROR", 				40000);
define("EN_RET_CODE__REQ_PARAMS_ERROR", 			40001);
define("EN_RET_CODE__NOT_FIND_CURDATA", 			40002);
define("EN_RET_CODE__CURDATA_ALREADY_BINDING", 		40003);
define("EN_RET_CODE__INVAIL_EMAIL_REGISTER", 		40004);
define("EN_RET_CODE__EMAIL_ALREADY_REGISTER", 		40005);
define("EN_RET_CODE__EMAIL_NOT_REGISTER", 			40006);
define("EN_RET_CODE__PASSWD_ERROR", 				40007);
define("EN_RET_CODE__INVAILD_LOGIN", 				40008);
define("EN_RET_CODE__EMAIL_NOT_BINDING_ANYDATA", 	40009);
define("EN_RET_CODE__EMAIL_NOT_BINDING_CURDATA", 	40010);
define("EN_RET_CODE__EMAIL_URL_EXPIRED", 			40011);
define("EN_RET_CODE__EMAIL_INVAILD_REQ", 			40012);
define("EN_RET_CODE__EMAIL_ALREADY_BINDING_DATA", 	40013);
// 第三方相关的返回码
define("EN_RET_CODE__THIRD_ALREADY_REGISTER", 		40014);
define("EN_RET_CODE__THIRD_NOT_REGISTER", 			40015);
define("EN_RET_CODE__THIRD_ALREADY_BINDING_DATA", 	40016);
define("EN_RET_CODE__THIRD_STRING_ERROR", 			40017);
define("EN_RET_CODE__THIRD_NOT_BINDING_ANYDATA", 	40018);
define("EN_RET_CODE__THIRD_NOT_BINDING_CURDATA", 	40010);

define("leyi_name", "Leyi");
define("product_project_info", serialize(array("1" => 
											array(0 => "gt", 1 => "War Ages", 2 => "http://54.223.156.94:12510/?")
										  	  )
										)
	  );

define("pid_map", serialize(array("1032609522" => "1", 
								  "1065845844" => "1"
								  )
							)
	  );

class CConf
{
	public static function GetProject($r_pid)
	{
		$product_project_info = unserialize(product_project_info)[$r_pid];
		if(null == $product_project_info)
		{
			$product_project_info = array(0 => "other", 1 => "other", 2 => "other");
		}
		return $product_project_info;
	}

	public static function GetAccountMailJson($lang)
	{
		$account_mail_json_path = unserialize(account_mail_json_path)[$lang];
		if(null == $account_mail_json_path)
		{
			$account_mail_json_path = "./data/other.json";
		}
		return $account_mail_json_path;
	}

	public static function GetRealPid($pid)
	{
		$pid_map = unserialize(pid_map)[$pid];
		if(null == $pid_map)
		{
			$pid_map = "-1";
		}
		return $pid_map;
	}

}

?>
