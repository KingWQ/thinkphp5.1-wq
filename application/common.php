<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
if(!function_exists('app_log')){
    function app_log($data = '', $logFileName = '') {
        $date =date('Y-m-d',time());
        $dir = './../runtime/request_log/' . $date.$logFileName . '.log';

        if (!is_dir('./../runtime/request_log/')){
            mkdir('./../runtime/request_log/', 0777, true);
        }

        $logData = [
            'log_time' => date('Y-m-d H:i:s', time()),
            'log_data' => $data,
        ];
        error_log(var_export($logData, TRUE), 3, $dir);
    }
}
if(!function_exists('curl_get')){
    function curl_get(string $url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //不做证书校验,部署在linux环境下请改为true
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $file_contents = curl_exec($ch);
        curl_close($ch);
        return $file_contents;
    }
}

if(!function_exists('curl_post')){
    function curl_post(string $url, array $params = []) {
        $data_string = json_encode($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')
        );
        $data = curl_exec($ch);
        curl_close($ch);
        return ($data);
    }
}

