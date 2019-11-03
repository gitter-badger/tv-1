<?php
/*
 * echo str_replace("\/","/",json_encode(BaoFeng::parse($url)));  
 */
namespace ZhiBo;
class BaoFeng  
{  
  
    public static function parse($url)  
    {  
        preg_match('#play/(.*)/play-(\d+)#',$url,$ids);  
        $id = $ids[1];  
        $aid = $ids[2];  
        return self::movie_json($id,$aid);  
    }  
    public static function movie_json($id,$aid)  
    {  
        $api = "http://moviebox.baofeng.net/movie_json/newboxp2p/{$id}/{$aid}.js";  
        $html = self::curl($api,COOKIE_BAOFENG);  
  
        $json = str_replace("var movie_detail=","",$html);  
        $data = json_decode($json,true);  
        $info_pianyuan = $data["info_pianyuan"];  
        foreach ($info_pianyuan as $key => $value) {  
            $aid = $value["aid"];  
            $wid = $value["wid"];  
            $ispay = $value["ispay"];//是否付费  
            $hd_type = $value["hd_type"];  
            switch ($hd_type) {  
                case '480P':$def = "标清";break;  
                case '720P':$def = "720P";break;  
                case '1080P':$def = "1080P";break;  
            }  
            $vurl = self::get_source($wid=13,$aid);  
  
            if (GlobalBase::is_ipad()) {  
                if($hd_type=='1080P'){  
                    $videoinfo["code"] = 200;  
                    $videoinfo["data"]["url"] = $vurl;  
                    return $videoinfo;  
                    exit;  
                }  
            }else{  
                if ($hd_type == '1080P' && $vurl != '') {  
                    $fdata[0]['url'] = $vurl;  
                    $fdata[0]['def'] = '蓝光';  
                }   
                if ($hd_type == '720P' && $vurl != '') {  
                    $fdata[1]['url'] = $vurl;  
                    $fdata[1]['def'] = '超清';  
                }  
                if ($hd_type == '480P' && $vurl != '') {  
                    $fdata[2]['url'] = $vurl;  
                    $fdata[2]['def'] = '高清';  
                }   
            }  
        }  
        for ($i=0; $i <= 3 ; $i++) {   
            if ($fdata[$i] == '') {continue;}  
            $key_arrays[]=$fdata[$i];  
        }  
        $videoinfo['code'] = 200;  
        $videoinfo['poster'] = $img;  
        $videoinfo['name'] = $video_info["coreVideoInfo"]["videoName"];  
        $videoinfo['play'] = 'h5mp4';  
        $videoinfo["data"]["url"] = $key_arrays[0]['url'];  
        return $videoinfo;  
    }  
    public static function get_source($wid=13,$aid){  
        $num = $aid % 500;  
        //$api = "http://minfo.baofeng.net/asp_c/{$wid}/{$num}/{$aid}.json";  
        $api = "http://minfo.baofeng.net/source/{$wid}/{$num}/{$aid}.json";  
        $html = self::curl($api,COOKIE_BAOFENG);  
        $json = str_replace(";","",str_replace("var storm_json = ","",$html));  
        $data = json_decode($json,true);  
        $video_list = $data["video_list"];  
        $iid = $video_list[0]["iid"];  
        $size = $video_list[0]["size"];  
        $vurl = self::get_video_url($iid,$size);  
        return $vurl;  
    }  
     public static function get_video_url($gcid,$size){  
        $time = number_format(microtime(true),3,'','');  
        $api = "http://rd.p2p.baofeng.net/queryvp.php?type=3&gcid={$gcid}&_={$time}&callback=jsonp7";  
        $html = self::curl($api,COOKIE_BAOFENG,PROXY);  
        preg_match("#'ip':'(.*?)'#",$html,$_ip);  
        preg_match("#'port':'(.*?)'#",$html,$_port);  
        preg_match("#'path':'(.*?)'#",$html,$_path);  
        preg_match("#'key':'(.*?)'#",$html,$_key);  
        $ip = self::getip($_ip[1]);  
        $port = $_port[1];  
        $path = $_path[1];  
        $key = $_key[1];  
        $vurl ="http://{$ip}:{$port}/{$path}?key={$key}&filelen={$size}";  
        return $vurl;  
    }  
    public static function curl($url,$cookie="",$proxy="")  
    {  
        //$data = mb_check_encoding($data,'gbk')?iconv('gbk','utf-8//IGNORE',$data):$data;//将字符串的编码从gbk转到UTF-8   
        $params["ua"] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";  
        $params["ip"] = "119.123.69.232";//伪装为固定IP，否则会报异地登录，会封号，不要更改  
        //$params["proxy"] = $proxy;//代理地址  
        $params["cookie"] = $cookie;  
        return GlobalBase::curl($url,$params);  
    }  
    /** 
     * [getip 解密IP地址] 
     * @param  [type] $ip [description] 
     * @return [type]     [description] 
     */  
    public static function getip($ip) {  
        $p2pmap = array(  
            "b"=>"0","a"=>"1","o"=>"2",  
            "f"=>"3","e"=>"4","n"=>"5",  
            "g"=>"6","h"=>"7","t"=>"8",  
            "m"=>"9","l"=>".","c"=>"A",  
            "p"=>"B","z"=>"C","r"=>"D",  
            "y"=>"E","s"=>"F"  
        );  
        $b = explode(",", $ip);  
        for ($j = 0; $j < count($b); $j++) {  
            $g = $b[$j];  
            $f = "";  
            $h = strlen($g);  
            for ($k = 0; $k < $h; $k++){  
                $f .= $p2pmap[substr($g,$k,1)];  
            }  
        }  
        return $f;  
    }  
}