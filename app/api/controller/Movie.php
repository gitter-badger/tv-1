<?php
namespace app\api\controller;
use app\BaseController;
use think\facade\Cache;
use think\facade\Db;
use app\facade\Http;



class Movie extends BaseController
{
	public function Zyapi($type,$id='')
    {	         	
        $DB=json_decode(Http::doGet('http://www.800zyapi.com/long/json.php'), true);
		$config=[
			'type'       => 'mysql',		
			'hostname'    => $DB['DB_HOST'],			
			'database'    => $DB['DB_DATABASE'],			
			'username'    => $DB['DB_USER'],			
			'password'    => $DB['DB_PASSWORD'],			
		];
		$base=Db::connect($config);
		
		switch ($type) {
			case 'nav': $VodData = $base->table('mac_vod_type')->field('t_id,t_name,t_pid,t_sort')->select();;break;
            case 'search': $VodBase = $base->table('mac_vod')->where('d_name',$id)->select();break;			
            case 'play': $VodBase = empty($id) ?$base->table('mac_vod')->field('d_id,d_name,d_pic,d_playurl')->select() : $base->table('mac_vod')->where('d_type',$id)->field('d_id,d_name,d_pic,d_playurl')->select();break;
		}				
	    return json($VodData);	 
    }
	public function CjYun($s,$type,$id='')
    {		   
	    switch ($type) {
			case 'p': $CaiData = json_decode(Http::doGet(config('movie.zyapi')[$s]['url'].'?p='.$id), true);break;
            case 'cid': $CaiData = json_decode(Http::doGet(config('movie.zyapi')[$s]['url'].'?cid='.$id), true);break;			
          
		} 
		foreach($CaiData ['data'] as $k =>$v) {				
            $Data[$k]['vod_id'] = $v['vod_id'];
           	$Data[$k]['vod_name'] = $v['vod_name'];
			$Data[$k]['vod_actor'] = $v['vod_actor'];
			$Data[$k]['vod_content'] =$v['vod_content']; 
			$Data[$k]['vod_pic'] =$v['vod_pic']; 
			$Data[$k]['vod_addtime'] =$v['vod_addtime']; 
			$Data[$k]['vod_url'] =$this->jax(explode('$$$', $v['vod_url'])[0]); 
			$Data[$k]['vod_m3u8'] =$this->jax(explode('$$$', $v['vod_url'])[1]); 
		}
		if (Request::isPost()){
			return $Data;
		}	
	    return json($Data);			 
    }
	public function jax($data)
    {	
	   $urllist=explode(PHP_EOL, $data);
	   foreach($urllist as $k =>$v) {
		  $Data[$k]['vod_name'] = explode('$', $v)[0]; 
		  $Data[$k]['vod_url'] = explode('$', $v)[1];
	   }
	   return $Data;	
    }	
}
