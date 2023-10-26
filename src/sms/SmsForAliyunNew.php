<?php

namespace Sxqibo\Sms\sms;

use AlibabaCloud\SDK\Dysmsapi\V20170525\Dysmsapi;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Models\SendSmsRequest;
use Darabonba\OpenApi\Models\Config;
use Sxqibo\Sms\SmsInterface;

final class SmsForAliyunNew implements SmsInterface
{
    private $config;
    private $status;
    private $sms;

    public function __construct($config = [])
    {
        $this->config = $config;

        if (empty($this->config['access_key']) || empty($this->config['access_secret'])) {
            $this->status = false;
        } else {
            $this->status = true;
            $tmpConfig = new Config([
                'accessKeyId' => $this->config['access_key'],
                'accessKeySecret' => $this->config['access_secret']
            ]);

            $this->sms = new Dysmsapi($tmpConfig);
        }
    }

    public function send(string $templateName, string $phone, array $arguments)
    {
        if (!$this->status) {
            $data['code'] = 103;
            $data['msg'] = '请在后台设置accessKeyId和accessKeySecret';

            return $data;
        }

        $conf = $this->config['actions'][$templateName];

        $sendSmsRequest = new SendSmsRequest([
            'phoneNumbers'  => $phone,
            'signName'      => $this->config['sign_name'],
            'templateCode'  => $conf['template_id'],
            'templateParam' => json_encode($arguments, JSON_UNESCAPED_UNICODE)
        ]);

        $result = $this->sms->sendSms($sendSmsRequest);

        if ($result->body->code == 'OK') {
            $data['code'] = 200;
            $data['msg'] = '发送成功';
        } else {
            $data['code'] = $result->body->code;
            $data['msg'] = $result->body->message;
        }

        return $data;
    }
}
