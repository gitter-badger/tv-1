<?php
/*
 * echo str_replace("\/","/",json_encode(Wasu::parse($url)));      
 */
namespace ZhiBo;
class Wasu   
{  
  
    public static function parse($url)  
    {  
        /*if(GlobalBase::is_ipad()){ 
            if(strstr($url,"www.wasu.cn/Play/")==true){ 
                $url = str_replace("Play","wap/Play",$url); 
            } 
            $content = self::curl($url); 
            preg_match("#'vid'\s*:\s*'(\d+)',#",$content,$playId); 
            preg_match("#'key'\s*:\s*'(.*)',#",$content,$playKey); 
            preg_match("#'url'\s*:\s*'(.*)',#",$content,$playUrl); 
            $ids['vid'] = $playId[1]; 
            $ids['key'] = $playKey[1]; 
            $ids['vurl'] = $playUrl[1]; 
            $ids['url'] = $url; 
 
            $data = self::get_wap_videos($ids); 
        }else{ */  
            if(strstr($url,"www.wasu.cn/wap/")==true){  
                $url = str_replace("/wap","",$url);  
            }  
            $content = self::curl($url);  
            preg_match("#var\s*_playId\s*=\s*'(\d+)',#",$content,$playId);  
            preg_match("#_playKey\s*=\s*'(.*)',#",$content,$playKey);  
            preg_match("#_playUrl\s*=\s*'(.*)',#",$content,$playUrl);  
            preg_match("#_playUrlHls\s*=\s*'(.*)',#",$content,$playUrlHls);  
            preg_match("#_playpic\s*=\s*'(.*)',#",$content,$posters);  
  
            preg_match("#_sid_=(.*),_cid#",$content,$playId2);  
  
            $ids['vid'] = isset($playId[1]) ? $playId[1] : $playId2[1];  
            $ids['poster'] = isset($posters[1]) ? $posters[1] : '' ;  
            $ids['key'] = $playKey[1];  
            $ids['url'] = $url;  
            $ids['vurl'] = $playUrl[1];  
            $ids['hurl'] = $playUrlHls[1];  
            //print_r($ids);exit;  
            $data = self::get_videos($ids);  
        /*} */  
        return $data;  
    }  
    public static function get_videos($ids)  
    {  
        $api = "https://www.wasu.cn/Api/getPlayInfoById/id/{$ids['vid']}/datatype/xml";  
        $ids['ref'] = 'https://www.wasu.cn/Play/show/id/'.$ids['vid'];  
        $ids['ua'] = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36';  
        $data = self::curl($api,$ids);  
        $txt = $data;  
        $pa = '%<title><!\[CDATA\[(.*)\]\]></title>%si';//正则表达式  
        preg_match_all($pa,$txt,$matches);  
        $xml = simplexml_load_string($data);  
        $json = json_decode(json_encode($xml),true);  
        $mp4 = $json["mp4"];  
        foreach ($mp4 as $_key => $value) {  
            switch ($_key) {  
                case 'hd0':$def = "标清";break;  
                case 'hd1':$def = "高清";break;  
                case 'hd2':$def = "超清";break;  
                case 'hd3':$def = "720P";break;  
                case 'hd4':$def = "1080P";break;  
            }  
            $value = base64_encode(base64_decode($value));  
            //$value = base64_encode(str_replace('.mp4','/playlist.m3u8',base64_decode($value)));  
            $vurl = self::streamCode(self::get_code($ids['vid'],$ids['key'],$value));  
  
            if (GlobalBase::is_ipad()) {  
                if ($_key == 'hd2' && $vurl != '') {  
                    $fdata[2]['url'] = $vurl;  
                    $fdata[2]['def'] = '高清';  
                }   
                if ($_key == 'hd1' && $vurl != '') {  
                    $fdata[3]['url'] = $vurl;  
                    $fdata[3]['def'] = '流畅';  
                }  
                if ($_key == 'hd0' && $vurl != '') {  
                    $fdata[4]['url'] = $vurl;  
                    $fdata[4]['def'] = '流畅';  
                }  
            } else {  
                if ($_key == 'hd4' && $vurl != '') {  
                    $fdata[0]['url'] = $vurl;  
                    $fdata[0]['def'] = '蓝光';  
                }   
                if ($_key == 'hd3' && $vurl != '') {  
                    $fdata[1]['url'] = $vurl;  
                    $fdata[1]['def'] = '720P';  
                }  
                if ($_key == 'hd2' && $vurl != '') {  
                    $fdata[2]['url'] = $vurl;  
                    $fdata[2]['def'] = '高清';  
                }   
                if ($_key == 'hd1' && $vurl != '') {  
                    $fdata[3]['url'] = $vurl;  
                    $fdata[3]['def'] = '流畅';  
                }  
                if ($_key == 'hd0' && $vurl != '') {  
                    $fdata[4]['url'] = $vurl;  
                    $fdata[4]['def'] = '流畅';  
                }  
            }  
        }  
        for ($i=0; $i <= 5 ; $i++) {   
            if ($fdata[$i] == '') {continue;}  
            $key_arrays[]=$fdata[$i];  
        }  
        $videoinfo['code'] = 200;  
        $videoinfo['poster'] = $json['snapshot'];  
        $videoinfo['name'] = isset($matches[1][0]) ? $matches[1][0] : '';  
        $videoinfo['play'] = 'h5mp4';  
        $videoinfo['data']['url'] = $key_arrays[0]['url'];  
        return $videoinfo;  
    }  
    public static function get_wap_videos($ids)  
    {  
        $api = "http://clientapi.wasu.cn/Phone/vodinfo/id/{$ids['vid']}";  
        $data = self::curl($api,$ids);  
        $json = json_decode($data,true);  
        $vods = $json["vods"];  
        $host = parse_url(base64_decode($ids['vurl']))["host"];  
        foreach ($vods as $key => $value) {  
            $def = $value["hd"];  
            $vurl = $value["url"];  
            $vurl = str_replace(parse_url($vurl)["host"],$host,$vurl);  
            $vurl = str_replace('.mp4','/playlist.m3u8',$vurl);  
            $vurl = self::streamCode(self::get_code($ids['vid'],$ids['key'],base64_encode($vurl)));  
            $video[0] = $vurl;  
            $video[1] = "video/m3u8";  
            $video[2] = $def;  
            $video[3] =  $def =="1080P"? 10 : $def =="高清"?10:0;  
            $videoinfo["video"][$key] = $video;  
        }  
        return $videoinfo;  
    }  
    public static function get_code($vid,$key,$url)  
    {  
        $api = "https://apiontime.wasu.cn/Auth/getVideoUrl?id={$vid}&mode=1&key={$key}&url={$url}";  
        $data = self::curl($api);  
        preg_match("#\[CDATA\[(.*)\]\]#",$data,$video);  
        return $video[1];  
    }  
    public static function curl($url,$ids='')  
    {  
        $params["ua"] = isset($ids['ua']) ? $ids['ua'] : "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";  
        return GlobalBase::curl($url,$params);  
    }  
  
