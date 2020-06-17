<?php
/**
 * PHP实现jwt
 */

namespace app\api\controller;
class Jwt
{

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
            $base64Payload = self::base64UrlEncode(json_encode($payload, JSON_UNESCAPED_UNICODE));
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

        $payload = json_decode(self::base64UrlDecode($base64Payload), JSON_OBJECT_AS_ARRAY);

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
    private static function base64UrlEncode(string $input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    /**
     * base64UrlEncode https://jwt.io/ 中base64UrlEncode解码实现
     * @param string $input 需要解码的字符串
     * @return bool|string
     */
    private static function base64UrlDecode(string $input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $addlen = 4 - $remainder;
            $input .= str_repeat('=', $addlen);
        }

        return base64_decode(strtr($input, '-_', '+/'));
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
}

////测试和官网是否匹配begin
//$payload = ['sub' => '1234567890', 'name' => 'John Doe', 'iat' => 1516239022];
//$jwt = new Jwt;
//$token = $jwt->getToken($payload);
//echo "<pre>";
//echo $token;
//
////对token进行验证签名
//$getPayload = $jwt->verifyToken($token);
//echo "<br><br>";
//var_dump($getPayload);
//echo "<br><br>";
////测试和官网是否匹配end
//
////自己使用测试begin
//$payload_test = [
//    'iss' => 'admin',
//    'iat' => time(),
//    'exp' => time() + 7200,
//    'nbf' => time(),
//    'sub' => 'www.admin.com',
//    'jti' => md5(uniqid('JWT', true) . time())
//];
//
//$token_test = Jwt::getToken($payload_test);
//echo "<pre>";
//echo $token_test;
//
////对token进行验证签名
//$getPayload_test = Jwt::verifyToken($token_test);
//echo "<br><br>";
//var_dump($getPayload_test);
//echo "<br><br>";
////自己使用时候end
