<?php

namespace Sxqibo\Sms\sms;

use Sxqibo\Sms\SmsInterface;

final class SmsForUpyun implements SmsInterface
{
    private $config;
    private $status;

    public function __construct($config=[])
    {
        $this->config = $config;
        if ($this->config['token'] == null) {
            $this->status = false;
        } else {
            $this->status = true;
        }
    }

    public function send(string $templateName, string $phone, array $arguments)
    {
        if (!$this->status) {
            $data['code'] = 103;
            $data['msg']  = '请在后台设置Token';

            return $data;
        }

        $conf = $this->config['actions'][$templateName];
        $msg['mobile'] = $phone;
        $msg['template_id'] = $conf['template_id'];
        $msg['vars'] = $arguments;
        $url = $this->config['apiurl'] ?: 'https://sms-api.upyun.com/api/messages';

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\nAuthorization: " . $this->config['token'],
                'method'  => 'POST',
                'content' => http_build_query($msg)
            ]
        ];

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $result = json_decode($result,true);

        if (isset($result['message_ids']['0']['message_id'])) {
            $data['code'] = 200;
            $data['msg'] = '发送成功';
        } else {
            $data['code'] = 102;
            $data['msg'] = '发送失败';
        }

        return $data;
    }
}
