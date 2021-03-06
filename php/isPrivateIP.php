<?php
function isPrivateIp($ipString)
{
    $ip = ip2long($ipString);
    //内网 IP: 10.0.0.0 - 10.255.255.255; 172.16.0.0 - 172.31.255.255; 192.168.0.0 - 192.168.255.255 sae外网网段 220.181.129.0/24, 220.181.136.0/24, 220.181.84.0/24, 123.125.23.0/24, 61.172.201.0/24, 202.108.5.0/24
    // 在支持大整数的操作系统下 ip2long 会返回正整数,如 -1408237568 会得到 4294967296-1408237568 = 2886729728
    if (
        (167772160 <= $ip && $ip <= 184549375)
        || (-1408237568 <= $ip && $ip <= -1407188993)
        || (-1062731776 <= $ip && $ip <= -1062666241)
        || (2886729728 <= $ip && $ip <= 2887778303)
        || (3232235520 <= $ip && $ip <= 3232301055)

        || (-592084736 <= $ip && $ip <= -592084481)
        || (3702882560 <= $ip && $ip <= 3702882815)

        || (-592082944 <= $ip && $ip <= -592082689)
        || (3702884352 <= $ip && $ip <= 3702884607)

        || (-592096256 <= $ip && $ip <= -592096001)
        || (3702871040 <= $ip && $ip <= 3702871295)

        || (1034733824 <= $ip && $ip <= 1034734079)

        || (-898890496 <= $ip && $ip <= -898890241)
        || (3396076800 <= $ip && $ip <= 3396077055)

        || (2071795456 <= $ip && $ip <= 2071795711)
    ) {
        return true;
    }

    return false;
}
