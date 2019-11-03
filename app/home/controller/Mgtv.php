<?php

namespace app\home\controller;

use think\facade\Config;
use think\facade\Cache;
use app\facade\Zhibo;
use think\facade\Request;
use app\BaseController;
use think\facade\View;


class Mgtv extends BaseController
{
	public function __construct()    {
		$cache = Cache::get('nav');
		if($cache){  
			$data=$cache;
    }else{
      $livelist = Zhibo::Migu('livelist');
      foreach($livelist as $k => $v) {	
        $VodData[$k]['cateid'] = $v['vomsID'];
        $VodData[$k]['name'] = $v['name'];
      }
      $data=['nav'=>$VodData,'webname'=>'VipTv -咪咕电视','title'=>'VipTv -咪咕电视','keywords'=>'电视,咪咕','description'=>'VipTV,�乾,����ֱ��'];	
      Cache::set('nav',$data);
    }		
    view::assign($data);
		
  }	
	public function Index() {  
	  /* $cache = Cache::get('IndexRot');
		if($cache){
			$Data=$cache;
		}
		$livelist = (Zhibo::Migu('datalist',$id))['body']['dataList'];
        foreach($livelist as $k => $v) {	
            $Data[$k]['pID'] =  isset($v['nowPlaying'])?$v['pID']:'';;
           	$Data[$k]['name'] =  isset($v['nowPlaying'])?$v['name']:'';;
			$Data[$k]['nowPlaying'] = isset($v['nowPlaying'])?$v['nowPlaying']:'';
           	$Data[$k]['startTime'] =  isset($v['nowPlaying'])?$v['startTime']:'';;
			$Data[$k]['endTime'] =  isset($v['nowPlaying'])?$v['endTime']:'';;
		} 
		view::assign('livelist',$Data);
		Cache::set('TvIndexRot',$Data);	 */	
    
		return view();
  }	
	public function TvVideo($id='623364608')   
  {  
		$cache = Cache::get('TvVideo_'.$id);
		if($cache){
			$Data=$cache;
		}else{
      $Data['contId']=(Zhibo::Migu('playurl',$id))['content']['contId'];
      $Data['contName']=(Zhibo::Migu('playurl',$id))['content']["contName"];
      $Data['playName']=(Zhibo::Migu('playurl',$id))['playBill']["playName"];
      $Data['programName']=(Zhibo::Migu('program',$id))[0]["programName"];
      $Data['epg']=(Zhibo::Migu('program',$id))[0]['content'];		
      Cache::set('TvVideo_'.$id,$Data);
    }	
		view::assign('data',$Data);
		return view();    
	}
	public function TvPlay($id='623364608')   
    { 
		$data = Zhibo::Migu('playurl',$id);	
    if(!$data){return '和服务器失去联系，暂时播放不了，请切换！' ;}
		view::assign('data',$data['content']);
		return view();
  }
	public function MgPlay($type='mgl',$url='623364608')  
  { 		
		view::assign(['type'=>$type,'url'=>$url]);
		return view();
  }
	public function TvLoading(){  
		return view();
  }

	public function TvList()
  {  
	  $cache = Cache::get('TvList_');
		if($cache){  
			$Data=$cache;
    }else{
      $livelist = Zhibo::Migu('livelist');
      foreach($livelist as $k => $v) {	
        $Data[$k]['vomsID'] = $v['vomsID'];
        $Data[$k]['name'] = $v['name'];
      	$Data[$k]['listinfo'] = Zhibo::Migu('datalist',$v['vomsID']);
      }		
      Cache::set('TvList_',$Data);      
    }
    view::assign('livelist',$Data);
    return  view();
  }  
}