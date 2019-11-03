<?php
namespace app\home\controller;
use think\facade\View;
use app\BaseController;
use ext\lib\Qiyi;
use app\api\Controller\video;

class Index extends BaseController
{
    public function index($type='dp',$url='')
    {			
		view::assign(['type'=>$type,'url'=>$url]);
        return view();
    }
}
