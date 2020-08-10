<?php
/**
 *检测文件中每一行是否包含中文
 */
set_time_limit(0);
$path = "../v36_js_dev/";
rdir($path);

function rdir($path){
    $d = opendir($path);
    if($d){
        while (false !== ($fn = readdir($d))){
            if(strpos($fn, '.') === 0) continue;
            $pf = $path . '/'. $fn;
            if(is_dir($pf)){
                rdir($pf); 
            }else{
                if(preg_match('/\.js$/', $pf)){
                    check($pf);
                }
            }
        }
    }
}


function check($fn){
    header("content=text/html; charset=utf-8");
    $pattern = "/[\x80-\xff]./";
    $fp = fopen($fn, 'rb');
    $lineCount  = 0;
    $logfile = "js_html.txt";
    // $haveChinese = false;

    $data = array();
    while($line = trim(fgets($fp))){
        $lineCount++;
        if(preg_match($pattern, $line)){

            //for php and js
            if(preg_match('/^\s*?(\/\/|#|\*|\/\*)/s', $line) || preg_match('/\/\/|\/\*.*?\*\//s', $line) || preg_match('/<!--.*?-->|{\?\*.*?\*\?}/s', $line) ){
                continue;
            //for html
            // if(preg_match('/<!--.*?-->|{\?\*.*?\*\?}/', $line)){
                // continue;
            }else{
                $haveChinese = true;
                $de = mb_detect_encoding($line, 'GBK,GB2312,BIG5,UTF-8,ASCII');
                $de = 'CP936' == $de ? 'GBK' : $de;
                if('UTF-8' != $de){
                    $info =$fn . ', line: '. $lineCount. ', ';
                    echo $info . $line. '<br />';
                    file_put_contents('js_html_'.$de.'.txt', $info. $line."\n", FILE_APPEND);
                }
                else{
                    $info =$fn . ', line:'. $lineCount. ', ';
                    echo $info . $line. '<br />';
                    file_put_contents($logfile, $info. $line."\n", FILE_APPEND);
                }
            }
        }
    }
    // $haveChinese && file_put_contents($logfile, "\n", FILE_APPEND);
    fclose($fp);
}

