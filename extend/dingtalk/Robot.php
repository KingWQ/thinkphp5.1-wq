<?php
/**
 * @Note 钉钉机器人消息通知
 * Created by PhpStorm.
 * User: KingWQ
 * Date: 2019/9/11
 * Time: 15:27
 */
namespace dingtalk;

class Robot
{
    /**
     * @note 发送消息通知
     * @param string $msgType 消息类型 text、link、markdown、actionCard、feedCard
     * @param array $content  消息内容
     * @param array $atAll  通知人数
     * @return array
     */
    public static function exec(string $msgType, array $content, array $atAll=[])
    {
        try{
            $paramArr = self::getParam($msgType, $content, $atAll);

            $webhook = env("dingtalk.webhook_url");
            $postString = json_encode($paramArr);
            $resJson = self::curlPost($webhook, $postString);

            $resArr = json_decode($resJson, true);
            if($resArr['errcode'] !== 0){
                throw new \Exception("请求失败,return: {$resJson}");
            }

            return ['status'=>1, 'msg'=>'ok', 'data'=>[]];
        }catch(\Exception $e){
            return ['status'=>0, 'msg'=>$e->getMessage(), 'data'=>[]];
        }
    }

    /**
     * @note 根据用户传的参数处理成 发消息请求参数
     * @param string $msgType 消息类型
     * @param array $content 消息内容
     * @param array $atAll 通知人数
     * @return array
     * @throws \Exception
     */
    private static function getParam(string $msgType, array $content, array $atAll)
    {
        if ( !in_array($msgType, ['text', 'link', 'markdown', 'actionCard', 'feedCard']) ) {
            throw new \Exception("消息类型错误");
        }

        $atFlag = empty($atAll) ? true : false;

        return [
            'msgtype'   => $msgType,
            $msgType    => $content,
            'at'        => ['atMobiles' => $atAll, 'isAtAll' => $atFlag,]
        ];
    }


    private static function curlPost(string $remoteServer, string $postString) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remoteServer);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-Type: application/json;charset=utf-8'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // 线下环境不用开启curl证书验证, 未调通情况可尝试添加该代码
        // curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
}