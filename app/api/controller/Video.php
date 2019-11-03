<?php
namespace app\api\controller;
use think\facade\Request;
use think\facade\Config;
use think\facade\Cache;
use app\facade\Http;

class Video 
{
	
	public static function miguvideo($type,$id='')  
	{
		    switch ($type) {
				case 'groups':$data = json_decode(Http::doGet('http://www.miguvideo.com/gateway/display/v1/data/groups/'.$id), true)['body']['components'][1];break;
				case 'gateway':$data = json_decode(Http::doGet('http://www.miguvideo.com/gateway/display/v1/layout/pages/'.$id), true);break;
				case 'content':$data = json_decode(Http::doGet('http://www.miguvideo.com/gateway/program/v1/cont/content-info/'.$id), true)['body']['data'];break;
				case 'playurl':$data = json_decode(Http::doGet('http://www.miguvideo.com/gateway/playurl/v3/play/playurl?contId='.$id), true)['body']['urlInfo'];break;				
			}
			
		return $data;	
	} 
	 
	public static function parse($url)
	{
		if(strstr($url,"vip.kankan.com")==true){
			preg_match('#/vod/(.*).html#iU',$url,$mid);
			$movieid = $mid[1];
			self::get_vip_video_url($movieid);
		}else{
			$html = Http::file_get_contents($url);
			preg_match('#id:\s*(\d+),#iU',$html,$_id);
			preg_match('#submovieid:(\d+),#iU',$html,$_vid);
			$id = $_id[1];
			$vid = $_vid[1];
			$params = parse_url($url);
			if(isset($params["query"])){
				$vid = $params["query"];
			}
			return self::get_video_url($id,$vid);
		}
	}
	public static function get_video_url($id,$vid)
	{
		$gcid = self::get_gcid($id,$vid);
		$api = "http://mp4.cl.kankan.com/getCdnresource_flv?gcid={$gcid}";
		$html = Http::file_get_contents($api);
		preg_match('#ip:"(.*)",#iU',$html,$_ip);
		preg_match('#port:(.*),#iU',$html,$_port);
		preg_match('#path:"(.*)"#iU',$html,$_path);
		preg_match('#param1:(.*),#iU',$html,$param1);
		preg_match('#param2:(.*)}#iU',$html,$param2);
		$ip = $_ip[1];
		$port = $_port[1];
		$path = $_path[1];
		$key1 = $param2[1];
		$key = md5("xl_mp43651".$param1[1].$key1);//秘钥可以为空，不能解析flv
		$vurl = "http://{$ip}/{$path}?key={$key}&key1={$key1}";
		$videoinfo["video"]["file"] = $vurl;
		$videoinfo["video"]["type"] = "video/mp4";
        return $videoinfo;
	}
	public static function get_gcid($id,$vid)
	{
		$no = substr($id,0,2);
		$api = "http://api.movie.kankan.com/vodjs/subdata/{$no}/{$id}/{$vid}.js";
		$html = Http::file_get_contents($api);
		preg_match('#{(.*)}#iU',$html,$js);
		$json = json_decode("{".$js[1]."}",true);
		$msurl = $json["msurls"][0];
		$gcid = explode("/",parse_url(explode(",", str_replace("'","",$msurl))[0])["path"])[2];
		return $gcid;
	}
	public static function get_vip_video_url($movieid)
	{
		$time = number_format(microtime(true),3,'','');
		$api = "http://auth.vip.kankan.com/vod/getPrevue?movieId={$movieid}&t={$time}";
		$html = Http::file_get_contents($api,COOKIE_KANKAN);
		$content = trim(str_replace("callback","",$html),"()");
		$sub_movies = json_decode($content,true)["data"]["sub_movies"];
		$count = 0;
		foreach ($sub_movies as $key => $value) {
			$byteType = $value["byteType"];
			$vurl = $value["previewVodUrl"];
			switch ($byteType) {
				case 2:$def="高清";break;
				case 4:$def="超清";break;
				default:$def="自动";break;
			}
	    	$video[0] = $vurl;
			$video[1] = "video/flv";
			$video[2] = $def;
			$video[3] = $byteType == 2 ? 10: 0;
			$video_info["video"][$count] = $video;
			$count++;
		}
        return $video_info;
	}
}
