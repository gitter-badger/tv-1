<?php
namespace app\api\controller;
use app\BaseController;
use think\facade\Cache;
use app\facade\Http;

class Zhuishu extends BaseController
{
	public function booksource($id='/道君/跃千愁/第一千一百章%20江湖人称盗爷/index.html'){   
		$data =  json_decode(Http::doGet('https://api.zsdfm.com/UrlSource'.$id), true);break;			
		return json($data);
	}  	
	public function chapter(,$id='/115088/5946751.html'){
		$data =  json_decode(Http::doGet('https://shuapi.jiaston.com/book'.$id), true);break;
		return json($data);		
	}	
	public function chapter-list($id='115088'){   
		$data =  json_decode(Http::doGet('https://https://shuapi.jiaston.com/book/'.$id), true);break;
		return json($data);		
	}
	public function search($id='道君'){
		$data =  json_decode(Http::doGet('http://api.easou.com/api/bookapp/searchdzh.m?word='.$id.'&page_id=1&count=20&cid=eef_&os=ios&appverion=1049'), true);break;
		return json($data);		
	}
}