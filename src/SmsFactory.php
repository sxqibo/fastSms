<?php

namespace Sxqibo\Sms;

final class SmsFactory
{
    const CLASS_PREFIX = "\\Sxqibo\\Sms\\sms\\SmsFor";

    const DIR = __DIR__ . '/sms/';

    public static function  getSmsObject(string $objectName, array $config = [])
    {
        // 转大驼峰
        // object_name => ObjectName
        // object => Object
        $objectName = str_replace(' ', '', ucwords(str_replace('_', ' ', $objectName)));

        // 拼接完整的类文件
        $fileName = 'SmsFor' . $objectName . '.php';

        // 拼接类路径
        $dir = self::DIR;

        // 保存类的完全限定名
        $class = '';

        $handle = dir($dir);

        // 便利具体实现类的目录，查找是否有对应的类
        while(false !== ($entry = $handle->read())) {
            if($entry == '.' || $entry == '..') {
                continue;
            }

            if(!is_dir($dir . $entry) && $entry == $fileName) {
                // 拼接类的完全限定名
                $class = self::CLASS_PREFIX . $objectName;
                break;
            }
        }

        $handle->close();

        // 找到则实例化请求的类
        if ($class != '') {
            return new $class($config);
        }

        // 没有找到则实现一个默认的类
        // 默认的类使用的是太平洋的类
        $class = self::CLASS_PREFIX . 'Aliyun';

        return new $class($config);
    }
}
