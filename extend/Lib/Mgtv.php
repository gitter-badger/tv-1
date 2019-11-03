<?php
/*
 * $ids = Mgtv::parse($url);  
 * $data = Mgtv::get_PC_B_video($ids);  
 * echo json_encode($data);      
 */
namespace ZhiBo;
class Mgtv  
{  
    //============================================================================================================================  
    /** 
     * [parse 解析获取视频 ID 和视频专辑 ID] 
     * @param  [type] $url [播放网址] 
     * @return [type]      [description] 
     */  
    public static function parse($url)  
    {  
        $name = "MGTV-".md5($url);  
  
        if (file_exists(NAME_PATH.$name) && time() - filemtime(NAME_PATH.$name) < 604800) { //文件存在并且文件创建时间小于7天  
            $data = json_decode(file_get_contents(NAME_PATH.$name),true);  
            $cid = $data['cid'];  
            $vid = $data['vid'];  
            $isIntact = $data['isIntact'];  
        }else{  
            $html = self::curl($url);  
            preg_match('#cid: ([\d]+),#iU',$html,$cids);  
            preg_match('#vid: ([\d]+),#iU',$html,$vids);  
            preg_match('#isIntact: ([\d]+),#iU',$html,$isIntacts);  
            file_put_contents(NAME_PATH.$name, json_encode(array("cid"=>$cids[1],"vid"=>$vids[1],"isIntact"=>$isIntacts[1])));  
            $cid = $cids[1];  
            $vid = $vids[1];  
            $isIntact = $isIntacts[1];  
  
        }  
  
        return array("cid"=>$cid,"vid"=>$vid,"url"=>$url,"isIntact"=>$isIntact);  
    }  
  
