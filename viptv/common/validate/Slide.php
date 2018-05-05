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
** 幻灯片表 模型验证器
*/

class Slide extends Validate {
	
	// 验证规则
	protected $rule = [
			'cid' 		=> 'number',
			'cid' 		=> 'checkCid', 			// 自定义函数验证
			'title' 	=> 'require',
		];
	
	// 返回对应信息
	protected $message = [
			'cid.number' 		=> '分类不正确',
			'cid.checkCid' 		=> '分类不正确',
			'title.require' 	=> '标题不能为空',
		];
	
	// 验证场景
	protected $scene = [
			'add' 	=> ['cid', 'title'],
			'edit' 	=> ['cid', 'title']
		];
	
	/*
	** 验证幻灯片分类是否存在验证幻灯片分类是否存在
	** 可以传入的参数共有5个（后面三个根据情况选用），依次为：
	** 验证数据、验证规则、全部数据（数组）、字段名、字段描述
	*/
	protected function checkCid($cid, $rule, $data) {
		if($cid == 0){
			return false;
		}
		
		$find = Loader::model('Slideseat') -> where(array('id' => $cid)) -> value('id');
		if($find){
			return true;
		}
		return false;
	}
	
}
