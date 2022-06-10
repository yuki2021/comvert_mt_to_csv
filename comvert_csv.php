<?php


class ComvertMtToCSV {

    public $blog_url = '';
    public $read_path = '';
    public $write_path = '';

    private $registDate = [];
    private $updateDate = [];
    private $titleArr = [];
    private $urlArr = [];

    function file_load() {

        $fp = fopen($this->read_path,'r');
        if ($fp) {
            $temp_text = '';
            $date_flg = false;
            while (($buffer = fgets($fp)) !== false) {
                if(preg_match('/^CONVERT BREAKS:(.*)$/', $buffer) == 1) {
                    $date_flg = true;

                }
                if(preg_match('/^DATE:(.*)$/', $buffer) == 1 && $date_flg) {
                    $temp_text = $this->trim_data_text($buffer);
                    $this->registDate[] = $temp_text;
                    $this->updateDate[] = $temp_text;
                    $date_flg = false;
                    continue;
                }
                if(preg_match('/^BASENAME:(.*)$/', $buffer) == 1) {
                    $temp_text = $this->trim_url_text($buffer);
                    $this->urlArr[] = $temp_text;
                    continue;
                }
                if(preg_match('/^TITLE:(.*)$/', $buffer) == 1) {
                    $temp_text = $this->trim_title_text($buffer);
                    $this->titleArr[] = $temp_text;
                    continue;
                }
            }
            fclose($fp);
            
        }
    }

    function file_read() {

        $fp2 = fopen($this->write_path, 'c+');
        foreach($this->registDate as $key => $loop) {
            $temp_text = '';
            $temp_text = '"'. $loop .'","'. $this->updateDate[$key] .'","'.
                    $this->titleArr[$key] .'","'. $this->urlArr[$key] .'"'. ",\n";
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
        $time_stamp = strtotime($temp);
        $date_str = date("Y-m-d\TH:i:s+09:00", $time_stamp);
        return $date_str;
    }

    function trim_url_text($url) {
        $temp = trim($url, 'BASENAME:');
        $temp = trim($temp);
        $temp = html_entity_decode($temp, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401);
        $temp = $this->encodeURI($temp);
        $temp2 = $this->blog_url . $temp;
        $temp2 = trim($temp2);
        return $temp2;
    }

    function trim_title_text($title) {
        $temp = trim($title, 'TITLE: ');
        $temp = trim($temp);
        return $temp;
    }

    function encodeURI($url) {
        // http://php.net/manual/en/function.rawurlencode.php
        // https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/encodeURI
        $unescaped = array(
            '%2D'=>'-','%5F'=>'_','%2E'=>'.','%21'=>'!', '%7E'=>'~',
            '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')'
        );
        $reserved = array(
            '%3B'=>';','%2C'=>',','%2F'=>'/','%3F'=>'?','%3A'=>':',
            '%40'=>'@','%26'=>'&','%3D'=>'=','%2B'=>'+','%24'=>'$'
        );
        $score = array(
            '%23'=>'#'
        );
        return strtr(rawurlencode($url), array_merge($reserved,$unescaped,$score));
    
    }
}

$obj = new ComvertMtToCSV();
$obj->blog_url = 'https://www.ituki-yu2.net/entry/';
$obj->read_path = './www.ituki-yu2.net.export.txt';
$obj->write_path = './write_new_csv.csv';
$obj->file_load();
$obj->file_read();
exit();