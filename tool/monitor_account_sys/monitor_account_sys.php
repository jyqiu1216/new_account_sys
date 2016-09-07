<?php
    require_once dirname(__FILE__).'/config.php';

    function GetMicroSecond() 
    {
	    list($t1, $t2) = explode(' ', microtime());     
	    return sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000000);  
    }	


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
            return array("ret_code" => $ret_code, "resultdata" => $data);
        }
        else
        {            
            $ret_code = $Resultdata->{"res_header"}->{"ret_code"};
            return array("ret_code" => $ret_code, "resultdata" => $Resultdata);
        }    
    }


    unlink(normal_time_cost);
    unlink(expect_time_cost);
    
    $en_account_prefix = "account.simpysam.com/account/index_en.php?";
    $en_check_account_status_interface = "request=".encrypt_url_php("lrct=10&reqno=1&time=".time()."&vs=1.0&sy=sy&dt=dt&did=did&pid=1032609522&lg=0&idfa=did&sid=0&uid=0&rid=&cidx=0&aid=0&sn=1&pg=0&pp=20&lang=0&npc=0&sbox=0&platform=IOS&command=check_account_status");
    $en_check_account_status_url = $en_account_prefix.$en_check_account_status_interface;
     
    $error_count = 0;
    $total_time = 0;
    for($index = 0; $index < detect_count; $index++)
    {
        $begin_time = GetMicroSecond();
        $result = GetService($en_check_account_status_url);
        $end_time = GetMicroSecond();
        $diff_time = $end_time - $begin_time;
        usleep(internal_time * 1000000);
        if(10000 == $result['ret_code'])
        {
            $error_count++;
        }
        $total_time = $total_time + $diff_time;
    }
    $avg_delay = $total_time / detect_count;
    if(0 != $error_count)
    {
        file_put_contents("./error_record", "time = ".time().", detect_count = ".detect_count.", error_count = ".$error_count.", avg_delay = ".$avg_delay."us\n", FILE_APPEND);
    }
    
    if($error_count >= error_ratio * detect_count)
    {
        file_put_contents("./warnning_file", "account sys req error: detect_count = ".detect_count.", error_count = ".$error_count.", avg_delay = ".$avg_delay."us\n", FILE_APPEND);
    }
    
?>

