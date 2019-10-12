<?php
if ($argc != 3) {
    echo "Usage php {$argv[0]} file1 file2" . PHP_EOL;
    exit(0);
}

$s3FileName = $argv[1];
$vFileName = $argv[2];

$sfp = fopen($s3FileName, 'r');
$vfp = fopen($vFileName, 'r');

$s3Line = trim(fgets($sfp, 1024));
$vLine = trim(fgets($vfp, 1024));

$ons3Fname = 'only_in_file1.txt';
$on446xFname = 'only_in_file2.txt';

while (true) {
    if ($s3Line && $vLine) {
        if ($s3Line == $vLine) {
            $s3Line = trim(fgets($sfp, 1024));
            $vLine = trim(fgets($vfp, 1024));
            continue;
        } else {
            $s3Pos = strpos($s3Line, ',');
            $vPos = strpos($vLine, ',');

            $s3Key = substr($s3Line, 0, $s3Pos);
            $vKey = substr($vLine, 0, $vPos);
            if ($s3Key < $vKey) {
                file_put_contents($ons3Fname, $s3Line . PHP_EOL, FILE_APPEND);
                $s3Line = trim(fgets($sfp, 1024));
            } else {
                file_put_contents($on446xFname, $vLine . PHP_EOL, FILE_APPEND);
                $vLine = trim(fgets($vfp, 1024));
            }
        }
    }

    if (feof($sfp) || feof($vfp)) {
        if ($vLine) {
            file_put_contents($on446xFname, $vLine . PHP_EOL, FILE_APPEND);
        }

        if ($s3Line) {
            file_put_contents($ons3Fname, $s3Line . PHP_EOL, FILE_APPEND);
        }

        break;
    }
}

while (!feof($sfp)) {
    $s3Line = trim(fgets($sfp, 1024));
    if ($s3Line) {
        file_put_contents($ons3Fname, $s3Line . PHP_EOL, FILE_APPEND);
    }
}

while (!feof($vfp)) {
    $vLine = trim(fgets($vfp, 1024));
    if ($vLine) {
        file_put_contents($on446xFname, $vLine . PHP_EOL, FILE_APPEND);
    }
}

fclose($sfp);
fclose($vfp);
