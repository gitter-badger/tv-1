<?php
namespace app\common\validate;
use think\Validate;
use \think\Loader;

// +----------------------------------------------------------------------
// | VenusCMF
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2099
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 水蕃茄 <lzhf237@126.com>
// +----------------------------------------------------------------------

/*
** 幻灯片分类表 模型验证器
*/

class Slideseat extends Validate {
	
	// 验证规则
	protected $rule = [
			'title' 	=> 'require',
		];
	
	// 返回对应信息
	protected $message = [
			'title.require' => '标题不能为空',
		];
	
	// 验证场景
	protected $scene = [
			'add' 	=> ['title'],
			'edit' 	=> ['title']
		];
	
	
}
