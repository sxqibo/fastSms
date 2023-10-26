<?php

namespace Sxqibo\Sms;

interface SmsInterface
{
    /**
     * @param string $templateName 模板名
     * @param string $phone 手机号
     * @param array $arguments 参数
     * @return mixed
     */
    public function send(string $templateName, string $phone, array $arguments);
}
