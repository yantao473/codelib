<?php
header('content-type: application/javascript');
if (isset($_REQUEST['http2'])) {
    echo "var h2Version='". $_REQUEST['http2'] . "';\n";
}
