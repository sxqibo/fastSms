<?php

use Sxqibo\Sms\SmsFactory;
use Sxqibo\Sms\SmsInterface;

require_once '../vendor/autoload.php';

final class SmsSend
{
    private SmsInterface $sms;

    public function __construct()
    {
        // 从数据库中读取的短信默认驱动
        $smsDefaultDriver = 'aliyun';
        // 从数据库中读取的短信配置
        $config = [];

        $this->sms = SmsFactory::getSmsObject($smsDefaultDriver, $config);
    }

    public function sendSms($mobile, $template, $param)
    {
        return $this->sms->send($template, $mobile, $param);
    }
}

$smsSend = new SmsSend();
$data = $smsSend->sendSms('123456', '注册', ['aaa', 123]);
var_dump($data);
