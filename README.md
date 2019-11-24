thinkphp5.1-wq
===============

用thinkphp5.1框架工作中实现的一些小功能：

 1. 钉钉机器人消息通知
 2. php生成二维码
 3. redis操作类库
 4. redis+mysql做队列
 5. 导出大量数据到csv文件
 6. google身份验证器类


#### 1: 钉钉机器人消息通知
~~~
demo/controller/DingTalk
~~~

#### 2: php生成二维码
~~~
composer require endroid/qr-code
demo/controller/Qrcode
~~~

#### 3: redis操作类库
~~~
demo/controller/Redis
~~~

#### 4: redis队列服务
~~~
common/service/QueueService
~~~

#### 5: 导出大量数据到csv工具类
~~~
extend/csv/Csv.php
~~~

#### 6: Google身份验证器类
~~~
extend/googleauth/GoogleAuth.php
~~~


