<?php
/**
 * PHP实现jwt
 */

namespace app\api\controller;


//$info = [
//    'iss' => json_encode(['name' => 'zhangsan', 'id' => 1]),
//    'iat' => time(),
//    'exp' => time() + 7200,
//];
//
//$token = Jwt::getToken($info);
//echo $token . '<br/>';
//
//$payload = Jwt::verifyToken($token);
//var_dump($payload);


class Jwt
{
    const ENCRYPT_METHOD = 'AES-256-CBC';
    const ACCESS_KEY =  'e1d3bcf4-38d5-41cd-9422-c3629277b7d3';

    //头部
    private static $jwtHeader = [
        'alg' => 'HS256', //生成signature的算法
        'typ' => 'JWT'  //类型
    ];

    //使用HMAC生成信息摘要时所使用的密钥
    private static $secretKey = 'MGQ0YzU3YWQzYWVjMzliN2NhZmIyZjllNGYxZjYy';

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
            $payloadStrEncrypt = self::encrypt($payloadStr);
            $base64Payload = self::base64UrlEncode($payloadStrEncrypt);
            $partToken = sprintf("%s.%s", $base64Header, $base64Payload);

            $signature = self::signature($partToken, self::$secretKey, self::$jwtHeader['alg']);
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

        $tokens = explode('.', $token);
        if (count($tokens) !== 3) {
            return ['code' => 400, 'message' => 'token 长度不合法'];
        }

        list($base64Header, $base64Payload, $sign) = $tokens;

        //获取jwt算法
        $base64DecodeHeader = json_decode(self::base64UrlDecode($base64Header), JSON_OBJECT_AS_ARRAY);
        if (empty($base64DecodeHeader['alg'])) {
            return ['code' => 400, 'message' => '不支持的算法'];
        }

        //签名验证
        if (self::signature($base64Header . '.' . $base64Payload, self::$secretKey, $base64DecodeHeader['alg']) !== $sign) {
            return ['code' => 400, 'message' => 'token 不匹配'];
        }

        $payload = json_decode(self::decrypt(self::base64UrlDecode($base64Payload)), JSON_OBJECT_AS_ARRAY);

        $time = time();
        //签发时间大于当前服务器时间验证失败
        if (isset($payload['iat']) && $payload['iat'] > $time) {
            return ['code' => 400, 'message' => '签发时间不合法'];
        }

        //过期时间小于当前服务器时间验证失败
        if (isset($payload['exp']) && $payload['exp'] < $time) {
            return ['code' => 400, 'message' => 'token已过期'];
        }

        //该nbf时间之前不接收处理该Token
        if (isset($payload['nbf']) && $payload['nbf'] > $time) {
            return ['code' => 400, 'message' => '处理token不合法'];
        }

        return ['code' => 0, 'message' => $payload];
    }

    /**
     * base64UrlEncode  https://jwt.io/ 中base64UrlEncode编码实现
     * @param string $input 需要编码的字符串
     * @return string
     */
    private static function base64UrlEncode(string $data)
    {
        return rtrim( strtr( base64_encode( $data ), '+/', '-_'), '=');
    }

    /**
     * base64UrlEncode https://jwt.io/ 中base64UrlEncode解码实现
     * @param string $input 需要解码的字符串
     * @return bool|string
     */
    private static function base64UrlDecode(string $data)
    {
        return base64_decode( strtr( $data, '-_', '+/') . str_repeat('=', 3 - ( 3 + strlen( $data )) % 4 ));
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

    private static function encrypt($encrypt) {
        $ivLength = openssl_cipher_iv_length(self::ENCRYPT_METHOD);
        $bytes = openssl_random_pseudo_bytes($ivLength, $isStrong);
        $b64Str = base64_encode($bytes);
        $iv = substr($b64Str, 0, $ivLength);  // 取 $ivLength 位
        $realKey = substr(sha1(sha1(self::ACCESS_KEY, true),true), 0, 16);
        $result = openssl_encrypt($encrypt, self::ENCRYPT_METHOD, $realKey, false, $iv);

        return $iv . $result;
    }

    private static function decrypt($decrypt) {
        $iv = substr($decrypt, 0, 16);
        $realKey = substr(sha1(sha1(self::ACCESS_KEY, true),true), 0, 16);
        $result = openssl_decrypt(substr($decrypt,16), self::ENCRYPT_METHOD, $realKey, false, $iv);

        return $result;
    }
}
