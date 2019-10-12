<?php
error_reporting(4096);
ini_set('display_errors', 1);
set_time_limit(0);

include 'lib/common/DBConnect.class.php';
include 'lib/common/MRedis.class.php';

define('QUEUE_NAME', '446x_uniq_sha1s');
define('BASE_PATH', 'data/');

$ins = DBConnect::getInstance();
$redis = new MRedis('XXXXXXXXXXXX', '6379', 'zuishaobawei');

$pid = getmypid();

while(true){
    if($redis){
        $data = $redis->lpop(QUEUE_NAME);
        if($data){
            $uniqArr = json_decode($data, true);
            $port = $uniqArr['port'];
            $sha1 = $uniqArr['sha1'];

            $multiArr = scanTable($ins, $sha1);
            if($multiArr){
                $tmpArr = $uniqArr;
                unset($tmpArr['port']);
                unset($tmpArr['digest_id']);
                foreach($multiArr as $mk => $mv){
                    if(array_diff($mv, $tmpArr)){ // 与446x数据库的记录相同需要保留, 不相同的要做处理
                        if($mv['type'] == $tmpArr['type']){
                            //待删除数据
                            file_put_contents(BASE_PATH . 'to_be_delete_' . $pid . '.txt', implode(',', $mv). PHP_EOL, FILE_APPEND);
                        }else{
                            // 类型不匹配的数据待确认,记录原始数据和待验证数据
                            $fname = sprintf(BASE_PATH . 'chk_%s_%d.txt', $sha1, $pid);
                            if(file_exists($fname)){
                                file_put_contents($fname,"chk: ". implode(',', $mv). PHP_EOL, FILE_APPEND);
                            }else{
                                file_put_contents($fname,"ori: ". implode(',', $uniqArr). PHP_EOL);
                                file_put_contents($fname,"chk: ". implode(',', $mv). PHP_EOL, FILE_APPEND);
                            }
                        }
                    } else{
                        // for test
                        // p($mv);
                        // p($uniqArr);
                        // exit(0);
                    }
                }
            }else{
                // 仅存在于446x数据库, 待验证后删除
                file_put_contents(BASE_PATH . 'only_in_446x_'.$pid.'.txt', implode(',', $uniqArr) . PHP_EOL, FILE_APPEND);
            }
        }else{
            sleep(1);
        }
    }else{
        $redis = new MRedis('XXXXXXXXXXXX', '6379', 'zuishaobawei');
    }
}


function scanTable($ins, $sha1){
    $data = [];

    $tpl = 'SELECT uid,sha1,`size`,`type` FROM file_info_%03d WHERE sha1="%s"';
    for ($i = 0; $i < 128; $i++) {
        $port = $i < 64 ? 4380 : 4381;
        $sql = sprintf($tpl, $i, $sha1);
        $res = $ins->get($port, $sql);

        if($res){
            foreach($res as $v){
                $data[] = $v;
            }
        }
    }

    return $data;
}

function p($data){
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}
