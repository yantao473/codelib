<?php

class Pmetal{
    public function __construct(){

    }

    public function getInfo(){
        $dyzby_url = 'http://www.diyizby.com/jiage.php';
        $dyzby_info = $this->sendReq($dyzby_url);
        $dyzby_info = iconv('GB2312', 'UTF-8', strip_tags($dyzby_info));
        $dyzby_info = str_replace('纸白银最新价格-第一纸白银分析网提供', '', $dyzby_info);
        $dyzby_info = trim($dyzby_info);
        $dyzby_info = str_replace("\n", " ", $dyzby_info);

        $icbc_array = array();
        $icbc_url = 'http://www.icbc.com.cn/ICBCDynamicSite/Charts/GoldTendencyPicture.aspx';
        $icbc_info = $this->sendReq($icbc_url);

        $match = array();
        if(preg_match('/<table class="style_text" id="TABLE1" style="border-right.*?>.*?<\/table>/s', $icbc_info, $match)){
            $table = $match[0];
            $m = array();
            if(preg_match_all('/<td.*?>(.*?)<\/td>/s', $table, $m)){
                $tds = $m[1];
                if($tds){
                    //$len = count($tds);
                    //only use two items
                    for($k=0; $k < 18; $k+=9){
                        $md = array();
                        $direct = trim($tds[$k+1]);
                        if(preg_match('/src="images\/(.*?)"/s', $direct, $md)){
                            $direct = "img/".trim($md[1]);
                        }

                        $md = array();
                        $trend = trim($tds[$k+7]);
                        if(preg_match('/<a.*?href="(.*?)"/s', $trend, $md)){
                            $trend = "http://www.icbc.com.cn/ICBCDynamicSite/Charts/". trim($md[1]);
                        }

                        $icbc_array[] = array(
                            'category' => trim($tds[$k]),
                            'direct' => $direct,
                            'bank_buy_price' => trim($tds[$k+2]),
                            'bank_sell_price' => trim($tds[$k+3]),
                            'middle_price' => trim($tds[$k+4]),
                            'hightest_price' => trim($tds[$k+5]),
                            'lowest_preice' => trim($tds[$k+6]),
                            'trend' => $trend,
                        );
                    }
                }
            }
        }
        return array(
            'dyzby' => $dyzby_info,
            'icbc'=> $icbc_array
        );
    }

    private function sendReq($url, $timeout=3) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.186 Safari/537.36");
        $info = curl_exec($ch);
        curl_close($ch);
        return $info;
    }
}
