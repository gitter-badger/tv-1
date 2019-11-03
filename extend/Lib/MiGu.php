<?php
/*
 * http://www.miguvideo.com/playurl/v1/play/playurlh5?contId=637544550&rateType=1,2,3,4
 * http://www.miguvideo.com/wap/resource/pc/detail/miguplay.jsp?cid=636120900
 * echo file_get_contents("http://www.miguvideo.com/wap/resource/pc/data/detailData.jsp?cid=636120900");
 * $ids = MIGU::parse($url);
 * echo str_replace("\/", "/", json_encode(MIGU::get_vip_video_url($ids)));
 */
namespace ZhiBo;
class MiGu
{    
    public static function parse($url)
    {
        $params = parse_url($url)["query"];
        $cid = str_replace("cid=","",$params);
        $name = "MIGU-".md5($cid);
        if (file_exists(NAME_PATH.$name)) {
            $data = json_decode(file_get_contents(NAME_PATH.$name),true);
            $ids = $data;
        }else{
            $api = "http://www.miguvideo.com/wap/resource/pc/data/miguData.jsp";
            $content = self::curl($api,'','http://www.miguvideo.com/wap/r…il/miguplay.jsp?{$params}',$cid);
            $content1 = self::curl($url,'',$url);
            if ($content1) {
                preg_match('#id="sessionID"\s*value="(.*?)">#',$content1,$_a);
            }
            if (isset($_a[1])) {
                $ids['sessionID'] = $_a[1];
            }            
            $json = json_decode($content,true);
            $data = $json[0];
            $ids['vid'] = $data['playId'];
            $ids['title'] = $data['name'];
            $ids['poster'] = $data['imgH'];
            $ids['url'] = $url;
            file_put_contents(NAME_PATH.$name, json_encode($ids));
        }
    }
    public static function get_vip_video_url($ids){
        $api = "http://www.miguvideo.com/playurl/v1/play/playurlh5?contId={$ids['vid']}&rateType=1,2,3&clientId={$ids['sessionID']}";
        $data = self::curl($api,'',$ids['url']);       
        $d = json_decode($data,true);
        if ($d['code'] == '200') {
            $data = $d['body'];
            $urls = $data['urlInfos'];
            $types = $data['mediaFiles'];
            $count=0;
            foreach ($urls as $key => $value) {
                switch ($types[$key]['rateType']) {
                    case '1':$def = "标清";$vurl = $value['url'];break;
                    case '2':$def = "高清";$vurl = $value['url'];break;
                    case '3':$def = "超清";$vurl = $value['url'];break;
                }
                if ($value['url'] == '') {
                    continue;
                }
                if (GlobalBase::is_ipad()) {
                    if ($types[$key]['rateType']=='1') {
                        $videoinfo['code'] = 200;
                        $videoinfo["data"]["url"] = $vurl;
                        $videoinfo['data']['poster'] = $ids['poster'];
                    }
                } else {
                    $video_m3u8[0] = str_replace('gslbmgspvod.miguvideo.com','vod.hcs.cmvideo.cn:8088',$vurl);
                    $video_m3u8[1] = "video/m3u8";
                    $video_m3u8[2] = $def;
                    $video_m3u8[3] = $key=="1" ? 10 : 0;
                    $videoinfo['code'] = 200;
                    $videoinfo['data']["video"][$count] = $video_m3u8;
                    $videoinfo['data']['poster'] = $ids['poster'];
                    $count++;
                }
            }
            return $videoinfo;
        }    
    }
    public static function format_video($url,$key){
	    $ip = GlobalBase::get_ip();
	    $name = 'MIGUVIDEO-'.md5($key.$ip);
	    $url_1 = explode('media',$url);
		$url_2 = str_replace('gslbmgspvod.miguvideo.com','vod.hcs.cmvideo.cn:8088',$url_1[0]);
		$domain = $url_2.'media/';
	    $_data = file_get_contents($url);
	    $_data = preg_split('/[\r\n]+/s', $_data);
		$d_u = array();
		$urls = array();
		$bool = true;
		$targetduration = "";
		foreach ($_data as $value) {
			if(strstr($value,"#EXT-X-TARGETDURATION:")){//多码率
				$targetduration = $value;
			}else if(strstr($value,'#EXTINF:')){//单码率
				$d_u[count($d_u)] = $value;
			}else if($value&&substr($value,0,1)!="#"){
				$urls[count($urls)] = $domain.$value;
			}
		}
		$m3u8 = "#EXTM3U\n#EXT-X-VERSION:3\n";
		$m3u8 .= empty($targetduration)?"#EXT-X-TARGETDURATION:7200\n" : $targetduration."\n";
		foreach ($d_u as $key => $value) {
			$m3u8 .= $value."\n"."../../video/ts.php?url=".base64_encode($urls[$key])."&site=migu\n";
		}
		$m3u8 .="#EXT-X-ENDLIST";
		file_put_contents(M3U8_PATH.$name.".m3u8", $m3u8);
		$vurl = "./data/m3u8/".$name.".m3u8";
		return $vurl;
    }
    public static function format_video1($url,$key){
        $ip = GlobalBase::get_ip();
        $name = 'MIGUVIDEO-'.md5($key.$ip);
        $url_1 = explode('media',$url);
        $url_2 = str_replace('gslbmgspvod.miguvideo.com','vod.hcs.cmvideo.cn:8088',$url_1[0]);
        $domain = $url_2.'media/';
        $_data = file_get_contents($url);
        $_data = preg_split('/[\r\n]+/s', $_data);
        $d_u = array();
        $urls = array();
        $bool = true;
        $targetduration = "";
        foreach ($_data as $value) {
            if(strstr($value,"#EXT-X-TARGETDURATION:")){//多码率
                $targetduration = $value;
            }else if(strstr($value,'#EXTINF:')){//单码率
                $d_u[count($d_u)] = $value;
            }else if($value&&substr($value,0,1)!="#"){
                $urls[count($urls)] = $domain.$value;
            }
        }
        $m3u8 = "#EXTM3U\n#EXT-X-VERSION:3\n";
        $m3u8 .= empty($targetduration)?"#EXT-X-TARGETDURATION:7200\n" : $targetduration."\n";
        foreach ($d_u as $key => $value) {
            $m3u8 .= $value."\n".$urls[$key]."\n";
        }
        $m3u8 .="#EXT-X-ENDLIST";
        print_r($m3u8);exit;
        file_put_contents(M3U8_PATH.$name.".m3u8", $m3u8);
        $vurl = "./data/m3u8/".$name.".m3u8";
        return $vurl;
    }
    public static function curl($url,$cookie="",$ref,$cid='')
    {
        $params["ua"] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
        if ($cid != '') {
            $params["fields"] = "cid=".$cid;
        }
        $params["ref"] = $ref;
        return GlobalBase::curl($url,$params);
    }	
}