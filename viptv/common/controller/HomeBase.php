<?php
namespace app\common\controller;
use think\Controller;
use think\Config;
use think\Cookie;
/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class HomeBase extends Controller
{   
  
	protected $url;
	protected $request;
	protected $module;
	protected $controller;
	protected $action;
	
    public function __construct(){ 
        parent::__construct();
		$type = \think\Request::instance()->isMobile() ? 'moblie' : 'pc';
		
    }
    public function _empty(){
        $this->error('不允许非法操作，您的IP已经记录！');
    }	
    protected function _initialize(){ 
	    header("Content-Type:text/html; charset=utf-8");
        if(!Model("Config")->tv_find(6)){
            $this->error('站点已经关闭，请稍后访问~');
        }
        $this->assign([
            'url'  => Model("Config")->tv_find('WEB_URL'),
            'title' => Model("Config")->tv_find('WEB_TITLE'),
			'slug' => Model("Config")->tv_find('WEB_SLUG'),
            'keywords' => Model("Config")->tv_find('WEB_KEYWORDS'),			
            'description' => Model("Config")->tv_find('WEB_DESCRIPTION'),
			'qqkefu' => Model("Config")->tv_find('QQ_KEFU'),
			'qqqun' => Model("Config")->tv_find('QQ_QUN'),
            'copyright' => Model("Config")->tv_find('WEB_COPYRIGHT'),
			'ver' => Model("Config")->tv_find('WEB_VER'),
            'nav' => Model("Nav")->nav_pid(0),
            'nav_live' => Model("Nav")->nav_pid(1),            
            'nav_vod'  => Model("Nav")->nav_pid(2),
            'nav_vip'  => Model("Nav")->nav_pid(3),
            'nav_app'  => Model("Nav")->nav_pid(4),
			'playad' =>1,  //播放器倒计时广告开关，1：开，0：关
			'timer'  => 60, //播放器倒计时广告总时间
        ]);   
    }
	protected function requestInfo() {
		
		defined('MODULE_NAME') or define('MODULE_NAME', $this->request->module());
		defined('CONTROLLER_NAME') or define('CONTROLLER_NAME', $this->request->controller());
		defined('ACTION_NAME') or define('ACTION_NAME', $this->request->action());
		defined('IS_POST') or define('IS_POST', $this->request->isPost());
		defined('IS_AJAX') or define('IS_AJAX', $this->request->isAjax());
		defined('IS_GET') or define('IS_GET', $this->request->isGet());
		defined('IS_MOBILE') or define('IS_MOBILE', $this->request->isMobile());

		$this->param = $this->request->param();
		$this->simple_url = strtolower($this->request->module() . '/' . $this->request->controller() . '/' . $this->request->action());
		$this->ip = $this->request->ip();
		$this->url = $this->request->url(true);//完整url
	}	
    
}
        