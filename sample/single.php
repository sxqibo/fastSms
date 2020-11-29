<?php
//命名空间
use Sxqibo\Dahansantong\Sms;

require_once '../vendor/autoload.php';

//参数
$sign     = '【企管家商旅】';
$account  = 'dh83681';
$password = 'Cs9rFvy2';

//实例化并发送
$fun    = new Sms($account, $password, $sign);
$result = $fun->sendSingleSms('18903467858', '你好啊！');
print_r($result);

