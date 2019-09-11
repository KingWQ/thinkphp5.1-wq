<?php
/**
 * @Note 钉钉机器人消息通知demo
 * Created by PhpStorm.
 * User: KingWQ
 * Date: 2019/9/11
 * Time: 15:35
 */
namespace app\demo\controller;

use dingtalk\Robot;

class DingTalk
{
    public function text()
    {
        $atAll = ['168xxxxxxxx'];
        $textArr = ['content'=>'我就是我, 是不一样的烟火'];

        $res = Robot::exec('text', $textArr, $atAll);
        dump($res);
    }

    public function markdown()
    {
        $atAll = ['168xxxxxxxx'];
        $json = '{
    "title": "杭州天气",
    "text": "#### 杭州天气 @156xxxx8827\n> 9度，西北风1级，空气良89，相对温度73%\n\n> ![screenshot](http://hbimg.huabanimg.com/957eebfd8b126ff4b6b8668c7c9f2c280d0f5315dd81-vRqsCS_fw236)\n> ###### 10点20分发布 [天气](https://www.timedifferent.com/) \n"
}';
        $markdownArr =  json_decode($json, true);

        $res = Robot::exec('markdown', $markdownArr, $atAll);
        dump($res);
    }

    public function actionCard()
    {
        $atAll = ['168xxxxxxxx'];

        //1: actionCard整体跳转
        $json = '{
        "title": "乔布斯 20 年前想打造一间苹果咖啡厅，而它正是 Apple Store 的前身",
        "text": "![screenshot](@lADOpwk3K80C0M0FoA) \n ### 乔布斯 20 年前想打造的苹果咖啡厅 \n Apple Store 的设计正从原来满满的科技感走向生活化，而其生活化的走向其实可以追溯到 20 年前苹果一个建立咖啡馆的计划",
        "hideAvatar": "0",
        "btnOrientation": "0",
        "singleTitle": "阅读全文",
        "singleURL": "https://www.timedifferent.com"
    }';
        $cardArr =  json_decode($json, true);

        $res = Robot::exec('actionCard', $cardArr, $atAll);
        dump($res);


        //2: actionCard 独立跳转
        $json = '{
        "title": "乔布斯 20 年前想打造一间苹果咖啡厅，而它正是 Apple Store 的前身",
        "text": "![screenshot](@lADOpwk3K80C0M0FoA) \n ### 乔布斯 20 年前想打造的苹果咖啡厅 \n Apple Store 的设计正从原来满满的科技感走向生活化，而其生活化的走向其实可以追溯到 20 年前苹果一个建立咖啡馆的计划",
        "hideAvatar": "0",
        "btnOrientation": "0",
        "btns": [
            {
                "title": "内容不错",
                "actionURL": "https://www.timedifferent.com"
            },
            {
                "title": "不感兴趣",
                "actionURL": "https://www.timedifferent.com"
            }
        ]}';

        $cardArr =  json_decode($json, true);

        $res = Robot::exec('actionCard', $cardArr, $atAll);
        dump($res);
    }

    public function feedCard()
    {
        $atAll = ['168xxxxxxxx'];
        $json = '{
        "links": [
            {
                "title": "时代的火车向前开", 
                "messageURL": "https://www.timedifferent.com", 
                "picURL": "http://hbimg.huabanimg.com/957eebfd8b126ff4b6b8668c7c9f2c280d0f5315dd81-vRqsCS_fw236"
            },
            {
                "title": "时代的火车向前开2", 
                "messageURL": "https://www.timedifferent.com", 
                "picURL": "http://hbimg.huabanimg.com/957eebfd8b126ff4b6b8668c7c9f2c280d0f5315dd81-vRqsCS_fw236"
            }
        ]}';
        $feedArr =  json_decode($json, true);

        $res = Robot::exec('feedCard', $feedArr, $atAll);
        dump($res);
    }

}
