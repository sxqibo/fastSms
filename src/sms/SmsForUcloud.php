<?php

namespace Sxqibo\Sms\sms;

use Sxqibo\Sms\SmsInterface;
use UCloud\Core\Exception\UCloudException;
use UCloud\USMS\Apis\SendUSMSMessageRequest;
use UCloud\USMS\USMSClient;

final class SmsForUcloud implements SmsInterface
{
    private $status;
    private $sms;
    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;

        if ($this->config['projectId'] == null
            || $this->config['publicKey'] == null
            || $this->config['privateKey'] == null) {
            $this->status = false;
        } else {
            $this->status = true;
            $this->sms = new USMSClient([
                'publicKey' => $config['publicKey'],
                'privateKey' => $config['privateKey'],
                'projectId' => $config['projectId']
            ]);
        }
    }

    /**
     * @throws UCloudException
     */
    public function send(string $templateName, string $phone, array $arguments)
    {
        if (!$this->status) {
            $data['code'] = 103;
            $data['msg'] = '请在后台设置appid、appkey、projectId';

            return $data;
        }

        $conf = $this->config['actions'][$templateName];
        $templateId = $conf['template_id'];

        $phoneNumbers = [$phone];

        $sigContent = $this->config['sign_name'];

        try {
            $req = new SendUSMSMessageRequest();
            $req->setPhoneNumbers($phoneNumbers);
            $req->setSigContent($sigContent);
            $req->setTemplateId($templateId);
            $req->setTemplateParams($arguments);
            $result = $this->sms->sendUSMSMessage($req);

            if ($result['RetCode'] == 0) {
                $data['code'] = 200;
                $data['msg'] = '发送成功';
            } else {
                $data['code'] = $result['RetCode'];
                $data['msg'] = '发送失败，'.$result['Message'];
            }
        } catch (UCloudException $e) {
            throw $e;
        }

        return $data;
    }
}
