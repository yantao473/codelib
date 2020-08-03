<?php

//  获取子网长度前缀
function getMaskBits($mask){
  return strlen(preg_replace('/0/', '', decbin(ip2long($mask))));
}

// 演示：
$mask = '255.240.0.0';
echo 'mask prefix len: ' . getMaskBits($mask).PHP_EOL;

// 子网掩码计算范围
function maskParse($ipStr) {
    $maskLen = 32;

    if (strpos($ipStr, '/') > 0) {
        list($ipStr, $maskLen) = explode('/', $ipStr);
    }

    $ip = ip2long($ipStr);
    $mask = 0xFFFFFFFF << (32 - $maskLen) & 0xFFFFFFFF;

    $ipStart = ($ip & $mask) + 1; // 忽略网关
    $ipEnd = ($ip | (~$mask) & 0xFFFFFFFF) - 1; // 忽略广播地址

    return array($ip, $mask, $ipStart, $ipEnd);
}

// 演示：
list($ip, $mask, $ipStart, $ipEnd) = maskParse('192.168.1.12/24');

echo 'IP地址 : ', long2ip($ip), PHP_EOL;
echo '子网掩码: ', long2ip($mask), PHP_EOL;
echo 'IP段开始: '. $ipStart . ' '.  long2ip($ipStart), PHP_EOL;
echo 'IP段结束: '.$ipEnd. ' ' .long2ip($ipEnd), PHP_EOL;


// ip是否在ip分段中：
function isInIPRange($ip, $ipStr) {
    $maskLen = 32;

    if (strpos($ipStr, '/') > 0) {
        list($ipStr, $maskLen) = explode('/', $ipStr);
    }

    $rightLen = 32 - $maskLen;
    return ip2long($ip) >> $rightLen == ip2long($ipStr) >> $rightLen;
}

// 演示：
$isInRange = isInIPRange('192.168.1.1', '192.168.1.0/24');
var_dump($isInRange);
