<?php
/**
 * 检测给定的ip是否白名单之内
 * 白名单有三种形式
 * a 10.217.37.*    表明范围为10.217.37.0 到 10.217.37.255
 * b 10.217.37.8-42 表明范围为10.217.37.8 到 10.217.37.42
 * c 10.217.37.8    表明只允许10.217.37.8访问
 */
class ipv4check {
    const IP_TYPE_SINGLE = 'single';
    const IP_TYPE_WILDCARD = 'wildcard';
    const IP_TYPE_SECTION = 'section';

    private $allowIPv4 = array("10.217.39.*", "10.217.39.28-42", "10.217.39.8");

    public function __construct(){}

    public function checkIP(){
        $isValidIP = false;
        $clientIP = $this->getClientIP();
        if('unknown' !== $clientIP){
            foreach($this->allowIPv4 as $ipv4){
                if($this->checkInRange($ipv4, $clientIP)){
                    $isValidIP = true;
                    break; 
                }            
            }
        }
        return $isValidIP;
    }

    private function checkInRange($ipv4, $clientIP){
        $isInRange = false;
        $checkType = $this->getCheckType($ipv4);
        //TODO check ipv6
        switch($checkType){
            case self::IP_TYPE_SINGLE:
                $isInRange = ($ipv4 == $clientIP); 
                break;
            case self::IP_TYPE_SECTION:
                $tmpArr = explode('-', $ipv4);
                $ipStart = trim($tmpArr[0]);
                $ipEnd = trim(substr($ipv4, 0, strripos($ipv4, '.'))).'.'.trim($tmpArr[1]);
                $isInRange = bindec(decbin(ip2long($clientIP))) >= bindec(decbin(ip2long($ipStart))) && bindec(decbin(ip2long($clientIP))) <= bindec(decbin(ip2long($ipEnd)));
                break;
            case self::IP_TYPE_WILDCARD:
                $ipStart = str_replace('*', '0', $ipv4);
                $ipEnd = str_replace('*', '255', $ipv4);
                $isInRange = bindec(decbin(ip2long($clientIP))) >= bindec(decbin(ip2long($ipStart))) && bindec(decbin(ip2long($clientIP))) <= bindec(decbin(ip2long($ipEnd)));
                break;
            default: break;

        }
        return $isInRange;
    }

    private function getCheckType($ipv4){
        if(strpos($ipv4, '*')){
           $type = self::IP_TYPE_WILDCARD; 
        }else if(strpos($ipv4, '-')){
            $type = self::IP_TYPE_SECTION;
        }else{
            $type = self::IP_TYPE_SINGLE;
        }
        return $type;
    }

    private function getClientIP(){
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")){
            $ip = getenv("HTTP_CLIENT_IP");
        }else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")){
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        }else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")){
            $ip = getenv("REMOTE_ADDR");
        }else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")){
            $ip = $_SERVER['REMOTE_ADDR'];
        } else{
            $ip = "unknown";
        }
        return $ip;
    }
}
?>