    /** 
     * [get_video_files 获取视频信息] 
     * @param  [type]  $vid [视频ID] 
     * @param  integer $def [视频清晰度] 
     * @return [type]       [description] 
     */  
    public static function get_PC_B_video($ids) //芒果浏览器pc端  
    {  
        $ref = $ids['url'];  
        $ids['re'] = $ids['url'];  
        $ids['ua'] = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36';  
        $vid = $ids['vid'];  
        $pno = "1000";  
        $wpno = "2010";  
        $ver =  "0.3.0001";  
        $suuid = self::createuuid(8) . "-" . self::createuuid(4) . "-" . self::createuuid(4) . "-" . self::createuuid(12);  
        $collection_id = "315515";  
        $_support = "10000000";  
        $files = [];  
  
        $did = self::createuuid(8) . "-" . self::createuuid(4) . "-" . self::createuuid(4) . "-" . self::createuuid('12');  
        $clit = time();  
        $api = "https://pcweb.api.mgtv.com/player/video?video_id={$vid}&suuid={$suuid}&cid={$ids['cid']}&tk2=".self::getTK2(array("did"  => "{$did}","ver"  => "{$ver}","pno"  => "{$pno}","clit" => time()))."&_support={$_support}";  
        if (isset($_COOKIE['PM_CHKID'])) {  
            $ids['cookie'] = COOKIE_MGTV."PM_CHKID=".$_COOKIE['PM_CHKID'].";";  
        }   
        $content = self::curl($api,$ids); /* COOKIE_MGTV */  
        //print_r($content);exit;  
        $json = json_decode($content,true);  
        if ($json['code'] == 200 && isset($json['data']['info']) && $json['data']['info']['paymark'] == '0') {  
            if (!isset($_COOKIE['PM_CHKID']) && is_file(COOKIE_PATH.'mgtv.txt')) {  
                $cookie = file_get_contents(COOKIE_PATH.'mgtv.txt');  
                $cc = explode("PM_CHKID",$cookie);  
                $c1 = "PM_CHKID=".trim($cc['1']).";";  
                setcookie("PM_CHKID",trim($cc['1']));  
                $ids['cookie'] = COOKIE_MGTV.$c1;  
            }  
            $pm2 = $json['data']['atc']['pm2'];  
            $g = "https://pstream.api.mgtv.com/player/getSource?tk2=" .self::getTK2(array("did"  => "{$did}","ver"  => "{$ver}","pno"  => "{$pno}","clit" => time())) . "&pm2=" . $json['data']['atc']['pm2'] . "&video_id=" . $vid . "&_support={$_support}&did={$did}&suuid={suuid}&collection_id={$collection_id}"/* &type=pch5 */;  
  
            $g1 = self::curl($g,$ids);  
  
            $k1 = json_decode($g1,true);  
  
            if (isset($k1['data'])) {  
                $data = $k1['data'];  
                $info = $data['info'];//视频信息  
                $points = $json['data']['points'];  
                $preview = $json['data']['frame'];  
  
                $domain = $data['stream_domain'];//视频域名数组  
                $stream = $data['stream'];  
                $count = 0;  
                if ($points['content'] != null || $points['content'] != '') {  
                    $_a = explode('|',$points['start']);  
                    $_b = explode('|',$points['end']);  
                    foreach ($points['content'] as $k => $v) {  
                        $_a = explode('|',$v);  
                        $_tmp['words'] .= self::filterGBK_SpecialChars($_a[1]).',';  
                        $_tmp['time'] .= (int)$_a[0].',';  
                    }  
                    $_tmp['words'] = substr($_tmp['words'],0,strlen($_tmp['words'])-1);  
                    $_tmp['time'] = substr($_tmp['time'],0,strlen($_tmp['time'])-1);  
                    //$videoinfo["prompt"] = $_tmp;  
                }  
  
                $videoinfo["data"]["poster"] = $json['data']['info']['thumb'];  
  
                foreach ($stream as $key => $value) {  
                    $def = $value['def'];  
                    if ($def==1) {  
                        continue;  
                    }  
                    if ($domain[$key] == null) {  
                        $domain[$key] = $domain[0];  
                    }  
                    //print_r($domain[$key].$value['url']);exit;  
                    if(!emptyempty($value['url'])){  
                        $vurl = self::get_video_url($domain[$key].$value['url'],$ids);  
                        if ($vurl == 'failed') {  
                            $vurl = self::get_video_url($domain[$key].$value['url'],$ids);  
                            if ($vurl == 'failed') {  
                                $vurl = self::get_video_url($domain[$key].$value['url'],$ids);  
                                if ($vurl == 'failed') {  
                                    $vurl = self::get_video_url($domain[$key].$value['url'],$ids);  
                                    if ($vurl == 'failed') {  
                                        break;    
                                    }  
                                }  
                            }  
  
                        }  
                        if (GlobalBase::is_ipad()) {  
                            if($def==3 || $def == 2){  
                                $true_url = $vurl;  
                                $videoinfo['code'] = 200;  
                                $videoinfo['play'] = 'hls';  
                                $videoinfo['type'] = 'mgtv';  
                                $videoinfo["data"]["url"] = str_replace("http://","https://",$true_url);  
                                exit(json_encode($videoinfo));  
                            } else {  
                                $videoinfo['play'] = 'url';  
                                $videoinfo["url"] = '../yun/?url='.urlencode($ref);  
                                exit(json_encode($videoinfo));  
                            }  
                        }else{  
                            $video[0] = $vurl;  
                            $video[1] = "video/m3u8";  
                            $video[2] = $value['name'];  
                            $video[3] = $def == 3 ? 10: 0;  
                            $videoinfo1["video"][$count] = str_replace('http://','//',$video);;  
                            $count++;  
                        }  
                    }  
                }  
                //print_r(json_encode($videoinfo1));exit;  
                if (!emptyempty($videoinfo1["video"]) && $videoinfo1["video"][count($videoinfo1["video"])-1][0] != 'busy') {  
                    $videoinfo['code'] = 200;  
                    $videoinfo['msg'] = '解析成功';  
                    $mp4url = !emptyempty($videoinfo1["video"][count($videoinfo1["video"])-1][0]) ? $videoinfo1["video"][count($videoinfo1["video"])-1][0] : $videoinfo1["video"][0][0];  
                    $videoinfo['data']['url'] = $mp4url;  
                    if (emptyempty($mp4url)) {  
                        $videoinfo['code'] = 404;  
                        $videoinfo['msg'] = '获取视频地址失败';  
                    }  
                    $videoinfo['play'] = 'hls';  
  
                    $imp4url = $videoinfo1["video"][0][0];  
                } else {  
                    $videoinfo['code'] = 302;  
                    $videoinfo['msg'] = $json['data']['info']['title'].'视频正在加载···请稍后···';  
                    $videoinfo["play"] = 'url';  
                    $videoinfo["url"] = '../yun/?url='.urlencode($ref);  
                }  
  
                $videoinfo["data"]["name"] = $json['data']['info']['title'];  
                $videoinfo["data"]["series"] = $json['data']['info']['series'];  
                $videoinfo["data"]["desc"] = $json['data']['info']['desc'];  
  
                return $videoinfo;  
            }  
        } else {  
            if (isset($json['data']['info']) && $json['data']['info']['paymark'] == '1') {  
                $_loc1['code'] = 302;  
                $_loc1['msg'] = '芒果VIP视频';  
                $_loc1['url'] = isset($json['url']) ? $json['url'] : '../yun/?url='.urlencode($ref);  
            } else if (isset($json['code']) && $json['code'] == 40001) {  
                $_loc1['code'] = 302;  
                $_loc1['msg'] = isset($json['msg']) ? $json['msg'] : '解析失败！';  
                $_loc1['url'] = isset($json['url']) ? $json['url'] : '../yun/?url='.urlencode($ref);  
            } else {  
                $_loc1 = array(  
                    "code" => 302,  
                    "msg" => '解析失败!',  
                    "url" => '../yun/?url='.urlencode($ref)  
                );  
            }  
            return $_loc1;  
        }  
    }  
  
