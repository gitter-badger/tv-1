<?php
/*   
 * echo json_encode(LongZhu::parse($url));          
 */
namespace ZhiBo;
class LongZhu  
{    
    public static function parse($url)  
    {  
        $vid = explode("?", basename($url))[0];  
        $data = self::get_video_url($vid);  
        if(!emptyempty($data)){  
            return $data;  
        }else{  
            $content = self::curl($url);  
            preg_match('#"RoomId":(.*?),#',$content,$_rid);  
            if(!emptyempty($_rid[1])){  
                $rid = $_rid[1];  
            }else{  
                preg_match("#ROOMID\s*=\s*'(.*?)';#",$content,$__rid);  
                $rid = $__rid[1];  
            }  
            return self::get_live_url($rid);  
        }  
    }  
    public static function get_video_url($mid)  
    {  
        $api = "http://api.v.plu.cn/CloudMedia/GetInfoForPlayer?mediaId={$mid}";  
        $content = self::curl($api);  
        $data = json_decode($content,true);  
        if(!emptyempty($data["urls"])){  
            $urls = $data["urls"];  
            foreach ($urls as $key => $value) {  
                $level = $value["RateLevel"];  
                $ext = $value["Ext"];  
                $vurl = $value["SecurityUrl"];  
                switch ($level) {  
                    case 1:$def="标清";break;  
                    case 2:$def="高清";break;  
                    case 3:$def="超清";break;  
                    case 4:$def="原画";break;  
                    default:$def="自动";break;  
                }  
                switch ($ext) {  
                    case 'flv':$type = "flv";break;  
                    case 'mp4':$type = "mp4";break;  
                    case 'm3u8':$type = "m3u8";break;  
                }  
                if (GlobalBase::is_ipad()) {  
                    if($ext=='m3u8'){  
                        $videoinfo['code'] = 200;  
                        $videoinfo["data"]["video"]["file"] = $vurl;  
                        $videoinfo["data"]["video"]["type"] = "video/m3u8";  
                        break;  
                    }  
                }else{  
                    $video[0] = $vurl;  
                    $video[1] = $type;  
                    $video[2] = $type.$def;  
                    $video[3] =  $level != 2 ? 0 : 10;  
                    $videoinfo["code"] = 200;  
                    $videoinfo["data"]["video"][$key] = $video;  
                    $videoinfo["data"]["flashplayer"] = true;  
                }  
            }  
            return $videoinfo;  
        }else{  
            return "";  
        }  
    }  
    public static function get_live_url($rid)  
    {  
        $api = "http://liveapi.plu.cn/liveapp/roomstatus?roomId={$rid}";  
        $content = self::curl($api);  
        $data = json_decode($content,true);  
        $img = $data["cover"];  
        $vurl = $data["streamUri"];  
        $videoinfo["poster"] = $img;      
        if (!GlobalBase::is_ipad()) {  
            $videoinfo["data"]["live"] = true;  
            $videoinfo["data"]["flashplayer"] = true;  
        }  
        $videoinfo['code'] = 200;  
        $videoinfo["data"]["video"]["file"] = $vurl;  
        $videoinfo["data"]["video"]["type"] = "video/m3u8";  
        return $videoinfo;  
    }  
    public static function curl($url)  
    {  
        $params["ua"] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";  
        return GlobalBase::curl($url,$params);  
    }  
}  