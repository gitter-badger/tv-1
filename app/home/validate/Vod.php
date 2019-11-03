<?php
namespace app\home\validate;
use think\Validate;
class Vod extends Validate
{
     //定义验证规则
    protected $rule = [
        'vod_id'        => 'require',
        'vod_play'      => 'require',
        'vod_api'       => 'require',
    ];
    //定义验证提示
    protected $message = [
        'vod_id.require'      => '亲，本节目还在外太空神游呢！！！', 
        'vod_play.require'    => '亲，播放器已走失，请帮我找回！',
        'vod_api.require'     => '亲，节目源失效或因版权而删除！',

    ];
}