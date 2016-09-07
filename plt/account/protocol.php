<?php

require_once './common.php';

/*
(1) 加密(无换行符)
{
    "res_header":
    {
        "ret_code":0,                   // 全局错误码,按来源区分：10000-19999为客户端错误码，20000-29999为op错误码，30000-99999为svr错误码
        "cost_time_us":20,              // 耗时，单位us
        "ret_time_s":1421065194,        // 当前结果返回的svr时间,单位秒——只用于调试
        "sid":0,                        // svr id
        "module":"${module_name}"       // ret_code非0时生效,表示出错的datatype (格式为字符串)
    },
    "res_data":
    [
        // 元数据节点
        {
            "key":"[${table_name}:${md5}:${time}:${en_flag}:${data_len}]",  // 格式为字符串
            "data":"dafdadkljfadkljflkajdlkfjalkjklfad"                     // 格式为字符串
        },
        // 元数据节点
        {
            "key":"[${table_name}:${md5}:${time}:${en_flag}:${data_len}]",
            "data":"dafdadkljfadkljflkajdlkfjalkjklfad"
        }
    ]
}

(2) 非加密(无换行符)
{
    "res_header":
    {
        "ret_code":0,                   // 全局错误码,按来源区分：10000-19999为客户端错误码，20000-29999为op错误码，30000-99999为svr错误码
        "cost_time_us":20,              // 耗时，单位us
        "ret_time_s":1421065194,        // 当前结果返回的svr时间,单位秒——只用于调试
        "sid":0,                        // svr id
        "module":"${module_name}"       // ret_code非0时生效,表示出错的datatype (格式为字符串)
    },
    "res_data":
    [
        // 元数据节点
        {
            "key":"[${table_name}:${md5}:${time}:${en_flag}:${data_len}]",  // 格式为字符串
            "data":[
                {
                    "key":"[${table_name}:${md5}:0:0:${data_len}]",
                    "data": 具体的元数据内容(格式为json：元组/数组/字段/数值/null/...)
                },
                {
                    "key":"[${table_name}:${md5}::0:0:${data_len}]",
                    "data": 具体的元数据内容(格式为json：元组/数组/字段/数值/null/...)
                }
            ]       
        },
        // 元数据节点
        {
            "key":"[${table_name}:${md5}:${time}:${en_flag}:${data_len}]", 
            "data":[
                {
                    "key":"[${table_name}:${md5}:0:0:${data_len}]",
                    "data": 具体的元数据内容
                },
                {
                    "key":"[${table_name}:${md5}:0:0:${data_len}]",
                    "data": 具体的元数据内容
                }
            ]
        }
    ]
}
*/

// 手写输出是为了保证需要符合预期的输出顺序
Class CProtocol
{
	// 转换数据节点为字符串
	// $DataNodeArray: 数据节点数组
	public static function DataNodeArrayToString($DataNodeArray)
	{
		$ret = "";
		$DataNodeString = "";
		$num = count($DataNodeArray);
		for($i = 0; $i < $num; $i++)
		{
		    if($i == $num - 1)
		    {
		        $DataNodeString .= $DataNodeArray[$i];
		    }
		    else
		    {
		        $DataNodeString .= $DataNodeArray[$i].',';
		    }
		}
		$ret = '['.$DataNodeString.']';
		return $ret;
	}


	// 返回数据节点
	// $table_name: 数据节点的表名
	// $data: 数据内容
	// $need_encrypt: 加密标记(0-非加密; 1-加密)
	// $need_ret_timne: 是否需要填充请求时间戳(0-不需要; 1-需要)
	public static function GenDataNode($table_name, $data, $md5, $need_encrypt, $need_ret_time)
	{
		if("" == $data)
		{
			$data = "null";
		}
		$ret = "";
		$key_str = "";
		$data_str = "";
		$data_content = "";
		$data_len = 0;
		$encode_flag = 0;
		if(1 == $need_encrypt)
		{
			// Encrypt函数加密之后会自动添加""
			$data_content = CCommon::Encrypt($data);
		}
		else
		{
			$data_content = $data;
		}
		$data_len = strlen($data_content);
		$data_str = '"data":'.$data_content;

		$encode_flag = $need_encrypt;

		if(0 == $need_ret_time)
		{
			$key_str = '"key":'.'"['.$table_name.':'.$md5.':0:'.$encode_flag.':'.$data_len.']"';
		}
		else
		{
			$key_str = '"key":'.'"['.$table_name.':'.$md5.':'.time().':'.$encode_flag.':'.$data_len.']"';
		}	
		
		$ret = '{'.$key_str.','.$data_str.'}';

		return $ret;

	}


	// 返回主体协议
	// $ret_arr: 数据内容array
	/* 
		{
			"key": "[${table_name}:${md5}:${time}:${en_flag}:${data_len}]",
			"data": 加密: "abcd"; 非加密: json格式)
		}
	*/
	// $sid: 服号
	// $module: 数据module的名字
	// $cost_time: 处理请求所需时间
	// $ret_code: 处理请求的返回码
	public static function ReturnResponse($ret_arr, $sid, $uid, $module, $cost_time, $ret_code)
	{	
		$ret = "";
		$ReturnData = "";
		$num = count($ret_arr);
		for($i = 0; $i < $num; $i++)
		{
		    if($i == $num - 1)
		    {
		        $ReturnData .= $ret_arr[$i];
		    }
		    else
		    {
		        $ReturnData .= $ret_arr[$i].',';
		    }
		}

		$ret = '{"res_header":{"ret_code":'.$ret_code.',"cost_time_us":'.$cost_time.',"ret_time_s":'.time().',"sid":'.$sid.',"uid":'.$uid.',"module":"'.$module.'"},';
		$ret .= '"res_data":['.$ReturnData.']}';

		return $ret;
	}

	public static function ReturnJsonData($module, $table, $table_node, $result, $CostTime, $ret_code)
	{

		$ResultDataArray = array();				
	    $md5DataNode = md5($result);    
	    array_push($ResultDataArray, CProtocol::GenDataNode($table_node, $result, $md5DataNode, 0, 0));

		$ret_arr = array();
        $DataNodeString = CProtocol::DataNodeArrayToString($ResultDataArray);
        $md5DataNode = md5($DataNodeString);
        array_push($ret_arr, CProtocol::GenDataNode($table, $DataNodeString, $md5DataNode, en_flag, 1));

        header("Content-Type: text/html; charset=UTF-8");
        echo CProtocol::ReturnResponse($ret_arr, 0, 0, $module, $CostTime, $ret_code);

	}


}




?>
