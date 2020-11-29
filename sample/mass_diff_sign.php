<?php
//命名空间
use Sxqibo\Dahansantong\Sms;

require_once '../vendor/autoload.php';

//参数
$sign1    = '【企管家商旅】';
$sign2    = '【国新商旅】';
$account  = 'dh83681';
$password = 'Cs9rFvy2';

//实例化并发送
$fun    = new Sms($account, $password);
$data   = [
    [
        'phones'   => '18903467858',
        'content'  => '你好1',
        'sign'     => $sign1,
        'sendtime' => date('YmdHm', time()),
        'msgid'    => '',
        'subcode'  => '',
    ], [
        'phones'   => '18803415820',
        'content'  => '你好2',
        'sign'     => $sign2,
        'sendtime' => date('YmdHm', time()),
        'msgid'    => '',
        'subcode'  => '',
    ]
];
$result = $fun->sendMassSms($data, false); //采用不同签名
print_r($result);

