<?php

$blog_url = 'https://www.ituki-yu2.net/entry/';

$registDate = [];
$updateDate = [];
$titleArr = [];
$urlArr = [];

function file_load() {

    global $registDate, $updateDate, $titleArr, $urlArr;

    $fp = fopen('./text.txt','r');
    if ($fp) {
        while (($buffer = fgets($fp)) !== false) {
            $temp_text = '';
            if(preg_match('/^DATE:(.*)$/', $buffer) == 1) {
                $temp_text = trim_data_text($buffer);
                $registDate[] = $temp_text;
                $updateDate[] = $temp_text;
                continue;
            }
            if(preg_match('/^BASENAME:(.*)$/', $buffer) == 1) {
                $temp_text = trim_url_text($buffer);
                $urlArr[] = $temp_text;
                continue;
            }
            if(preg_match('/^TITLE:(.*)$/', $buffer) == 1) {
                $temp_text = trim_title_text($buffer);
                $titleArr[] = $temp_text;
                continue;
            }
        }
        fclose($fp);
        
    }
}

function file_read() {

    global $registDate, $updateDate, $titleArr, $urlArr;

    $fp2 = fopen('./write_new_csv.csv', 'c+');
    foreach($registDate as $key => $loop) {
        $temp_text = '';
        $temp_text = '"'. $loop .'","'. $updateDate[$key] .'","'. $titleArr[$key] .'","'. $urlArr[$key] .'"'. ",\n";
        if(fwrite($fp2, $temp_text) == false) {
            print("ファイル書き込みに失敗しました:". $temp_text);
            break;
        }
    }
    fclose($fp2);
}

function trim_data_text($date) {
    $temp = trim($date, 'DATE: ');
    $temp = trim($temp);
    return $temp;
}

function trim_url_text($url) {

    global $blog_url;

    $temp = trim($url, 'BASENAME: ');
    $temp2 = $blog_url . $temp;
    $temp2 = trim($temp2);
    return $temp2;
}

function trim_title_text($title) {
    $temp = trim($title, 'TITLE: ');
    $temp = trim($temp);
    return $temp;
}

file_load();
file_read();
exit();