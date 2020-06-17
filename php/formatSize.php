<?php
function formatSize($byte)
{
    if (0 == $byte) {
        return '0 bytes';
    } else {
        $filesizename = array("bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
        $byte = (float)$byte; //屏蔽sae警告
        $i = floor(log($byte, 1024));
        return (round($byte / pow(1024, $i), 2) . ' ' . $filesizename[$i]);
    }
}
