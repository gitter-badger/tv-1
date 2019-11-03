<?php
/*   
 * echo json_encode(LeCloud::parse($url));       
 */
namespace ZhiBo;
class LeCloud  
{  
    public static function parse($url)  
    {  
      $_loc1_ = $url;  
      $_loc3_ = explode('|',$_loc1_);  
      $uu = explode(":",$_loc3_[0])[1];  
      $vu = explode(":",$_loc3_[1])[1];  
      return self::get_video($uu,$vu);  
    }  
    public static function get_video($uuid,$vuid){  
        $sign = md5("cfflashformatjsonran".time()."uu{$uuid}ver2.2vu{$vuid}2f9d6924b33a165a6d8b5d3d42f4f987");  
        $v = json_decode(self::https_curl("api.letvcloud.com/gpc.php?cf=flash&format=json&ran=".time()."&uu={$uuid}&ver=2.2&vu={$vuid}&sign={$sign}"),true);  
        $ltyp = array("low"=>"标清","high"=>"高清","super"=>"超清","yuanhua"=>"原画");  
        foreach( $v['data']['video_info']['media'] as $l=>$row ){  
            $f['mp4'][$ltyp[$l]] = base64_decode($row['play_url']['main_url']);  
            $f['m3u8'][$ltyp[$l]] = str_replace("tss=no","tss=ios",base64_decode($row['play_url']['main_url']));  
        }  
        $data = array(  
            'title' => $v['data']['video_info']['video_name'],  
            'poster' => $v['data']['play_info']['init_pic'],  
            'data' => $f,  
        );  
        $data['url'] = isset($data['data']['mp4']['原画']) ? $data['data']['mp4']['原画'] : isset($data['data']['mp4']['超清']) ? $data['data']['mp4']['超清'] : $data['data']['mp4']['高清'];  
        if( $v['code']==0 ){  
            $printr = array('code' => 200, 'msg' => 'success', 'play' => 'h5mp4','data' => $data);  
        }else{  
            $printr = array('code' => 404, 'msg' => 'ㄟ( ▔, ▔ )ㄏ，参数错误');  
        }  
        return $printr;  
    }  
    public static function https_curl($url){  
        $curl = curl_init();  
        curl_setopt($curl, CURLOPT_URL, $url);  
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);  
        return curl_exec($curl);  
    }  
}  