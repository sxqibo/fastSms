<?php

namespace Sxqibo\Sms\sms;

use GuzzleHttp\Client;
use Sxqibo\Sms\SmsInterface;

final class SmsForMont implements SmsInterface
{
    private $config;
    private $status;

    public function __construct($config = [])
    {
        $this->config = $config;
        if (empty($this->config['url']) || empty($this->config['account']) || empty($this->config['password'])) {
            $this->status = false;
        } else {
            $this->status = true;
        }
    }

    public function send(string $templateName, string $phone, array $arguments)
    {
        if (!$this->status) {
            $data['code'] = 103;
            $data['msg'] = '请在后台设置接口地址、用户名和密码';

            return $data;
        }

        $client = new Client();

        $pwd = $this->getPwd();
        $json = [
            'userid' => mb_strtoupper($this->config['account']),
            'pwd' => $pwd['pwd'],
            'mobile' => $phone,
            'content' => urlencode($arguments[0]),
            'timestamp' => $pwd['timestamp']
        ];

        $response = $client->request('POST', $this->config['url'] . '/sms/v2/std/send_single', [
           'json' => $json,
           'headers' => ['Content-Type' => 'application/json']
        ]);

        $result = $response->getBody()->getContents();
        $result = json_decode($result,true);

        if ($result['result'] == '000000') {
            $data['code'] = 200;
            $data['msg'] = '发送成功';
        } else {
            $data['code'] = $result['result'];
            $data['msg'] = '发送失败，'.urldecode($result['desc']);
        }

        return $data;
    }

    private function getPwd()
    {
        $timestamp = $this->getTimestamp();
        $pwd = sprintf("%s%s%s%s",
            mb_strtoupper($this->config['account']),
            '00000000',
            $this->config['password'],
            $timestamp);

        return ['pwd' => mb_strtolower(md5($pwd)), 'timestamp' => $timestamp];
    }

    private function getTimestamp()
    {
        return date("mdHis", time());
    }
}
