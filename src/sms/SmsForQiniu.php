<?php

namespace Sxqibo\Sms\sms;

use Qiniu\Auth;
use Qiniu\Sms\Sms;
use Sxqibo\Sms\SmsInterface;

final class SmsForQiniu implements SmsInterface
{
    private $config;
    private $status;
    private $sms;

    public function __construct($config=[])
    {
        $this->config = $config;
        if ($this->config['AccessKey'] == null || $this->config['SecretKey'] == null) {
            $this->status = false;
        } else {
            $this->status = true;
            $auth = new Auth($this->config['AccessKey'], $this->config['SecretKey']);
            $this->sms = new Sms($auth);
        }
    }

    public function send(string $templateName, string $phone, array $arguments)
    {
        if (!$this->status) {
            $data['code'] = 103;
            $data['msg']  = '请在后台设置AccessKey和SecretKey';

            return $data;
        }

        $conf = $this->config['actions'][$templateName];
        $phoneNumbers = [$phone];
        $templateId = $conf['template_id'];
        $result = $this->sms->sendMessage($templateId, $phoneNumbers, $arguments);
        if (isset($result[0]['job_id'])) {
            $data['code'] = 200;
            $data['msg'] = '发送成功';
        } else {
            $data['code'] = 102;
            $data['msg'] = '发送失败';
        }

        return $data;
    }
}