    /* 
      *  
     */  
    public static function get_Phone_C_video($ids) //芒果浏览器pc端  
    {  
        $ref = $ids['url'];  
        $ids['re'] = $ids['url'];  
        $ids['ua'] = 'okhttp/imgotv';  
        $ids['cookie'] = COOKIE_MGTV;  
        $vid = $ids['vid'];  
        $ver =  "0.2.24011";  
        $suuid = self::createuuid(8) . "-" . self::createuuid(4) . "-" . self::createuuid(4) . "-" . self::createuuid(12);  
        $_support = "10100001";  
  
        $api = "https://mobile.api.mgtv.com/v8/video/getSource?_support=10100001&device=oppo%20R11&osVersion=4.4.2&appVersion=5.8.6_1&ticket=&userId=0&mac=i352419010176358&osType=android&channel=360dev&uuid=&endType=mgtvapp&androidid=b0359fa2c8301858&imei=352419010176358&macaddress=B2%3A35%3A9F%3AA2%3AC8%3A30&seqId=3cdb503a7f75ab0d54139bf4a92bf380&version=5.2&type=10&abroad=0&src=mgtv&uid=&phonetype=oppo%20R11&videoId={$vid}&isowner=0&clipId=323323&playType=1&dataType=1&keepPlay=0&source=40&localPlayVideoId=4458399&localVideoWatchTime=121&did=i352419010176358&suuid={$suuid}&hdts=h264%2Ch265";  
  
        $content = self::curl($api,$ids); /* COOKIE_MGTV */  
  
        $json = json_decode($content,true);  
        $_tmp = array();  
  
        if ($json['code'] == 200) {  
            $domains = $json['data']['videoDomains'];  
            $data = $json['data']['videoSources'];  
            foreach ($data as $key => $value) {  
                $def = $value['definition'];  
                if ($value['url'] == '') {  
                    continue;  
                }  
  
                $url = $domains[1].$value['url']."&ver={$ver}&chk=074a2db93003e523b945509df080ac00&_support={$_support}&did=i352419010176358&suuid={$suuid}";  
  
                //print_r($url);exit;  
                $url = self::get_video_url($url,$ids);  
  
                if (GlobalBase::is_ipad()) {  
                    if($def==3 || $def == 2 && $url != ''){  
                        $true_url = self::get_video_mp4_url($url);  
                        $videoinfo["video"]["file"] = $true_url;  
                        $videoinfo["video"]["type"] = "video/mp4";  
                        exit(json_encode($videoinfo));  
                    } else {  
                        $videoinfo['play'] = 'url';  
                        $videoinfo["url"] = '../yun/?url='.urlencode($ids['url']);  
                        exit(json_encode($videoinfo));  
                    }  
                } else {  
                    if ($def == 4 && $url!='') {  
                        $_tmp[4]['def'] = '蓝光';  
                        $_tmp[4]['url'] = $url;  
                    }  
                    if ($def == 3 && $url!='') {  
  
                        $_tmp[3]['def'] = '超清';  
                        $_tmp[3]['url'] = $url;  
                    }  
                    if ($def == 2 && $url!='') {  
                        $_tmp[2]['def'] = '高清';  
                        $_tmp[2]['url'] = $url;  
                    }  
                    if ($def == 1 && $url!='') {  
                        $_tmp[1]['def'] = '标清';  
                        $_tmp[1]['url'] = self::get_video_mp4_url($url);  
  
                    }  
  
                }  
  
            }  
            //print(json_encode($_tmp[3]));exit;  
            for ($i=1; $i <= 5 ; $i++) {   
                if ($_tmp[$i] == '') {  
                    continue;  
                }  
                $key_arrays[]=$_tmp[$i];  
            }  
            $videoinfo["video"]["file"] = $key_arrays[count($key_arrays)-1]['url'];  
            $videoinfo["video"]["type"] = "video/mp4";  
            $videoinfo['msg'] = '网站资源';  
  
        }  
        return $videoinfo;  
  
    }  
  
