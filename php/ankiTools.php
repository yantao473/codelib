<?php
if ($argc != 2) {
    echo "Usage: php {$argv[0]} word";
    exit(0);
}

// mp3 https://dict.youdao.com/dictvoice?audio=scarcely&type=2
// img https://www.quword.com/images/words/testify1.jpg
// 朗文 https://www.ldoceonline.com/dictionary/testify

$word = $argv[1];
$c = $word . ';';
$matches = [];


// 获取朗文字典数据
$ldoUrl = 'https://www.ldoceonline.com/dictionary/' . $word;
$ldoContent = sendReq($ldoUrl);

// 获取分节信息
if (preg_match('#<span class="HYPHENATION">(.*?)</span>#is', $ldoContent, $matches)) {
    if ($matches && $matches[1]) {
        $c .= $matches[1] . ';';
    }
}

// 获取英语解释
$engyi = '';
if (preg_match_all('#span class="DEF">(.*?)</span>#is', $ldoContent, $matches)) {
    if ($matches && $matches[1]) {
        foreach ($matches[1] as $k => $match) {
            $engyi .= ($k + 1) . ' ' . trim(strip_tags($match)) . '<br>';
        }
    }
}
$engyi = trim($engyi, '<br>');


// 美式发音 mp3 数据 type=1 是英式英语 type=2是美式英语
$sound = '';
$mp3Url = sprintf('https://dict.youdao.com/dictvoice?audio=%s&type=2', $word);
$mp3Name = sprintf('%s.mp3',  $word);
$mp3Path = sprintf('media/%s',  $mp3Name);
downCurl($mp3Url, $mp3Path);
if (file_exists($mp3Path) && mime_content_type($mp3Path) === "audio/mpeg") {
    $sound = sprintf('[sound:%s]', $mp3Name);
}

// 获取图片
$img = '';
$imgUrl = sprintf('https://www.quword.com/images/words/%s1.jpg', $word);
$imgName = sprintf('%s.jpg', $word);
$imgPath = sprintf('media/%s', $imgName);
downCurl($imgUrl, $imgPath);
if (file_exists($imgPath) && mime_content_type($imgPath) === "image/jpeg") {
    $img = sprintf('<img src="%s"/>', $imgName);
}

// 趣词数据
$qwUrl = 'https://www.quword.com/w/' . $word;
$qwcontent = sendReq($qwUrl);

if (preg_match('#<div class="row" id="yd-word-pron">(.*?)</div>#is', $qwcontent, $matches)) {
    if ($matches) {
        $m = $matches[1];
        $m = nr2space($m);
        $c .= $m . ';';
    }
}

if ($sound) {
    $c .= $sound . ';';
} else {
    $c .= ';';
}

if (preg_match('/<div class="row" id="yd-word-meaning">(.*?)<\/div>/is', $qwcontent, $matches)) {
    $m = trim(strip_tags($matches[1]));
    $c .= nr2br($m) . ';';
}

if ($engyi) {
    $c .= $engyi . ';';
} else {
    $c .= ';';
}
if ($img) {
    $c .= $img . ';';
} else {
    $img .= ';';
}


//  获取例句
$liju = '';
if (preg_match('#<div\s*class="row"\s*?id="yd-liju">(.*?)</div>#is', $qwcontent, $matches)) {
    if ($matches && $matches[1]) {
        $dtmatches = [];
        if (preg_match_all('#<dt>(.*?)</dd>#is', $matches[1], $dtmatches)) {
            if ($dtmatches && $dtmatches[1]) {
                $dts = $dtmatches[1];
                $tmpArr = [];
                foreach ($dts as $dt) {
                    $dt = str_replace('</dt>', '', $dt);
                    $dt = str_replace('<dd>', '', $dt);
                    $tmpArr[] = $dt;
                }

                $liju = implode('<br>', array_slice($tmpArr, 0, 3));
            }
        }
    }
}

if ($liju) {
    $c .= $liju . ';';
} else {
    $c .= ';';
}

// 记忆方法
$jiyi = '';
if (preg_match('#记忆方法</h3>.*?</div>(.*?)</div>#is', $qwcontent, $matches)) {
    if ($matches && $matches[1]) {
        $m = nr2space($matches[1]);
        $m = preg_replace('#<br\s*?/*?>#', '\n', $matches[1]);
        $m = strip_tags($m) . PHP_EOL;
        $m = str_replace('\n', '<br/>', $m);
        $jiyi = trim($m);
    }
}
if ($jiyi) {
    $c .= $jiyi;
} else {
    $c .= ';';
}

file_put_contents($word.'.csv', $c);

function nr2br($m)
{
    $patten = ["\r\n", "\r", "\n"];
    return str_replace($patten, '<br/>', $m);
}

function nr2space($m)
{
    $patten = ["\r\n", "\r", "\n"];
    return str_replace($patten, ' ', $m);
}

function sendReq($url)
{
    $headers = [
        'Expect:',
        'Cache-Control: no-cache',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $content = curl_exec($ch);
    curl_close($ch);

    return $content;
}

function downCurl($url, $filePath)
{
    $headers = [
        'Expect:',
        'Cache-Control: no-cache',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $fp = fopen($filePath, 'w+');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);

    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
}
