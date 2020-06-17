<?php
/**
 * 获取IP地址
 * @return string
 */
function getRemoteIp()
{
    if (isset($_REQUEST['cip'])) {
        $cip = $_REQUEST['cip'];
    } else if (isset($_REQUEST['ip'])) {
        $cip = $_REQUEST['ip'];
    } else if (isset($_REQUEST['remote_addr'])) {
        $cip = $_REQUEST['remote_addr'];
    } else if (!empty ($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        if (false === strpos($cip, ',')) {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            //多个转发IP的，取第1个，那是用户最初的IP
            $arrIp = explode(', ', $cip);
            $cip = $arrIp[0];
        }
    } else if (!empty ($_SERVER ["REMOTE_ADDR"])) {
        $cip = $_SERVER ["REMOTE_ADDR"];
    } else {
        $cip = '';
    }
    return $cip;
}
