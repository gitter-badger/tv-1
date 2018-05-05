<?php
namespace app\home\model;
use think\Model;
class Play extends Model
{
    public function tv_find($id){
        $info = $this->field('*')->where('play_id', $id)->find();
        return $info;
    }
}