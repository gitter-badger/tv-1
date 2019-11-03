<?php
namespace app\api\controller;
use app\BaseController;
use think\facade\Cache;
use app\facade\Http;

class Zhibo extends BaseController{
    public function Migu($type,$id=''){   
        switch ($type) {
			case 'livelist':$TvData =  json_decode(Http::doGet('http://live.miguvideo.com/live/v2/tv-data/70002091'), true)['body']['liveList'];break;	
			case 'datalist': $TvData = isset(json_decode(Http::doGet('http://live.miguvideo.com/live/v2/tv-data/'.$id), true)['body']['dataList'])?json_decode(Http::doGet('http://live.miguvideo.com/live/v2/tv-data/'.$id), true)['body']['dataList']:'';break;
			case 'playurl': 
				$UrlData = json_decode(Http::doGet('http://h5.miguvideo.com/playurl/v1/play/playurlh5?contId='.$id.'&rateType=1,2,3&clientId=migu'), true)['body'];
				$TvData = str_replace("http://h5live.gslb.cmvideo.cn","http://mgzb.live.miguvideo.com:8088",$UrlData);break;
				
			case 'program': $TvData = json_decode(Http::doGet('http://live.miguvideo.com/live/v2/tv-programs-data/'.$id.'/'.date("Ymd ", time())), true)['body']['program'];break;
		}        	
	    return $TvData;
    }
	public function CnTv($id='cctv1'){       
        $TvData=json_decode(Http::doGet('http://vdn.live.cntv.cn/api2/live.do?channel=pd://cctv_p2p_hd'.$id.'&client=flash'), true);
		if (Request::isPost()){
			return $TvData;
		}	
	    return $TvData;
    }
	public function bindou($classid=1,$id=0){       
        $TvData=Http::doGet('http://m.tv.bingdou.net/e/DownSys/DownSoft/?classid='.$classid.'&id='.$id);			
	    return $TvData;
    }	
	public function ivi($id='cctv1'){       
        $TvData=Http::doGet('http://ivi.bupt.edu.cn/');			
	    return $TvData;
    }	
	public function QtTv($id='764502578'){       
       $url=Http::doGet('http://liveaccess.qt.qq.com/get_video_url_v3?module='.$id.'&videotype=flv');
		preg_match('|"urllist":"(.*?)"|i', $url, $vid);		
		$TvData = explode(';', $vid[1])[1];		   	
	    return $TvData;
    }	
	public function ifeng($id='5435BFA3-210B-4F4F-A90F-BCB1C4C40D59'){  
		$json= ['4AC51C17-9FBE-47F2-8EE0-8285A66EAFF5','270DE943-3CDF-45E1-8445-9403F93E80C4','2c942450-2165-4750-80de-7dff9c224153','35383695-26c3-4ce5-b535-0001abce11e4'];
		$TvData=json_decode(Http::doGet('http://live.ifeng.com/liveAllocation.do?cid='.$id), true);			
	    return $TvData;	
    }	
	public function ZhiboTv($type,$id='1'){  	    
        switch ($type) {
			case 'list':$TvData =  json_decode(Http::doGet('http://rest.zhibo.tv/schedule/get-type-list-new'), true)['data']['typeList'];break;	
			case 'cate_id': $TvData = json_decode(Http::doGet('http://rest.zhibo.tv/anchor/get-list-by-type-id?page=1&id='.$id.'&equipment=1&size=20'),true); break;
			case 'url': $TvData = 'rtmp://live.zhibo.tv/8live/'.$id;break;
		}    
	    return $TvData;	
    }
	public function DouYu($id='537366'){  	    
        $TvData = json_decode(Http::doGet('https://m.douyu.com/html5/live?roomId='.$id),true)['data']; 			
	    return $TvData;	
    }	
	public function QieCdn($type,$id='197'){  	    
        switch ($type) {
			case 'cate':$TvData = ['name'=>['NBA','台球','足球','CBA','搏击','篮球','英文原声'],'id'=>['197','200','198','231','202','214','215']];break;	
			case 'cate_id': $TvData = json_decode(Http::doGet('http://api.qiecdn.com/api/v1/live/'.$id),true)['data']; break;
			case 'url': $TvData = json_decode(Http::doGet('http://api.qiecdn.com/api/v1/room/'.$id),true)['data']; $TvData=$TvData['rtmp_url'].'?'.$TvData['rtmp_live'];break;
		}  
	    return $TvData;	
    }	
}