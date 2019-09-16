<?php
/**
 * @Note 队列服务：redis+mysql做队列，队列数据存到redis中，可以避免拿重，mysql记录为了redis服务故障，适合一般小型项目
 * Created by PhpStorm.
 * User: KingWQ
 * Date: 2019/9/16
 * Time: 10:57
 */
namespace app\common\service;

use think\Db;
use think\Exception;
use redis\Predis;

class QueueService
{
    /**
     * @note 入队到redis列表中，同时记录到mysql中
     * @param string $queueName 队列任务名称：order、email
     * @param array $queueData
     * @return array
     * @throws \Exception
     */
    public static function add(string $queueName, array $queueData)
    {
        try{
            $addData = [
                'queue_name'    => $queueName,
                'data'          => json_encode($queueData),
                'order_sn'      => $queueData['order_sn'] ?? '',
                'user_id'       => $queueData['user_id'] ?? '',
            ];
            $taskId = Db::name('v_queue')->insertGetId($addData);

            $queueData['task_id'] = $taskId;
            $queueJson = json_encode($queueData);
            $res = Predis::getInstance()->rPush($queueName, $queueJson);

            if($res != 1) exception("入队失败：{$queueName}, data: {$queueJson}");

            return ['status'=>1,'msg'=>"---- {$queueName} 进队列成功 ----\n"];
        } catch (Exception $e){
            return ['status'=>0, 'msg'=>$e->getMessage()];
        }

    }

    /**
     * @note 出队
     * @param string $queueName  队列任务名称
     * @return array
     * @throws \Exception
     */
    public static function run(string $queueName)
    {
        try{
            $task = Predis::getInstance()->lrange($queueName, 0, -1);
            if(empty($task)) return ['status'=>1,'msg'=>"---- {$queueName} 队列里没有任务要执行 ----\n"];

            $taskJson = Predis::getInstance()->lPop($queueName);
            app_log($taskJson, "{$queueName}_queue");

            $taskArr = json_decode($taskJson,true);
            if(empty($taskArr)) exception("{$queueName} json转arr为空  json:{$taskJson}");

            switch($queueName){
                case 'order': $res = self::dealOrder($taskArr);break;
                case 'email': $res = self::dealEmail($taskArr);break;
            }

            $queueStatus = $res['status'] == 1 ? 1 : -1;
            $updateTime = date('Y-m-d H:i:s');
            Db::name("v_queue")->where('id', $taskArr['task_id'])->update(['status'=>$queueStatus, 'remark'=>$res['msg'], 'update_time'=>$updateTime]);

            if($res['status'] != 1) exception("{$queueName} 任务执行失败 {$res['msg']}：{$taskJson}");

            return ['status'=>1,'msg'=>"ok",'data'=>$res];
        }catch(Exception $e){
            return ['status'=>0, 'msg'=>$e->getMessage()];
        }
    }


    /**
     * @note 处理订单队列
     * @param array $taskArr 队列数据
     * @return array
     * @throws \Exception
     */
    public static function dealOrder(array $taskArr)
    {
        try{
            $order = Db::name('v_order')->where('order_sn', $taskArr['order_sn'])->find();
            if(empty($order)) exception("订单号不存在：{$taskArr['order_sn']}");

            //1：请求供应商下单
            $postData['product_code']   = $order['product_code'];
            $postData['order_sn']       = $order['order_sn'];
            $postData['location_id']    = $order['location_id'];
            $postData['service_code']   = $order['service_code'];
            $res = curl_post(env("supplier_url"), 'post', $postData);
            $resArr = json_decode($res, true);
            if($resArr['code'] != 2000) exception($resArr['msg']);


            //2:修改订单数据
            $currDate = date("Y-m-d H:i:s");
            $expTime  = strtotime($currDate)+60*24*3600;

            $orderData['order_status']  = 1;
            $orderData['update_time']   = date('Y-m-d H:i:s');

            $subData['goods_status']    = 3;
            $subData['update_time']     = date('Y-m-d H:i:s');
            $subData['ticket_data']     = json_encode(['ticket_code'=>$resArr['data']['coupon_id']]);
            $subData['exp_time']        = date('Y-m-d H:i:s', $expTime);

            Db::transaction(function () use ($order,$orderData,$subData) {
                Db::name('v_order')->where('order_sn', $order['order_sn'])->update($orderData);
                Db::name('v_order_sub')->where('order_sn',$order['order_sn'])->update($subData);
            });

            //3：发送预定成功邮件
            $emailTask = [
                'order_sn'  => $order['order_sn'],
                'email_type'=> 'order_success',
                'email'     => $order['email'],
            ];
            self::add('email', $emailTask);

            return ['status'=>1, 'msg'=>'ppg_order下单成功','data'=>$order];
        }catch(Exception $e){
            return ['status'=>0, 'msg'=>$e->getMessage(), 'data'=>[]];
        }
    }

    /**
     * @note 处理发送邮件队列
     * @param array $taskArr 队列数据
     * @return array
     * @throws \Exception
     */
    public static function dealEmail(array $taskArr)
    {
        try{
            $email = EmailService::getContent($taskArr);
            if($email['status'] != 1) exception($email['msg']);

            $res = (new SendMailer())->sendEmail($taskArr['email'], $email['data']['title'], $email['data']['content']);
            if($res['status'] != 1) exception($res['msg']);

            return ['status'=>1, 'msg'=>'ok', 'data'=>[]];
        }catch (Exception $e){
            return ['status'=>0, 'msg'=>$e->getMessage(), 'data'=>[]];
        }
    }
}