    /* 
    public getMgtv($ids) 
    { 
        $api = "https://mobile.api.hunantv.com/v6/video/getSource?_support=10100001&device=N5207&src=mgtv&appVersion=5.7.0_1&osVersion=4.2.2&osType=android&version=5.2&type=10&ticket=&userId=0&channel=mgtv3&videoid={$ids['vid']}"; 
        $data = self::curl($api,$ids); 
        print_r($data); 
    } */  
    /** 
     * [get_video_url 获取视频最终地址] 
     * @param  [type] $url [视频请求链接] 
     * @return [type]      [description] 
     */  
    public static function get_video_url($url,$ids){  
  
        $content = self::curl($url,$ids);  
        //print_r($content);exit;  
        $data = json_decode($content,true);  
        $vurl = $data["info"];  
        return $vurl;  
    }  
  
    /** 
     * [get_video_mp4_url 获取视频MP4视频] 
     * @param [type] $url [视频请求连接] 
     * @return [type] url [mp4最终地址]   
     */  
    public static function get_video_mp4_url($url){  
        $u1 = explode('?',$url);  
        $u1 = explode('//',$u1['0']);  
        $u1 = explode('/',$u1['1']);  
        $f1 = explode('_',$u1['5']);  
        $fid = $f1['0'];  
        $file2 = "/".$u1[1]."/".$u1[2]."/".$u1[3]."/".$u1[4]."/".$u1[5];  
        if(strstr($file2,'_mp4')){  
            $file= str_replace('_mp4','.mp4',$file2);  
        }else{  
            $file=$file;  
        }  
        $true_url = "https://disp.titan.mgtv.com/vod.do?fmt=4&pno=1042000&fid=$fid&file=$file";  
        return $true_url;  
    }  
  
    public static function curl($url,$ids)  
    {  
        $params["ua"] = !emptyempty($ids['ua']) ? $ids['ua'] : "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";  
        $params["cookie"] = $ids['cookie'];  
        //$params["proxy"] = PROXY;  
        $params["ip"] = "14.21.96.129";//伪装为固定IP，否则会报异地登录，会封号，不要更改  
        return GlobalBase::mgtv_curl($url,$params);  
    }  
  
    /* 
     * 生成0到1随机数 
     */  
    public static function random($min = 0, $max = 1){       
        return $min + mt_rand()/mt_getrandmax()*($max-$min);   
    }  
  
    /* 
    * did 生成 
    *  
     */  
    public static function createuuid($b, $a=null) {  
        switch ($a) {  
            case 'h':  
                $a = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";  
                break;  
            case 's':  
                $a = "0123456789";  
                break;  
            case 'd':  
                $a = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";  
                break;  
            case 'x':  
                $a = "abcdefghijklmnopqrstuvwxyz";  
                break;  
            default:  
                $a = "0123456789abcdef";  
                break;  
        }  
        $z = str_split($a);  
        for ($c='',$g=0; $g < $b; $g++) {   
            $c .= $z[ceil(100000000 * (self::random())) % count($z)];  
        }  
        return $c;  
    }  
  
    public static function filterGBK_SpecialChars($str)  
    {  
        $str = str_replace('“','',$str);  
        $str = str_replace('”','',$str);  
        return $str;  
    }  
  
    public static function charAt($str, $index = 0){  
        return substr($str, $index, 1);  
    }  
  
    public static function getTK2($param1){  
        $_loc1_ = 0;  
        $_loc2_ = 0;  
        foreach( $param1 as $_loc3_ => $_loc4_ ){  
            $_loc5_[] = $_loc3_ . "=" . $_loc4_;  
        }  
        $_loc6_ = join("|",$_loc5_);  
        $_loc7_ = base64_encode($_loc6_);  
        $_loc8_ = str_replace(array("+","/","="),array("_","~","-"),$_loc7_);  
        $_loc11 = "";  
        foreach( str_split($_loc8_) as $_loc9_ => $_loc10_ ){  
            $_loc11 .= self::charAt($_loc8_,strlen($_loc8_)-$_loc9_-1);  
        }  
        return $_loc11;  
    }  
}  