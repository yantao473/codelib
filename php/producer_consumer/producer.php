<?php
error_reporting(4096);
ini_set('display_errors', 1);
set_time_limit(0);

include 'lib/common/DBConnect.class.php';
include 'lib/common/MRedis.class.php';

define('RD_LIMIT', 5000);
define('REDIS_RECORDS', 200000);
define('QUEUE_NAME', '000x_uniq_sha1s');


if($argc != 2) {
    echo "Usage php {$argv[0]} port" . PHP_EOL;
    exit(0);
}

$port = $argv[1];
$ports = [ 0001, 0002, 0003, 0004, 0005, 0006, 0007, 0008 ];

if(!in_array($port , $ports)){
    echo "invalid port" . PHP_EOL;
    exit(0);
}

$index = array_search($port, $ports);

$ins = DBConnect::getInstance();
$redis = new MRedis('XXXXXXXXXXXX', '6379', 'zuishaobawei');

$ctime = strtotime('2019-09-10 00:00:00');
$tpl = 'SELECT digest_id,uid,sha1,bytes AS `size`,`type` FROM file_digest_%03d WHERE digest_id > %d AND ctime < %d ORDER BY digest_id ASC LIMIT %d';

for ($i = $index * 16; $i < ($index + 1) * 16; $i++) {
    $last_digest_id = 0;

    while(true){
        do {
            $len = $redis->llen(QUEUE_NAME);
            sleep(5);
        }while($len >= REDIS_RECORDS);

        $sql = sprintf($tpl, $i, $last_digest_id, $ctime, RD_LIMIT);
        echo $sql . PHP_EOL;
        $result = $ins->get($port, $sql);
        if($result){
            $last_digest_id = end($result)['digest_id'];
            foreach($result as $r){
                $r['port'] = $port;
                $redis->rpush(QUEUE_NAME, json_encode($r));
            }

            sleep(10);
        }else{
            break;
        }
    }
}

function p($data){
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}
