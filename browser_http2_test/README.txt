网站是否支持http2检测

http://stackoverflow.com/questions/27211134/detect-http2-spdy-support-in-browser

Thanks, Patrick. I took your advice and leveraged nginx's $http2 variable and used PHP to return a dynamic JS variable for the browser's detection. (Passing a cookie or adding a response header for AJAX detection are also options if other readers prefer).

NGINX config additions

server {
    ...

    if ($http2) { 
        rewrite ^/detect-http2.js /detect-http2.js.php?http2=$http2 last;
    }
    # fallback to non-HTTP2 rewrite
    rewrite ^/detect-http2.js /detect-http2.js.php last;

    # add response header if needed in the future
    add_header x-http2 $http2;
}

detect-http2.js.php
<?php
    header('content-type: application/javascript');
    if (isset($_REQUEST['http2'])) {
        echo "var h2Version='". $_REQUEST['http2'] . "';\n";
    }
?>

detect-http2.html
<html>
    <body>
        <script src="https://DOMAIN_NAME/detect-http2.js"></script>
        <script>
            document.write('HTTP2-Supported Browser: '+ (typeof h2Version !== 'undefined'));
        </script>
    </body>
</html>
