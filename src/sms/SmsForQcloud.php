<?php

namespace Sxqibo\Sms\sms;

use Qcloud\Sms\SmsSingleSender;
use Sxqibo\Sms\SmsInterface;

final class SmsForQcloud implements SmsInterface
{
    private $config;
    private $status;
    private $sms;

    public function __construct($config=[])
    {
        $this->config = $config;
        if ($this->config['appid'] == null || $this->config['appkey'] == null) {
            $this->status = false;
        } else {
            $this->status = true;
            $this->sms = new SmsSingleSender($this->config['appid'], $this->config['appkey']);
        }
    }

    public function send(string $templateName, string $phone, array $arguments)
    {
        if (!$this->status) {
            $data['code'] = 103;
            $data['msg']  = '请在后台设置appid和appkey';

            return $data;
        }

        $conf = $this->config['actions'][$templateName];
        $phoneNumbers = $phone;
        $templateId = $conf['template_id'];
        $smsSign = $this->config['sign_name'];
        $result = $this->sms->sendWithParam("86", $phoneNumbers, $templateId, $arguments, $smsSign, "", "");
        $result = json_decode($result,true);
        if ($result['result'] == 0) {
            $data['code'] = 200;
            $data['msg'] = '发送成功';
        } else {
            $data['code'] = $result['result'];
            $data['msg'] = '发送失败，'.$result['errmsg'];
        }

        return $data;
    }
}
