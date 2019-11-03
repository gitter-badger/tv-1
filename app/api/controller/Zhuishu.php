<?php
namespace app\api\controller;
use app\BaseController;
use think\facade\Cache;
use app\facade\Http;

class Zhuishu extends BaseController
{
	public function Rank($type,$id=''){       
		switch ($type) {
			case 'gender': $data =  json_decode(Http::doGet('http://api.zhuishushenqi.com/ranking/gender'), true);break;	
			case 'info': $data = json_decode(Http::doGet('http://api.zhuishushenqi.com/ranking/'.$id), true);break;
		} 
		return json($data);
  }  	
	public function Book($type,$id=''){   
    switch ($type) {
			case 'gender': $data =  json_decode(Http::doGet('http://api.zhuishushenqi.com/ranking/gender'), true);break;	
			case 'info': $data = json_decode(Http::doGet('http://api.zhuishushenqi.com/ranking/'.$id), true);break;
		} 
		return json($data);
  }
	/* 根据分类获取小说列表
     * @param {String} gender 可选：male/female/press
     * @param {String} type 可选：hot(热门)/new(新书）/reputation(好评)/over(完结)/monthly(包月)
     * @param {String} major
     * @param {String} minor
     * @param {Number} start
     * @param {Number} limit
     * https://api.zhuishushenqi.com/book/by-categories?gender=male&type=hot&major=奇幻&minor=&start=0&limit=20
     */
 
	public function category($type,$id=''){   
    switch ($type) {
			case 'gender': $data =  json_decode(Http::doGet('http://api.zhuishushenqi.com/ranking/gender'), true);break;	
			case 'info': $data = json_decode(Http::doGet('http://api.zhuishushenqi.com/ranking/'.$id), true);break;
		} 
		return json($data);
  }
	public function comment($type,$id=''){   
    switch ($type) {
			case 'discussions': $data = json_decode(Http::doGet('http://api.zhuishushenqi.com/ranking/'.$id), true);break;
			case 'shortreviews': $data = json_decode(Http::doGet('http://api.zhuishushenqi.com/ranking/'.$id), true);break;
			case 'bookreviews': $data = json_decode(Http::doGet('http://api.zhuishushenqi.com/ranking/'.$id), true);break;
		} 
		return json($data);
  }	

	/* 根据分类获取小说列表
	 * @param {String} gender: 男生:male 女生:female 出版:press
	 * @param {String} type: 热门:hot 新书:new 好评:repulation 完结: over 包月: month
	 * @param {String} major: 大类别 从接口1获取
	 * @param {String} minor: 小类别 从接口4获取 (非必填)
	 * @param {Number} 分页开始页
	 * @param {Number} 分页条数
	 * https://api.zhuishushenqi.com/book/by-categories?gender=male&type=hot&major=奇幻&minor=&start=0&limit=20
	 */
	public function Book($type,$id='548d9c17eb0337ee6df738f5'){
		switch ($type) {			
			case 'info': $data = json_decode(Http::doGet('http://api.zhuishushenqi.com/book/'.$id), true);break;  /* 书籍详情 */
			case 'btoc': $data = json_decode(Http::doGet('http://api.zhuishushenqi.com/btoc?view=summary&book='.$id), true);break;
			case 'atoc': $data = json_decode(Http::doGet('http://api.zhuishushenqi.com/atoc?view=summary&book='.$id), true);break;
			case 'bmix': $data = json_decode(Http::doGet('http://api.zhuishushenqi.com/mix-btoc/'.$id.'?view=chapters?view=chapters'), true);break;
			case 'amix': $data = json_decode(Http::doGet('http://api.zhuishushenqi.com/mix-atoc/'.$id.'?view=chapters'), true);break; /* 书源 */
			case 'recommend': $data = json_decode(Http::doGet('http://api.zhuishushenqi.com/book/'.$id.'/recommend'), true);break;  /* 相关推荐 */
			case 'booksearch': $data = json_decode(Http::doGet('http://api.zhuishushenqi.com/book/fuzzy-search?query='.$id.'/recommend'), true);break;	/* 书籍搜索 可以搜索作者但是不精确	 */
			case 'picturec': $data = json_decode(Http::doGet('http://api.zhuishushenqi.com/book/'.$id.'/recommend'), true);break;	  /* 漫画内容 pictureContent */
		} 
		return json($data);
	}	
	public function category($type,$id=''){   
		switch ($type) {
			case 'statistics': $data =  json_decode(Http::doGet('http://api.zhuishushenqi.com/cats/lv2/statistics'), true);break;	 /* 带书籍数量的父分类 */
			case 'cats': $data = json_decode(Http::doGet('http://api.zhuishushenqi.com/cats/lv2'), true);break; /* 带子分类的父分类 */
			case 'info': $data =  json_decode(Http::doGet('http://novel.juhe.im/category-info?gender=male&type=hot&major=奇幻&minor=&start=0&limit=20'), true);break;	  /* 分类详情: 带着书籍 */
		} 
		return json($data);
	} 
	public function search($type,$id=''){   
		switch ($type) {
			case 'hotwords': $data =  json_decode(Http::doGet('http://api.zhuishushenqi.com/book/search-hotwords'), true);break;	 /* 搜索热词 */
			case 'hotword': $data = json_decode(Http::doGet('http://api.zhuishushenqi.com/book/hot-word'), true);break; /* 热门搜索 */
			case 'auto': $data =  json_decode(Http::doGet('http://api.zhuishushenqi.com/book/auto-complete?query='.$id), true);break;	  /* 搜索自动补充 */
			case 'fuzzy': $data =  json_decode(Http::doGet('http://api.zhuishushenqi.com/book/fuzzy-search?query=?query='.$id), true);break;	  /* 模糊搜索 */
		} 
		return json($data);
	}
	public function comment($type,$id=''){   
		switch ($type) {
			case 'discussions': $data = json_decode(Http::doGet('http://api.zhuishushenqi.com/post/by-book'.$id), true);break; /* 讨论 */
			case 'shortreviews': $data = json_decode(Http::doGet('http://api.zhuishushenqi.com/post/short-review'.$id), true);break;  /* 短评 */
			case 'bookreviews': $data = json_decode(Http::doGet('http://api.zhuishushenqi.com/post/review/by-book'.$id), true);break;  /* 长评 */
	  } 
		return json($data);
  }
	public function  booklist($type,$id=''){
		switch ($type) {
			case 'lists': $data = json_decode(Http::doGet('http://api.zhuishushenqi.com/book-list'), true);break; 
			case 'detail': $data = json_decode(Http::doGet('http://api.zhuishushenqi.com/book-list'.$id), true);break;  
	  } 
		return json($data);
	}
}