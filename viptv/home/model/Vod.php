<?php
namespace app\home\model;
use think\Model;
class Vod extends Model
{
    public function find($vid){
        $db = $this->field('*')->where('vod_id', $vid)->find();
        $vod_play=explode("$$$",$db['vod_play']);
        $vod_api=explode("###",$db['vod_api']); 
        foreach($vod_play as $k=>$v){
            $i=$k+1;
            $api_play=explode("$$$",$vod_api[$k]);
            foreach($api_play as $key=>$val){             
                $dd=explode('/',$val);
                $title=controller('Ajax')->title($dd[0],$dd[1]);
                $play_list[] = ['pid'=>$v,'lid'=>$dd[0],'vid'=>$dd[1],'name'=>$title];
            }
        }
        return ['vod_id'=>$db->vod_id,'vod_title'=>$db->vod_title,'vod_play'=>$db->vod_play,'vod_api'=>$db->vod_api,'list'=>array_merge($play_list)];
    }
    public function detail($name){
        $info = self::field('*')->where('vod_tag','like','%' . $name . '%')->order('vod_id', 'asc')->select();
        return $info;
    }
}