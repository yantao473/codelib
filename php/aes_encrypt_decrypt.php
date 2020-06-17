<?php
define('ENCRYPT_METHOD', 'AES-128-CBC');
define('ACCESS_KEY', 'e1d3bcf4-38d5-41cd-9422-c3629277b7d3');

// $passwd = uniqid('', true);
// echo "ori password: " . $passwd . PHP_EOL;
// $userInitPassword =encrypt($passwd, ACCESS_KEY);
// echo "encrypt password: " . $userInitPassword. PHP_EOL;

// $userInitPassword = 'Hgvcf+jxBWUgFA6TrPPe9FDpZPBvlC9dUwn0v3Y8kKNtoWNdu+QKKQoyqx0sz1tf2Oc5JlJu6jcRa+si';
// $decrypt  = decrypt($userInitPassword, ACCESS_KEY);
// echo "decrypt password: " . $decrypt. PHP_EOL;
// echo encrypt('sinamail_5e1bcdf56c88c7.27237083', ACCESS_KEY);

if($argc != 3){
	echo "Usage php {$argv[0]} method key" . PHP_EOL;
	exit(0);
}

$method = $argv[1];
$kstr = $argv[2];

if(!in_array($method, array('encrypt', 'decrypt'))){
	echo "Inalid method method must be encrypt or decrypt" . PHP_EOL;
	exit(0);
}

if($method == 'encrypt'){
	$result = encrypt($kstr, ACCESS_KEY);
}else{
	$result = decrypt($kstr, ACCESS_KEY);
}

echo $result . PHP_EOL;


function encrypt($encrypt, $key) {
    $ivLength = openssl_cipher_iv_length(ENCRYPT_METHOD);
    $bytes = openssl_random_pseudo_bytes($ivLength, $isStrong);
    $b64Str = base64_encode($bytes);
    $iv = substr($b64Str, 0, $ivLength);  // 取 $ivLength 位
    $key2 = substr(sha1(sha1($key, true),true), 0, 16);
    $result = openssl_encrypt($encrypt, ENCRYPT_METHOD, $key2, false, $iv);

    return $iv . $result;
}

function decrypt($decrypt, $key) {
    $iv = substr($decrypt, 0, 16);
    echo 'iv: ' . $iv . PHP_EOL;
    echo 'key: ' . $key . PHP_EOL;

    $key2 = substr(sha1(sha1($key, true),true), 0, 16);
    $result = openssl_decrypt(substr($decrypt,16), ENCRYPT_METHOD, $key2, false, $iv);

    return $result;
}

