<?php
/*
 * $id = Le::parse($url);    
 * $video_info = Le::parseVideoUrl($id);   
 * echo json_encode($video_info);      
 */
namespace ZhiBo;
class Le  
{  
    public static function parse($url)  
    {  
        preg_match('#vplay/(.*?).htm#',$url,$ids);  
        if ($ids[1]) {  
            $name = "LE-".md5($ids[1]);  
        }else{  
            $name = "LE-".md5($url);  
        }  
  
        if (file_exists(NAME_PATH.$name)) {  
            $data = json_decode(file_get_contents(NAME_PATH.$name),true);  
            $pay = $data['pay'];  
            $vid = $data['vid'];  
            $title = $data['title'];  
            $poster = $data['poster'];  
        }else{  
            $content = self::curl($url);  
            preg_match('#vid: (.*?),#',$content,$vids);  
            preg_match('#title:"(.*?)",#',$content,$titles);  
            preg_match('#videoPic:"(.*?)",#',$content,$pics);  
            preg_match('#isPay: (.*?),#',$content,$isPay);  
            $vid = $vids[1];  
            $title = $titles[1];  
            $poster = !emptyempty($pics[1]) ? str_replace('320_200','640_320',$pics[1]) : '';  
            $pay = $isPay[1];//是否付费  
            file_put_contents(NAME_PATH.$name, json_encode(array("vid"=>$vid,"pay"=>$pay,"title"=>$title,"poster"=>$poster)));  
        }  
        return $vid;  
    }  
    public static function parseVideoUrl($vid){  
        $time = number_format(microtime(true),3,'.','');   
        $tkey = self::getMmsKey($time);  
        $tss = GlobalBase::is_ipad() ? "no" : "ios";  
        $splatid = GlobalBase::is_ipad() ? 107 : 105;  
        $domain = GlobalBase::is_ipad() ? 'm.le.com' : 'www.le.com';  
        $source = GlobalBase::is_ipad() ? '1001' : '1000';  
        //$url = "http://player-pc.le.com/mms/out/video/playJson?id={$vid}&format=1&tkey={$tkey}&domain=www.le.com&dvtype=1000&region=cn&accessyx=1&platid=3&splatid=304&source=1000&tss=no";  
        $url = "http://player-pc.le.com/mms/out/video/playJson?id={$vid}&platid=1&splatid={$splatid}&format=1&tkey={$tkey}&domain={$domain}&dvtype=720p&devid=70A6E0A1FB93DA437B79DA594B3C9D03B428043B&region=cn&source={$source}&accessyx=1&tss={$tss}";//&tss=tvts  
  
        $data = self::curl($url);  
        //print_r($data);exit;  
        $content = json_decode($data,true);  
        $playurl = $content["msgs"]["playurl"];  
        $point = $content["msgs"]["point"];  
        $hot = $point["hot"];  
        $seek = $point["skip"][0];  
  
        $pic = $playurl["picAll"]["640*320"];  
        $domains = array("http://play.g3proxy.lecloud.com","http://bplay.g3proxy.lecloud.com","http://g3.letv.com");  
        $domain = $domains[0];//$domains[mt_rand(0,count($domains)-1)];  
        $dispatch = $playurl["dispatch"];  
        $duration = $playurl["duration"];  
        foreach ($dispatch as $key => $value) {  
            switch ($key) {  
                case '350':$def = "流畅";break;  
                case '1000':$def = "超清";break;  
                case '1300':$def = "原画";break;  
                case '720p':$def = "720P";break;  
                case '1080p':$def = "1080P";break;  
            }  
            $vurl = $domain.$value[0];//self::getVideoUrl($domain.$value[0]."&format=1&expect=3&sign=letv"); //&format=1为json &format=1为xml  preg_replace("#/vod/v2/#","/  
  
            if (GlobalBase::is_ipad()) {  
                if($key =="1000"){ 
                    $videoinfo['code'] = 200; 
                    //$videoinfo['type'] = 'le'; 
                    //$videoinfo['play'] = 'h5mp4'; 
                    $videoinfo["data"]["url"] = $vurl; 
                    return $videoinfo;exit; 
                } 
            }else{ 
                if ($key == '1300' && $vurl != '') { 
                    $fdata[0]['url'] = $vurl; 
                    $fdata[0]['def'] = '原画'; 
                }  
                if ($key == '1080p' && $vurl != '') { 
                    $fdata[1]['url'] = $vurl; 
                    $fdata[1]['def'] = '1080P'; 
                } 
                if ($key == '1000' && $vurl != '') { 
                    $fdata[2]['url'] = $vurl; 
                    $fdata[2]['def'] = '超清'; 
                } 
                if ($key == '720p' && $vurl != '') { 
                    $fdata[3]['url'] = $vurl; 
                    $fdata[3]['def'] = '720P'; 
                } 
                if ($key == '350' && $vurl != '') { 
                    $fdata[4]['url'] = $vurl; 
                    $fdata[4]['def'] = '流畅'; 
                } 
            } 
        } 
        for ($i=0; $i <= 10 ; $i++) {  
            if ($fdata[$i] == '') {continue;} 
            $key_arrays[]=$fdata[$i]; 
        } 
        $videoinfo['code'] = 200; 
        $videoinfo['poster'] = $img; 
        $videoinfo['name'] = $video_info["coreVideoInfo"]["videoName"]; 
        $videoinfo['play'] = ''; 
        $videoinfo["data"]["video"]["file"] = $key_arrays[0]['url']; 
        $videoinfo["data"]["video"]["type"] = "video/m3u8"; 
        return $videoinfo; 
    } 
    public static function curl($url,$cookie="") 
    { 
        $params["ua"] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36"; 
        $params["cookie"] = $cookie; 
        return GlobalBase::curl($url,$params); 
    } 
    //==================================================以下代码不需要修改========================================================== 
    /** 
     * [getVideoUrl 获取视频最终地址] 
     * @param  [type] $url [description] 
     * @return [type]      [description] 
     */ 
    private static function getVideoUrl($url){ 
        $data = GlobalBase::curl($url); 
        $content = json_decode($data,true); 
        $location = $content["location"]; 
        $nodelist = $content["nodelist"]; 
        $vurl = $nodelist[mt_rand(0,count($nodelist)-1)]["location"]; 
        return $location; 
    } 
    //========================================= 方式二 ================================================= 
    /** 
     * [getMmsKey 获取tkey] 
     * @param  [type] $e [时间] 
     * @return [type]    [description] 
     */ 
    private static function getMmsKey($e) 
    { 
        $t = 185025305; 
        $r = 8; 
        $n = $e; 
        $n = self::rotateRight($n, $r); 
        $o = self::s2v("O",$n, $t); 
        return $o; 
    } 
    private static function rotateRight($e, $t) 
    { 
        for ($r, $n = 0; self::s2v("g",$t, $n); $n++){ 
            $r = self::s2v("o",1,$e);  
            $e >>= 1;  
            $r <<= 31;  
            $e += $r;  
        }  
        return $e;  
    }  
    private static function s2v($k,$y,$r){  
        switch ($k) {  
            case 'D':return $y | $r;break;  
            case 'd':return $y % $r;break;  
            case 'O':return $y ^ $r;break;  
            case 'k':return $y < $r;break;  
            case 'J':return $y >> $r;break;  
            case 'R':return $y === $r;break;  
            case 'g':return $y > $r;break;  
            case 'o':return $y & $r;break;  
            case 'l':return $y !== $r;break;  
            case 'L':return $y != $r;break;  
            case 'a':return $y - $r;break;  
            case 'u':return $y == $r;break;  
            case 'e':return $y << $r;break;  
        }  
    }  
}  