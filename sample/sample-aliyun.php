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
        $provider = 'aliyun_new';
        // 从数据库中读取的短信配置
        $config = [
            'version'       => '2017-05-25',
            'host'          => 'dysmsapi.aliyuncs.com',
            'scheme'        => 'http',
            'region_id'     => 'cn-hangzhou',
            'access_key'    => 'aaa',
            'access_secret' => 'bbb',
            'sign_name'     => 'ccc',
            'actions'       => [
                'smsVerify'        => [
                    'actions_name'      => '验证码短信',
                    'template_id'  => 'SMS_264200953',
                ],
                'login'           => [
                    'actions_name'      => '登录验证',
                    'template_id'  => 'SMS_53115057',
                ],
                'changePassword' => [
                    'actions_name'      => '修改密码',
                    'template_id'  => 'SMS_53115053',
                ],
                'changeUserinfo' => [
                    'actions_name'      => '变更信息',
                    'template_id'  => 'SMS_53115052',
                ],
            ],
        ];;

        $this->sms = SmsFactory::getSmsObject($provider, $config);

//        $config =  [
//            'account' => '',
//            'password' => '',
//            'sign' => '',
//        ];
//
//        $this->sms = SmsFactory::getSmsObject('dahan', $config);
    }

    public function sendSms($mobile, $template, $param)
    {
        return $this->sms->send($template, $mobile, $param);
    }
}

$smsSend = new SmsSend();
// 有模版的
$data = $smsSend->sendSms('', 'smsVerify', ['code' => '123456']);
// 没模版的，大汉三通没有模板
$data = $smsSend->sendSms('', '', ['123456']);
var_dump($data);
