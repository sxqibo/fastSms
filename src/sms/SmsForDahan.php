<?php
// +----------------------------------------------------------------------
// | 大汉三通短信发送方法
// +----------------------------------------------------------------------
// | Author: 子弹兄  Date:2019-02-14 Time:9:33
// +----------------------------------------------------------------------

namespace Sxqibo\Sms\sms;

use Sxqibo\Sms\SmsInterface;

final class SmsForDahan implements SmsInterface
{
    private $status;
    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;

        if (empty($this->config['account']) || empty($this->config['password'])) {
            $this->status = false;
        } else {
            $this->status = true;
        }
    }

    public function send(string $templateName, string $phone, array $arguments)
    {
        if (!$this->status) {
            $data['code'] = 103;
            $data['msg'] = '请在后台设置account和password';

            return $data;
        }

        $params = [
            'account'   => $this->config['account'],       // 参数1：用户账号
            'password'  => $this->config['password'],      // 参数2：账号密码，需采用MD5加密(32位小写)；
            'phones'    => $phone,                         // 参数3：接收手机号码，多个手机号码用英文逗号分隔，最多500个，必填；
            'content'   => $arguments[0],                  // 参数4：短信内容，最多350个汉字，必填,内容中不要出现【】[]这两种方括号，该字符为签名专用；
            'sign'      => $this->config['sign'],          // 参数5：短信签名，该签名需要提前报备，生效后方可使用，不可修改，必填，示例如：【大汉三通】；
            'sendtime'  => date('YmdHm', time()),          // 参数6：定时发送时间，格式yyyyMMddHHmm，为空或早于当前时间则立即发送；
            'msgid'     => $this->config['msgid'],         // 该批短信编号(32位UUID)，需保证唯一，选填
            'subcode'   => $this->config['subCode'],       // 短信签名对应子码(大汉三通提供)+自定义扩展子码(选填)，必须是数字，选填，未填使用签名对应子码，通常建议不填；
        ];

        try {
            // 发送数据
            $result = $this->funCurl($this->config['submit_url'], json_encode($params), 1);
            // 判断数组
            if (!is_array($result)) {
                $result = json_decode($result, true);
            }

            if ((int)$result['result'] == 0) {
                $data['code'] = 200;
                $data['msg'] = '发送成功';
            } else {
                $data['code'] = $result['result'];
                $data['msg'] = '发送失败，' . $result['desc'];
            }
        } catch (\Exception $e) {
            $data['code'] = $e->getCode();
            $data['msg'] = '发送失败，' . $e->getMessage();
        }

        return $data;
    }

    /**
     * 请求接口返回内容，目前主要应用IP地址查询
     * @param string url  请求的URL地址
     * @param bool $params 请求的参数
     * @param int $isPost 是否采用POST形式
     * @return bool|mixed
     */
    public static function funCurl($url, $params = false, $isPost = 0)
    {
        $httpInfo = [];
        $ch       = curl_init();
        $header   = array('Expect:');
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'JuheData');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在

        if ($isPost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }
        $response = curl_exec($ch);
        // 返回最后一次的错误号
        $errCode = curl_errno($ch);
        if ($response === false) {
            // echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));

        curl_close($ch);

        return $response;
    }
}
