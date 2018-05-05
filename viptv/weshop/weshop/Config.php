<?php
return array(
    'name' => '微商城',
    'addon' => 'weshop',
    'desc' => '微商城',
    'version' => '1.0',
    'author' => 'mrlv',
    'logo' => 'logo.jpg',
    'entry_url' => 'weshop/index/index',
    //'install_sql' => 'install.sql',
    'upgrade_sql' => '',
    'menu' => [
        [
            'name' => 'Banner管理',
            'url' => 'weshop/Banner/index',
            'icon' => ''
        ],
        [
            'name' => '商品分类',
            'url' => 'weshop/Type/index',
            'icon' => ''
        ],
        [
            'name' => '商品管理',
            'url' => 'weshop/Goods/index',
            'icon' => ''
        ],
        [
            'name' => '订单管理',
            'url' => 'weshop/Order/index',
            'icon' => ''
        ],
    ],
    //有快递接口的可以启用下边的
    /*'config' => array(
        [
            'name' => 'express',
            'title' => '快递公司',
            'type' => 'select',
            'value' => [
                '0' => [
                    'name'=>'顺丰快递',
                    'value'=>'shunfeng',
                ],
                '1' => [
                    'name'=>'申通快递',
                    'value'=>'shentong',
                ],
                '2' => [
                    'name'=>'天天快递',
                    'value'=>'tiantian',
                ],
                '3' => [
                    'name'=>'圆通快递',
                    'value'=>'yuantong',
                ],
                '4' => [
                    'name'=>'韵达快递',
                    'value'=>'yunda',
                ],
                '5' => [
                    'name'=>'中通快递',
                    'value'=>'zhongtong',
                ],
                '6' => [
                    'name'=>'EMS',
                    'value'=>'ems',
                ],
            ],
            'placeholder' => '',
            'tip' => '',
        ],
        [
            'name' => 'key',
            'title' => 'KEY',
            'type' => 'text',
            'value' => '',
            'placeholder' => '',
            'tip' => '快递查询接口KEY',
        ],
    ),*/
);