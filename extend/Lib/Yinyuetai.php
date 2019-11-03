<?php
/*
 * echo json_encode(Yinyuetai::parse($url));    
 */
namespace ZhiBo;
class Yinyuetai  
{  
  
    /** 
     * [parse 解析网页获取视频ID] 
     * @param  [type] $url  [description] 
     * @return [type]       [description] 
     */  
    public static function parse($url)  
    {  
        $html = self::curl($url);  
  
        $vids = $aids = $titles = array();  
        if ($html){  
            preg_match('#id : "([\d]+)"#iU',$html,$vids);  
            $vid = $vids[1];  
            $data = self::get_video_info($vid);  
            return $data;  
        }  
    }  
    public static function get_video_urls($vid)  
    {  
        $api_video = "http://www.yinyuetai.com/api/info/get-video-urls?videoId={$vid}";  
        $content = GlobalBase::curl($api_video);  
        print_r($api_video);exit;  
        $data = json_decode($content,true);  
        $hd = $data["hdVideoUrl"];//高清  
        $hc = $data["hcVideoUrl"];//流畅  
        $he = $data["heVideoUrl"];//超清  
        if (GlobalBase::is_ipad()) {  
            $videoinfo["video"]["file"] = $hd;  
            $videoinfo["video"]["type"] = "video/mp4";  
        }else{  
            $videoinfo["video"][0] = array($hd,"video/mp4","高清",10);  
            $videoinfo["video"][1] = array($he,"video/mp4","超清",0);  
            $videoinfo["video"][2] = array($hc,"video/mp4","流畅",0);  
        }  
        return $videoinfo;  
    }  
    public static function get_mv_info($vid)  
    {  
        $api_video = "http://ext.yinyuetai.com/main/get-h-mv-info?json=true&videoId={$vid}";  
        $content = self::curl($api_video);  
        print_r($content);exit;  
        $data = json_decode($content,true);  
        $video_info = $data["videoInfo"];  
        $coreVideoInfo = $video_info["coreVideoInfo"];  
        $img = $coreVideoInfo["bigHeadImage"];  
        $videoUrlModels = $coreVideoInfo["videoUrlModels"];  
        foreach ($videoUrlModels as $key => $value) {  
            $def = $value["qualityLevel"];  
            $defname = $value["qualityLevelName"];  
            $vurl = $value["videoUrl"];  
            if (GlobalBase::is_ipad()) {  
                if($def=="hd"){  
                    $videoinfo['code'] = 200;  
                    $videoinfo["data"]["url"] = $vurl;  
                    break;  
                }  
            }else{  
                if ($stream_type == 'mp5hd4' && $m3u8 != '') {  
                    $fdata[0]['url'] = $m3u8;  
                    $fdata[0]['def'] = '4k';  
                }   
                if ($stream_type == 'mp5hd3' && $m3u8 != '') {  
                    $fdata[1]['url'] = $m3u8;  
                    $fdata[1]['def'] = 'mp5原画';  
                }  
                $video[0] = $vurl;  
                $video[1] = "video/mp4";  
                $video[2] = $defname;  
                $video[3] =  $def =="hd"? 10 : 0;  
                $videoinfo["video"][$key] = $video;  
            }  
        }  
        return $videoinfo;  
    }  
    public static function get_video_info($vid)  
    {  
        $api_video = "http://www.yinyuetai.com/insite/get-video-info?json=true&videoId={$vid}";  
        $content = self::curl($api_video);  
        //print_r($api_video);exit;  
        $data = json_decode($content,true);  
        $video_info = $data["videoInfo"];  
        $img = $video_info["bigHeadImage"];  
        $coreVideoInfo = $video_info["coreVideoInfo"];  
        $videoUrlModels = $coreVideoInfo["videoUrlModels"];  
        foreach ($videoUrlModels as $key => $value) {  
            $def = $value["qualityLevel"];  
            $defname = $value["qualityLevelName"];  
            $vurl = $value["videoUrl"];  
            if (GlobalBase::is_ipad()) {  
                if($def=="hd"){  
                    $videoinfo['code'] = 200;  
                    $videoinfo['poster'] = $img;  
                    $videoinfo['name'] = $video_info["coreVideoInfo"]["videoName"];  
                    $videoinfo['play'] = 'h5mp4';  
                    $videoinfo["data"]["url"] = $vurl;  
                    return $videoinfo;  
                    exit;  
                }  
            }else{  
                if ($def == 'sh' && $vurl != '') {  
                    $fdata[0]['url'] = $vurl;  
                    $fdata[0]['def'] = '蓝光';  
                }   
                if ($def == 'he' && $vurl != '') {  
                    $fdata[1]['url'] = $vurl;  
                    $fdata[1]['def'] = '超清';  
                }  
                if ($def == 'hd' && $vurl != '') {  
                    $fdata[2]['url'] = $vurl;  
                    $fdata[2]['def'] = '高清';  
                }   
                if ($def == 'hc' && $vurl != '') {  
                    $fdata[3]['url'] = $vurl;  
                    $fdata[3]['def'] = '流畅';  
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
        $videoinfo['play'] = 'h5mp4';  
        $videoinfo["data"]["url"] = $key_arrays[0]['url'];  
        return $videoinfo;  
    }  
    public static function curl($url)  
    {  
        $params["ua"] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";  
        return GlobalBase::curl($url,$params);  
    }    
}