    //=======================================================================================================================================  
    public static function streamCode($string, $operation = 'DECODE', $key = 'wasu!@#48217#$@#1', $expiry = 0){  
        $ckey_length = 4;  
        $key = md5($key ? $key : '12345678');  
        $keya = md5(substr($key, 0, 16));  
        $keyb = md5(substr($key, 16, 32));  
        $keyc = $ckey_length ? $operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length) : '';  
        $cryptkey = $keya . md5($keya . $keyc);  
        $key_length = strlen($cryptkey);  
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;  
        $string_length = strlen($string);  
        $result = '';  
        $box = range(0, 255);  
        $rndkey = array();  
        for($i = 0; $i <= 255; $i++){  
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);  
        }  
        for($j = $i = 0; $i < 128; $i++){  
            $j = ($j + $box[$i] + $rndkey[$i]) % 128;  
            $tmp = $box[$i];  
            $box[$i] = $box[$j];  
            $box[$j] = $tmp;  
        }  
        for($a = $j = $i = 0; $i < $string_length; $i++){  
            $a = ($a + 1) % 128;  
            $j = ($j + $box[$a]) % 128;  
            $tmp = $box[$a];  
            $box[$a] = $box[$j];  
            $box[$j] = $tmp;  
            $result .= chr(ord($string[$i]) ^ $box[($box[$a] + $box[$j]) % 128]);  
        }  
        if($operation == 'DECODE'){  
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)){  
                return substr($result, 26);  
            }else{  
                return '';  
            }  
        }else{  
            return $keyc . base64_encode($result);  
        }  
    }  
    public static function _streamCode($enstr, $token = "wasu!@#48217#$@#1") {  
        $klen = 4;  
        if (strstr($enstr,".mp4")) {  
            return $enstr;  
        }  
        $token = md5($token);  
        $tm = $_SERVER['REQUEST_TIME'];  
        $key1 = md5(substr($token, 0, 16));  
        $key2 = md5(substr($token, 16, 32));  
        $key3 = substr($enstr, 0, $klen);  
        $key4 = $key1.md5($key1.$key3);  
        $keylen = strlen($key4);  
        $enstr = base64_decode(substr($enstr, $klen));  
        $l16 = 0;  
        $l14 = array();  
        $l15 = array();  
        $local13 = strlen($enstr);  
        while ($l16 < 128){  
            $l14[$l16] = $l16;  
            $l15[$l16] = ord(substr($key4, $l16 % $keylen, 1)) & 0xff;  
            $l16++;  
        }  
        $l16 = 0;  
        $l17 = $l16;  
        while ($l16 < 128) {  
            $l17 = (($l17 + $l14[$l16]) + $l15[$l16]) % 128;  
            $l19 = $l14[$l16];  
            $l14[$l16] = $l14[$l17];  
            $l14[$l17] = $l19;  
            $l16++;  
        }  
        $l16 = 0;  
        $l20 = array();  
        $l17 = $l16;  
        $l18 = $l17;  
        while ($l16 < $local13) {  
            $l18 = (($l18 + 1) % 128);  
            $l17 = (($l17 + $l14[$l18]) % 128);  
            $l19 = $l14[$l18];  
            $l14[$l18] = $l14[$l17];  
            $l14[$l17] = $l19;  
            $l20[] = ((ord(substr($enstr, $l16, 1)) & 0xFF) ^ $l14[(($l14[$l18] + $l14[$l17]) % 128)]);  
            $l16++;  
        }  
        $toStr = "";  
        for($i = 0; $i < count($l20); $i++){  
            $toStr .= chr($l20[$i]);  
        }  
        return strlen($toStr) > 26 ? substr($toStr, 26) : "";  
    }  
  
    public static function get_WasuVODUrl($url) {  
        $play = parse_url($url);  
        $tm = $_SERVER['REQUEST_TIME'];  
        $timestamp = date("YmdHi", $tm);  
        $token = sprintf("%s%s%s", "ccVOD@)!#\$WASUPC", $timestamp, $play['path']);  
        return sprintf("http://clientvod.wasu.cn/%s/%s%s", $timestamp, md5($token), $play['path']);  
    }  
} 