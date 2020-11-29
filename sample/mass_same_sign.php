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
$data   = [
    [
        'phones'   => '18903467858',
        'content'  => '你好1',
        'sendtime' => date('YmdHm', time()),
        'msgid'    => '',
        'subcode'  => '',
    ], [
        'phones'   => '18803415820',
        'content'  => '你好2',
        'sendtime' => date('YmdHm', time()),
        'msgid'    => '',
        'subcode'  => '',
    ]
];
$result = $fun->sendMassSms($data); //相同签名
print_r($result);

