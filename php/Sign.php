<?php

namespace app\api\controller;

use think\Db;

/**
 * Created by PhpStorm
 * User: yanqing
 * Date: 5/21/20 9:31 AM
 */
class Sign
{
    const EXPIRE_SECONDS = 10;

    private function __construct()
    {
    }

    public static function checkSign($params, $signature)
    {
        if (!$params || !$signature) {
            return false;
        }

        $time = $params['x-wms-timestamp'];

        // 超过一天过期
        if ((time() - $time) > self::EXPIRE_SECONDS) {
            return false;
        }

        $ssig = self::buildSign($params);

        if ($ssig === $signature) {
            return true;
        }

        return false;
    }

    private static function buildSign($params)
    {
        ksort($params);

        $ssigStr = '';
        foreach ($params as $k => $v) {
            // 去除路由中的参数及ssig
            if (in_array($k, ['ssig', 'version', 'controller', 'function'])) {
                continue;
            }

            $ssigStr .= "$k:$v\n";
        }

        $appKey = $params['x-wms-accesskey'];
        $secretKey = self::getSecretKey($appKey);
        if ($secretKey) {
            $ssig = substr(base64_encode(hash_hmac('sha256', $ssigStr, $secretKey, true)), 10, 10);

            // android url_safe模式对字符进行了如下替换，比较时需要进行同样的转换
            // '+' -> '-'
            // '/' -> '_'
            // '=' -> ''

            // Convert Base64 to Base64URL by replacing “+” with “-” and “/” with “_”
            $ssig = strtr($ssig, '+/', '-_');

            // Remove padding character from the end of line and return the Base64URL result
            $ssig = rtrim($ssig, '=');
        }

        return $ssig;
    }

    private static function getSecretKey($accessKey)
    {
        $secretKey = '';
        // TODO 使用redis或memcached读到内存中
        $result = Db::name('app')
            ->where('access_key', $accessKey)
            ->find();

        if (!is_null($result)) {
            $secretKey = $result['secret_key'];
        }

        return $secretKey;
    }
}
