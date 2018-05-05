<?php
namespace app\home\controller;
use app\common\controller\HomeBase;

class Index extends HomeBase
{    
    public function index()
    {           
        return view();
    } 
	public function demo()
    {         
              
        $path = "application\\home\\controller\\index.php";

        // 定义输出文字
        $html = "<p>我是 [path] 文件的index方法</p>";

        // 调用temphook钩子, 实现钩子业务
        hook('temphook', ['data'=>$html]);

        // 替换path标签
        return str_replace('[path]', $path, $html);
    } 
}
        