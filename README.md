## 大汉三通短信发送

本次开发，包含了 大汉三通 发送短信 的接口的封装！

## 运行环境

PHP7.0+

## 代码贡献

如果您有发现有BUG，欢迎 Star，欢迎 PR ！

## 商务合作

手机和微信: 18903467858

欢迎商务联系！合作共赢！

## 说明

1、首选向`大汉三通`官方进行注册并获取短信配置参数！  
2、使用下边的例子进行发送  
3、到 `3tong.net` 查看自己发送的成功与失败的记录

## 例子
```
//大汉三通短信配置
$sign = 【企管家商旅】;
$account = 'dh8368x;
$password = '123456;
//实例化
$fun = new Sms($sign, $account, $password);
//给指定手机号发送内容
$fun->sendSingleSms('18903467858', '你好啊！');
```