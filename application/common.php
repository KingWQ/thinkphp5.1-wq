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

//1：添加日志
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

//2：get请求
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

//3：post请求
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

//4：传递数据以易于阅读的样式格式化后输出
if(!function_exists('p')){
    function p($data){
        // 定义样式
        $str='<pre style="display: block;padding: 9.5px;margin: 44px 0 0 0;font-size: 13px;line-height: 1.42857;color: #333;word-break: break-all;word-wrap: break-word;background-color: #F5F5F5;border: 1px solid #CCC;border-radius: 4px;">';
        // 如果是boolean或者null直接显示文字；否则print
        if (is_bool($data)) {
            $show_data=$data ? 'true' : 'false';
        }elseif (is_null($data)) {
            $show_data='null';
        }else{
            $show_data=print_r($data,true);
        }
        $str.=$show_data;
        $str.='</pre>';
        echo $str;
    }
}
//5：p函数的终止操作
if(!function_exists('pd')){
    function pd($data){
        p($data);
        die;
    }
}


