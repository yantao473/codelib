<?php

if($argc != 2){
    echo "Usage: php {$argv[0]} word";
    exit(0);
}

// $word = 'assumption';
$word = $argv[1];

$content = sendReq($word);

$matches = [];

$c = $word;
if(preg_match('/<div class="row" id="yd-word-pron">(.*?)</is', $content, $matches)){
    $m = $matches[1];
    $m = br2space($m);
    $c .= ','. $m . ',';
}

if(preg_match('/<div class="row" id="yd-word-meaning">(.*?)<\/div>/is', $content, $matches)){
    $m = trim(strip_tags($matches[1]));
    $c .=  br2space($m);
}

echo  $c.PHP_EOL;



function br2space($m){
    $m .= str_replace(',', ';',$m);
    $m = preg_replace("/\r\n/is", " ", $m);//回车符是\r\n
    $m = preg_replace("/\r/is", " ", $m);//回车符是\r
    $m = preg_replace("/\n/is", " ", $m);//回车符是\n
    $m = preg_replace("/\n/is", " ", $m);//回车符是\n
    return $m;
}

function sendReq($word){
    $url = 'https://www.quword.com/w/'.$word;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $content = curl_exec($ch);
    curl_close($ch);

    return $content;
}
