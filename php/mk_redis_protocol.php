<?php
/**
 * 使用时 cat filename | redis-cli -s /tmp/redis.sock -a 'xxxx' --pipe
 * 使用时 cat filename | redis-cli host:port -a 'xxxx' --pipe
 * */
date_default_timezone_set('Asia/Shanghai');
ini_set('display_errors', 1);
error_reporting(4096);
ini_set('memory_limit', '512M');
set_time_limit(0);

define('BUFF_LIMIT', 5000);

if ($argc < 2) {
    echo "Usage php {$argv[0]} filename" . PHP_EOL;
    exit (0);
}

$fileName = $argv[1];
$fname = pathinfo($fileName, PATHINFO_FILENAME);
$arr = file($fileName);
mk_redis_protocol($arr, $fname);

// 对于一条命令LPUSH llist_mail 1075451854 转为 redis protocol的方法
// 1. 先将命令转换为 ['LPUSH','llist_mail','1075451854']
// 2. 计算list长度为x 记下*x\r\n
// 3. 计算list每一项a的长度记为k 记下$k\r\na\r\n
// 4. 将两次记下的字串连接 *x\r\n$k\r\na\r\n
function mk_redis_protocol($arr, $name) {
    $fn = "{$name}_redis_protocol.txt";

    $list = "llist_{$name}";
    $listLen = strlen($list);

    $cmd = 'LPUSH';
    $cmdLen = strlen($cmd);

    $cmdArrLen = 3;  // 确认操作命令为 LPUSH $list $v 长度固定，因此简化构造数组过程

    $cmdBuff = [];

    foreach($arr as $v) {
        $cmdBuff[] = sprintf("*%d\r\n$%d\r\n%s\r\n$%d\r\n%s\r\n$%d\r\n%s\r\n", $cmdArrLen, $cmdLen, $cmd, $listLen, $list, strlen($v), $v);

        if(count($cmdBuff) >= BUFF_LIMIT) {
            file_put_contents($fn, implode('', $cmdBuff), FILE_APPEND|LOCK_EX);
            $cmdBuff = [];
        }
    }

    if($cmdBuff) {
        file_put_contents($fn, implode('', $cmdBuff), FILE_APPEND|LOCK_EX);
    }
}
