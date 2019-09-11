<?php
/**
 * @Note 用endroid/qr-code组件 服务器端生成二维码图片
 * Created by PhpStorm.
 * User: KingWQ
 * Date: 2019/9/11
 * Time: 18:07
 */
namespace app\demo\controller;

use Endroid\QrCode\Response\QrCodeResponse;

class Qrcode
{
    public function generate()
    {
        $code = "timedifferent";
        $qrCode = new \Endroid\QrCode\QrCode($code);

        //1：直接输出二维码到浏览器，不保存图片，后面要加exit()，不然乱码
        header('Content-Type: '.$qrCode->getContentType());
        echo $qrCode->writeString();
        exit();

        //2: 保存图片
        $qrCode->writeFile('./qrcode.png');

        //3：返回一个二维码响应对象
        $response = new QrCodeResponse($qrCode);
        dump($response);
    }
}