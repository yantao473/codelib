<?php
function maskParse($ipStr) {
    $maskLen = 32;

    if (strpos($ipStr, '/') > 0) {
        list($ipStr, $maskLen) = explode('/', $ipStr);
    }

    $ip = ip2long($ipStr);
    $mask = 0xFFFFFFFF << (32 - $maskLen) & 0xFFFFFFFF;
    $ipStart = $ip & $mask;
    $ipEnd = $ip | (~$mask) & 0xFFFFFFFF;
    return array($ip, $mask, $ipStart, $ipEnd);
}

//演示：
list($ip, $mask, $ipStart, $ipEnd) = maskParse('192.168.1.12/24');

echo 'IP地址 : ', long2ip($ip), PHP_EOL;
echo '子网掩码: ', long2ip($mask), PHP_EOL;
echo 'IP段开始: $ipStart  ', long2ip($ipStart), PHP_EOL;
echo 'IP段结束: $ipEnd ', long2ip($ipEnd), PHP_EOL;

// 结果：
//
// IP地址 : 192.168.1.12
// 子网掩码: 255.255.255.0
// IP段开始: 192.168.1.0
// IP段结束: 192.168.1.255
//
// ip是否在ip分段中：

function isInIPRange($ip, $ipStr) {
    $maskLen = 32;
    if (strpos($ipStr, '/') > 0) {
        list($ipStr, $maskLen) = explode('/', $ipStr);
    }
    $rightLen = 32 - $maskLen;
    return ip2long($ip) >> $rightLen == ip2long($ipStr) >> $rightLen;
}

//演示：
$isInRange = isInIPRange('192.168.1.1', '192.168.1.0/24');
echo $isInRange . PHP_EOL;
