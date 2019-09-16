<?php
/**
 * @Note redis 操作demo
 * Created by PhpStorm.
 * User: KingWQ
 * Date: 2019/9/16
 * Time: 10:26
 */
namespace  app\demo\controller;

use redis\Predis;

class Redis
{
    public function index()
    {
        $redis = Predis::getInstance();
        $redis->set('blog', 'https://www.timedifferent.com');
        dump($redis->get('blog'));
    }

}