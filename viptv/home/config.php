<?php
//配置文件
return [
    // +----------------------------------------------------------------------
    // | 模板替换
    // +----------------------------------------------------------------------
    'view_replace_str'  =>  [
	    '__ROOT__'      =>  request()->root() ,
		'__STATIC__'    =>  request()->root() . '/public/static',
        '__JS__'     => request()->root() . '/public/static/home/js',
        '__IMG__'    => request()->root() . '/public/static/home/img',
        '__CSS__'    => request()->root() . '/public/static/home/css',
        '__SWI__'    => request()->root() . '/public/static/home/swi',
        '__CMP__'    => request()->root() . '/public/static/home/player/cmp',
        '__CKP__'    => request()->root() . '/public/static/home/player/ckp',
        '__CHP__'    => request()->root() . '/public/static/home/player/chp',
        '__CUP__'    => request()->root() . '/public/static/home/player/cup',
        '__JWP__'    => request()->root() . '/public/static/home/player/jwp',
        '__SWF__'    => request()->root() . '/public/static/home/player/swf',
        '__SMU__'    => request()->root() . '/public/static/home/player/smu',
        '__MUS__'    => request()->root() . '/public/static/home/player/mus',	
		'__Player__'    => request()->root() . '/public/static/home/player',
    ],  
];