<?php

$blog_url = 'https://www.ituki-yu2.net/entry/';

$temp_csv[0] = '登録日時';
$temp_csv[1] = '更新日時';
$temp_csv[2] = '記事タイトル';
$temp_csv[3] = 'URL';

$fp = fopen('./text.txt','r');
if ($fp) {
    while (($buffer = fgets($fp)) !== false) {
        if(preg_match('/^BASENAME:(.*)$/', $buffer) == 1) {
            echo $buffer;
        }
    }
    if (!feof($fp)) {
        echo "Error: unexpected fgets() fail\n";
    }
    fclose($fp);
}