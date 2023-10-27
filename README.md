# 岐伯短信接口

#### 介绍
本项目是集成了各大云服务厂商的短信业务平台，由山西岐伯科技有限公司维护，目前支持阿里云、腾讯云、七牛云、又拍云、Ucloud、华为云和大汉三通，您如果有其他厂商的集成需求，请通过邮件联系作者提交需求。

#### 运行环境

PHP7.4+

#### 安装教程

使用 `composer require sxqibo/fast-sms` 命令行安装即可。

安装完成后，会在安装目录带一个配置文件，`config/config`，请参考该配置文件的结构当做构造函数的配置结构来进行传参。

#### 代码贡献

如果您有发现有BUG，欢迎 Star，欢迎 PR ！

#### 商务合作

手机和微信: 18903467858

欢迎商务联系！合作共赢！

#### 说明

该 composer 包集成了多种短信接口，具体包含下列接口：
1. 大汉三通 Dahan
2. 阿里云 aliyun 或 aliyun_new
3. 腾讯云 qcloud
4. 七牛云 qiniu
5. 又拍云 ucloud
6. 华为云 huawei
7. 优刻得 upyun

#### 例子

```php
use Sxqibo\Sms\SmsFactory;
use Sxqibo\Sms\SmsInterface;

require_once '../vendor/autoload.php';

final class SmsSend
{
    private SmsInterface $sms;

    public function __construct()
    {
        // 从数据库中读取的短信供应商
        // 这个值是文档中 [说明] 部分的值
        $provider = 'aliyun_new';
        
        // 从数据库中读取的短信配置
        $config = [
            'version'       => '2017-05-25',
            'host'          => 'dysmsapi.aliyuncs.com',
            'scheme'        => 'http',
            'region_id'     => 'cn-hangzhou',
            'access_key'    => 'aaa',
            'access_secret' => 'bbb',
            'sign_name'     => 'ccc',
            'actions'       => [
                'smsVerify'        => [
                    'actions_name'      => '验证码短信',
                    'template_id'  => 'SMS_264200953',
                ],
                'login'           => [
                    'actions_name'      => '登录验证',
                    'template_id'  => 'SMS_53115057',
                ],
                'changePassword' => [
                    'actions_name'      => '修改密码',
                    'template_id'  => 'SMS_53115053',
                ],
                'changeUserinfo' => [
                    'actions_name'      => '变更信息',
                    'template_id'  => 'SMS_53115052',
                ],
            ],
        ];;

        $this->sms = SmsFactory::getSmsObject($provider, $config);
    }

    public function sendSms($mobile, $template, $param)
    {
        return $this->sms->send($template, $mobile, $param);
    }
}
```

上面代码中的 $provider 和 $config 都可以是从数据库中读取的，也可以是从配置文件中读取的

注意：$config 的结构要和上面的结构一致*

调用上面的代码

```php
$smsSend = new SmsSend();
// 参数1是手机号
// 参数二是模版，有的短信可能没有模版，传空即可，注意这个模版在 $config 中配置过的
// 第三个参数是模版对应的内容，注意有的短信没有模板，可以直接把短信内容(字符串)包装到数组中即可，如['123456']
$data = $smsSend->sendSms('15000000001', 'smsVerify', ['code' => '123456']);
var_dump($data);
```

#### 

如果需要扩展，那么实现仿照 sms 目录下，定义一个以 SmsFor 开头的类；类的构造接收配置；然后实现 SmsInterface 的接口即可；

传参时，传 SmsFor 后面的部分即可。
