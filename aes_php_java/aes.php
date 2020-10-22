<?php

$data = 'hello';
$key = 'MGQ0YzU3YWQzYWVjMzliN2NhZmIyZjllNGYxZjYy';

$encode = encrypt($key, $data);
echo 'encrypt: ' . $encode . PHP_EOL;

// echo base64UrlDecode($encode).PHP_EOL;


// $encode = 'W8ZIIlhw5GLDLCvOAs06o7AU_z1vVE8IQ9MKz6MSEes=';
echo decrypt($key, base64UrlDecode($encode));


function encrypt($secKey, $validationString) {
    $ivLength = openssl_cipher_iv_length('AES-256-CBC');
    $iv = openssl_random_pseudo_bytes($ivLength, $isStrong);

    $digestSeed = hash('SHA512', $secKey, true);
    $seedEncArray = substr($digestSeed, 0, 32);

    $finalByteArray = openssl_encrypt(
        $validationString,
        'AES-256-CBC',
        $seedEncArray,
        OPENSSL_RAW_DATA,
        $iv
    );

    return base64UrlEncode($iv . $finalByteArray);
}

function decrypt($secKey, $validationString) {
    $digestSeed = hash('SHA512', $secKey, true);
    $seedEncArray = substr($digestSeed, 0, 32);

    $iv = substr($validationString, 0, 16);
    $content = substr($validationString, 16);

    $finalByteArray = openssl_decrypt(
        $content,
        'aes-256-cbc',
        $seedEncArray,
        OPENSSL_RAW_DATA,
        $iv,
    );

    return $finalByteArray;
}


function base64UrlEncode($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64UrlDecode(string $data)
{
    // return base64_decode($data);
    return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
}
