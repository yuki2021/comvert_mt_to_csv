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
            while (($buffer = fgets($fp)) !== false) {
                $temp_text = '';
                if(preg_match('/^DATE:(.*)$/', $buffer) == 1) {
                    $temp_text = $this->trim_data_text($buffer);
                    $this->registDate[] = $temp_text;
                    $this->updateDate[] = $temp_text;
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
}

$obj = new ComvertMtToCSV();
$obj->blog_url = 'https://www.ituki-yu2.net/entry/';
$obj->read_path = './text.txt';
$obj->write_path = './write_new_csv.csv';
$obj->file_load();
$obj->file_read();
exit();