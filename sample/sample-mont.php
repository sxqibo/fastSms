<?php

use Sxqibo\Sms\SmsFactory;
use Sxqibo\Sms\SmsInterface;

require_once '../vendor/autoload.php';

final class SmsSend
{
    private SmsInterface $sms;

    public function __construct()
    {
        // 从数据库中读取的短信供应商
        $provider = 'mont';
        // 从数据库中读取的短信配置
        $config = [
            'url' => '',
            'account' => '',
            'password' => ''
        ];

        $this->sms = SmsFactory::getSmsObject($provider, $config);
    }

    public function sendSms($mobile, $template, $param)
    {
        return $this->sms->send($template, $mobile, $param);
    }
}

$smsSend = new SmsSend();

// 不需要模板
$mobile = '手机号';
$express_name = '顺丰';
$express_no = '20240529';
$message = "您的订单已发货，物流公司：{$express_name}，运单号：{$express_no}，请注意查收";
$data = $smsSend->sendSms($mobile, '', [$message]);
var_dump($data);
