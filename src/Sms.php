<?php
// +----------------------------------------------------------------------
// | 大汉三通短信发送方法
// +----------------------------------------------------------------------
// | Author: 子弹兄  Date:2019-02-14 Time:9:33
// +----------------------------------------------------------------------
namespace Sxqibo\Dahansantong;

class Sms
{
    const SUBMIT  = 'http://www.dh3t.com/json/sms/Submit';    //4.1.1.短信下发地址
    const BATCH_SUBMIT  = 'http://www.dh3t.com/json/sms/BatchSubmit';    //4.1.1.短信下发地址

    /**
     * Sms constructor.
     * @author hongwei 2019-02-14
     * @param string $account 用户账号
     * @param string $password 账号密码
     * @param string $sign 签名
     * @param null $subCode 短信签名对应子码（非必须）
     */
    public function __construct($account, $password, $sign = null, $subCode = null)
    {
        try {
            $account    = trim($account);
            $password   = md5(trim($password));
            $sign       = isset($sign) ? trim($sign) : '';
            $subCode    = isset($subCode) ? trim($subCode) : '';

            if (empty($account)) {
                throw new \Exception("account is empty");
            }
            if (empty($password)) {
                throw new \Exception("password is empty");
            }
            //sign不用判断
            //subCode不用判断

            $this->sign     = $sign;
            $this->account  = $account;
            $this->password = $password;
            $this->subCode  = $subCode;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private $sign;
    private $account;
    private $password;
    private $subCode;

    /**
     * 短信下发
     * 说明：发送一条或者多条内容相同的短信
     * @author hongwei 2019-02-14
     * @param $phones
     * @param $content
     * @return array
     */
    public function sendSingleSms($phones, $content)
    {
        $sendTime = date('YmdHm', time()); //时间格式
        $params = [
            'account'   => $this->account,       //参数1：用户账号
            'password'  => $this->password,      //参数2：账号密码，需采用MD5加密(32位小写)；
            'phones'    => $phones,              //参数3：接收手机号码，多个手机号码用英文逗号分隔，最多500个，必填；
            'content'   => $content,             //参数4：短信内容，最多350个汉字，必填,内容中不要出现【】[]这两种方括号，该字符为签名专用；
            'sign'      => $this->sign,          //参数5：短信签名，该签名需要提前报备，生效后方可使用，不可修改，必填，示例如：【大汉三通】；
            'sendtime'  => $sendTime,            //参数6：定时发送时间，格式yyyyMMddHHmm，为空或早于当前时间则立即发送；
            'msgid'     => '',                   //该批短信编号(32位UUID)，需保证唯一，选填
            'subcode'   => $this->subCode,       //短信签名对应子码(大汉三通提供)+自定义扩展子码(选填)，必须是数字，选填，未填使用签名对应子码，通常建议不填；
        ];
        try {
            //发送数据
            $result = $this->funCurl(self::SUBMIT, json_encode($params), 1);
            //判断数组
            if (!is_array($result)) {
                $result = json_decode($result, true);
            }
            //不为0抛出异常
            if (!(int)$result['result'] == 0) {
                throw new \Exception($result['desc'], $result['result']);
            }
        } catch (\Exception $e) {
            echo 'Message is:'.$e->getMessage(), '，Code is '.$e->getCode();
            exit;
        }
        return $result;
    }

    /**
     * 批量短信下是否发相同“签名”
     * 说明：批量发送不同内容短信
     * @author hongwei 2019-02-14
     * @param array $data 短信格式，请参考文档
     * @param bool $sameSign 是否采用相同的“签名”，true采用相同签名，false采用不同签名，默认采用相同签名
     * @return bool|mixed
     */
    public function sendMassSms($data = [], $sameSign = true)
    {
        //如果有相同的签名
        if ($sameSign) {
            foreach ($data as $k => $v) {
                $data[$k]['sign'] = $this->sign;
            }
        }
        //参数
        $params = [
            'account'  => $this->account,
            'password' => $this->password,
            'data'     => $data
        ];
        try {
            //发送数据
            $result = $this->funCurl(self::BATCH_SUBMIT, json_encode($params), 1);
            //判断数组
            if (!is_array($result)) {
                $result = json_decode($result, true);
            }
            //不为0抛出异常
            if (!(int)$result['result'] == 0) {
                throw new \Exception($result['desc'], $result['result']);
            }
        } catch (\Exception $e) {
            echo 'Message is:'.$e->getMessage(), '，Code is '.$e->getCode();
            exit;
        }
        return $result;
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
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }
}
