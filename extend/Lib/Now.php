<?php
/*   
 * echo json_encode(Now::parse($url));          
 */
namespace ZhiBo;
class Now
{
    public static function parse($url)
    {
        parse_str(parse_url($url)["query"]);
        if($roomid){
            return self::get_room_info_v2($roomid);
        }
    }
    public static function get_room_info_v2($room_id)
    {
        $api = "https://now.qq.com/cgi-bin/now/web/room/get_room_info_v2?room_id=$room_id";
        $content = self::curl($api);
        $data = json_decode($content,true)["result"];
        $is_on_live = $data["is_on_live"];
        if($is_on_live){//是否直播
            return self::get_live_room_url($room_id);
        }else{
            if(!emptyempty($data["vid"])){
                $vid = $data["vid"];
                return self::get_record_room_info($vid);
            }else{
                return GlobalBase::get_unknown_video();
            }
        }
    }
    public static function get_record_room_info($vid)
    {
        $api = "https://now.qq.com/cgi-bin/now/web/room/get_record_room_info?vid=$vid";
        $content = self::curl($api);
        $data = json_decode($content,true)["result"];
        $record_video_url = $data["record_video_url"];
        $video_cover_url = $data["video_cover_url"];
        $videoinfo["poster"] = $video_cover_url;
        $videoinfo["video"]["file"] = $record_video_url;
        $videoinfo["video"]["type"] = "video/m3u8";
        return $videoinfo;
    }
    public static function get_live_room_url($room_id)
    {
        $api = "https://now.qq.com/cgi-bin/now/web/room/get_live_room_url?platform=4&room_id=$room_id";
        $content = self::curl($api);
        $data = json_decode($content,true)["result"];
        $videoURLList = $data["videoURLList"];
        foreach ($videoURLList as $key => $value) {
            if (GlobalBase::is_ipad()) {
                $videoinfo["url"]= $value;
                break;
            }else{
                switch ($key) {
                    case 0:$def = "自动";break;
                    case 1:$def = "高清";break;
                    case 2:$def = "标清";break;
                    case 3:$def = "流畅";break;
                }
                $videoinfo["live"] = true;
                $video[0] = $value;
                $video[1] = "video/m3u8";
                $video[2] = $def;
                $video[3] =  $key != 0 ? 0 : 10;
                $videoinfo["video"][$key] = $video;
            }
        }
        return $videoinfo;
    }
    public static function curl($url)
    {
        $params["ua"] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
        return GlobalBase::curl($url,$params);
    }
}