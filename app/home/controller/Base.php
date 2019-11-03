<?php
namespace app\home\controller;

use think\facade\Config;
use think\facade\Cache;
use app\BaseController;
use think\facade\View;

use think\App;

class Base extends BaseController;
{
  protected $request;
	protected $app;
	protected $view;
	protected $zhibo;
	
	public function __construct(App $app,View $view)
  {
    $this->app     = $app;
		$this->view     = $view;
    $this->request = $this->app->request;
		$this->$zhibo =	use app\facade\Zhibo;	
    $cache = Cache::get('nav');    
		if($cache){  
			$VodData = $cache;
    }else{
      foreach($this->$zhibo->migu('livelist') as $k => $v) {
        $VodData[$k]['cateid'] = $v['vomsID'];
        $VodData[$k]['name'] = $v['name'];
      }	
      Cache::set('nav',$VodData);
    }	
		View::assign('nav',$VodData);
  }
	public function display($tpl){
		$this->filterView();
		return $this->view->fetch($tpl);
	}	
	public function __call($method, $args)
  {
    return json(['status'=>'01','msg'=>'方法不存在']);
  }
}