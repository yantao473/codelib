<?php
/**
 * PHP实现jwt
 */

namespace app\api\controller;

// $info = [
//    'iss' => json_encode(['name' => 'zhangsan', 'id' => 1]),
//    'iat' => time(),
//    'exp' => time() + 7200,
// ];
//
// $token = Jwt::getToken($info);
// echo $token .PHP_EOL;
//
// $payload = Jwt::verifyToken($token);
// var_dump($payload);


class Jwt
{
    const ENCRYPT_METHOD = 'AES-256-CBC';
    const SECRET_KEY = 'MGQ0YzU3YWQzYWVjMzliN2NhZmIyZjllNGYxZjYy';

    //头部
    private static $jwtHeader = [
        'alg' => 'HS256', //生成signature的算法
        'typ' => 'JWT'  //类型
    ];


    /**
     * 获取jwt token
     * @param array $payload jwt载荷  格式如下非必须
     * [
     * 'iss'=>'jwt_admin', //该JWT的签发者
     * 'iat'=>time(), //签发时间
     * 'exp'=>time()+7200, //过期时间
     * 'nbf'=>time()+60, //该时间之前不接收处理该Token
     * 'sub'=>'www.admin.com', //面向的用户
     * 'jti'=>md5(uniqid('JWT').time()) //该Token唯一标识
     * ]
     * @return bool|string
     */
    public static function getToken(array $payload)
    {
        $token = '';
        if (is_array($payload)) {
            $base64Header = self::base64UrlEncode(json_encode(self::$jwtHeader, JSON_UNESCAPED_UNICODE));
            $payloadStr = json_encode($payload, JSON_UNESCAPED_UNICODE);
            $base64Payload = self::encrypt($payloadStr);
            $partToken = sprintf("%s.%s", $base64Header, $base64Payload);

            $signature = self::signature($partToken, self::SECRET_KEY, self::$jwtHeader['alg']);
            $token = sprintf("%s.%s", $partToken, $signature);
        }

        return $token;
    }

    /**
     * 验证token是否有效,默认验证exp,nbf,iat时间
     * @param string $Token 需要验证的token
     * @return bool|string
     */
    public static function verifyToken(string $token)
    {
        $result = ['code' => 403];
        $tokens = explode('.', $token);
        if (count($tokens) !== 3) {
            $result['msg'] = 'token 长度不合法';
            return $result;
        }

        list($base64Header, $base64Payload, $sign) = $tokens;

        //获取jwt算法
        $base64DecodeHeader = json_decode(self::base64UrlDecode($base64Header), JSON_OBJECT_AS_ARRAY);
        if (empty($base64DecodeHeader['alg'])) {
            $result['msg'] = '不支持的算法';
            return $result;
        }

        //签名验证
        if (self::signature($base64Header . '.' . $base64Payload, self::SECRET_KEY, $base64DecodeHeader['alg']) !== $sign) {
            $result['msg'] = 'token 不匹配';
            return $result;
        }

        // 加密版
        $payload = json_decode(self::decrypt($base64Payload), JSON_OBJECT_AS_ARRAY);

        $time = time();
        //签发时间大于当前服务器时间验证失败
        if (isset($payload['iat']) && $payload['iat'] > $time) {
            $result['msg'] = '签发时间不合法';
            return $result;
        }

        //过期时间小于当前服务器时间验证失败
        if (isset($payload['exp']) && $payload['exp'] < $time) {
            $result['msg'] = 'token已过期';
            return $result;
        }

        //该nbf时间之前不接收处理该Token
        if (isset($payload['nbf']) && $payload['nbf'] > $time) {
            $result['msg'] = '处理token不合法';
            return $result;
        }

        $result['code'] = 200;
        $result['msg'] = $payload;
        return $result;
    }

    /**
     * base64UrlEncode  https://jwt.io/ 中base64UrlEncode编码实现
     * @param string $input 需要编码的字符串
     * @return string
     */
    private static function base64UrlEncode(string $data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * base64UrlEncode https://jwt.io/ 中base64UrlEncode解码实现
     * @param string $input 需要解码的字符串
     * @return bool|string
     */
    private static function base64UrlDecode(string $data)
    {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }

    /**
     * HMACSHA256签名  https://jwt.io/ 中HMACSHA256签名实现
     * @param string $input 为base64UrlEncode(header).".".base64UrlEncode(payload)
     * @param string $key
     * @param string $alg 算法方式
     * @return mixed
     */
    private static function signature(string $input, string $secretKey, string $alg = 'HS256')
    {
        $alg_config = ['HS256' => 'sha256'];
        return self::base64UrlEncode(hash_hmac($alg_config[$alg], $input, $secretKey, true));
    }

    private static function encrypt($encrypt)
    {
        $ivLength = openssl_cipher_iv_length(self::ENCRYPT_METHOD);
        $iv = openssl_random_pseudo_bytes($ivLength, $isStrong);
        if (!$isStrong) {
            // do nothing
        }

        $secKey = self::getSecKey();
        $finalByteArray = openssl_encrypt($encrypt, self::ENCRYPT_METHOD, $secKey, OPENSSL_RAW_DATA, $iv);

        return self::base64UrlEncode($iv . $finalByteArray);
    }

    private static function decrypt($decrypt)
    {
        $secKey = self::getSecKey();
        $decStr = self::base64UrlDecode($decrypt);
        $iv = substr($decStr, 0, 16);
        $content = substr($decStr, 16);
        return openssl_decrypt($content, self::ENCRYPT_METHOD, $secKey, OPENSSL_RAW_DATA, $iv);
    }

    private static function getSecKey()
    {
        $digestSeed = hash('SHA512', self::SECRET_KEY, true);
        return substr($digestSeed, 0, 32);
    }

}
