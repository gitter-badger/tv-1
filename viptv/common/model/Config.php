<?php
namespace app\common\model;

use think\Model;

class Config extends Model {
    public function tv_find($name){
        $info = self::field('*')->where('config_name', $name)->find();
        if($info){
            return $info['config_value'];
        }
        return '没有查询到数据！' ;
    }
}
