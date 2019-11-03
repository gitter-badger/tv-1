<?php
/*   
 * echo json_encode(HuYa::parse($url));        
 */
namespace ZhiBo;
class HuYa
{
	public static function parse($url)
	{
		$vid = explode(".html", basename($url))[0];
		return self::get_video_url($vid);
	}
	public static function get_video_url($vid)
	{
		$api = "http://v-api-play.huya.com/?r=vhuyaplay%2Fvideo&vid={$vid}";
		$content = self::curl($api);
		$data = json_decode($content,true);
		if($data["code"]==1){
			$result = $data["result"];
			$cover = $result["cover"];
			$videoinfo["poster"] = $cover;
			$items = $result["items"];
			foreach ($items as $key => $value) {
				$height = $value["height"];
				$vurl = $value["transcode"]["urls"][0];
				switch ($height) {
					case "360":$def="标清";break;
					case "540":$def="高清";break;
					case "720":$def="超清";break;
					case "1080":$def="超高清";break;
				}
				if (GlobalBase::is_ipad()) {
					if($height=='720'){
						$videoinfo['code'] = 200;
						$videoinfo["data"]["url"]= $vurl;
						break;
					}
				}else{
					$video[0] = $vurl;
					$video[1] = "video/mp4";
					$video[2] = $def;
					$video[3] = $height!='720' ? 0 : 10;
					$videoinfo['code'] = 200;
					$videoinfo["data"]["video"][$key] = $video;
					$videoinfo["data"]["flashplayer"] = true;
				}
		    }
		    return $videoinfo;
		}
	}
	public static function curl($url)
	{
		$params["ua"] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
		return GlobalBase::curl($url,$params);
	}
}