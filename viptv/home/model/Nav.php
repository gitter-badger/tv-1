<?php
namespace app\home\model;

use think\Model;

class Nav extends Model {
    public function nav_pid($id){
        $data_list = self::field('*')->where('nav_pid', $id)->select();
        if (!$data_list) {
            $this->error = $this->getError();
            return false;
        }
        return $data_list;
    }   
    public function nav_icon($id){
        $data_list = self::field('*')->where('nav_icon', $id)->find();
        if (!$data_list) {
            $this->error = $this->getError();
            return false;
        }
        return $data_list;
    }   
}
