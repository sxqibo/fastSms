<?php

namespace Sxqibo\Sms\sms;

use AlibabaCloud\Client\AlibabaCloud;
use Sxqibo\Sms\SmsInterface;

final class SmsForAliyun implements SmsInterface
{
    private $config;
    private $status;

    public function __construct($config=[])
    {
        $this->config = $config;
        if (empty($this->config['access_key']) || empty($this->config['access_secret'])) {
            $this->status = false;
        } else {
            $this->status = true;
            AlibabaCloud::accessKeyClient($this->config['access_key'], $this->config['access_secret'])
                ->regionId($this->config['region_id'])
                ->asDefaultClient();
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
        $result = AlibabaCloud::rpc()
            ->product('Dysmsapi')
            ->version($this->config['version'])
            ->action('SendSms')
            ->method('POST')
            ->host($this->config['host'])
            ->options([
                'query' => [
                    'RegionId' => $this->config['region_id'],
                    'PhoneNumbers' => $phone,
                    'SignName' => $this->config['sign_name'],
                    'TemplateCode' => $conf['template_id'],
                    'TemplateParam' => json_encode($arguments),
                ],
            ])
            ->request();

        $result = $result->toArray();

        if ($result['Code'] == "OK") {
            $data['code'] = 200;
            $data['msg'] = '发送成功';
        } else {
            $data['code'] = $result['Code'];
            $data['msg'] = '发送失败，'.$result['Message'];
        }

        return $data;
    }
}
