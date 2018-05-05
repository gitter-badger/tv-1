<?php
namespace app\home\controller;
use app\common\controller\HomeBase;
use think\Request;
class User extends HomeBase
{    

	public function index()
    {
        if (!session('user_id') || !session('user_name')) {
            $this->error('亲！请登录',url('login'));
        } else {
            $forum = Db::name('user');
            $user_id = session('user_id');
            $count=$forum->where("user_id = {$user_id}")->count();
            $tptc = $forum->where("user_id = {$user_id}")->order('id desc')->paginate(10);
            $this->assign('tptc', $tptc);
            $this->assign('user_id', $user_id);
            $this->assign('count', $count);
            return view();
        }
    }
    public function forget()
    {   
        return view();
    }   
    public function login()
    {  
        if (request()->isPost()){
            $data=request()->Post('.');
			dump($data);
        }	
        return view();
    }
    public function register()
    {   
        return view();
    } 
}
        