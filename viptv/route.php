<?php
use think\Route;
return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[list]'    => [':id'   => ['vod/vist', ['method' => 'GET|POST'], ['id' => '\d+']] ],    
    '[play]'    => [':id'   => ['vod/play', ['method' => 'GET|POST'], ['id' => '\d+']]],
    '[cmp]'     => ['[:lid]/[:vid]'   => ['vod/cmp', ['method' => 'GET|POST'], ['lid' => '\d+','vid' => '\w+']]],
    '[cup]'     => ['[:lid]/[:vid]'   => ['vod/cup', ['method' => 'GET|POST'], ['lid' => '\d+','vid' => '\w+']]],
    '[ckp]'     => ['[:lid]/[:vid]'   => ['vod/ckp', ['method' => 'GET|POST'], ['lid' => '\d+','vid' => '\w+']]],
    '[ckl]'     => ['[:lid]/[:vid]'   => ['vod/ckl', ['method' => 'GET|POST'], ['lid' => '\d+','vid' => '\w+']]],   
    '[chp]'     => ['[:lid]/[:vid]'   => ['vod/chp', ['method' => 'GET|POST'], ['lid' => '\d+','vid' => '\w+']]],
    '[jwp]'     => ['[:lid]/[:vid]'   => ['vod/jwp', ['method' => 'GET|POST'], ['lid' => '\d+','vid' => '\w+']]],
    '[jwl]'     => ['[:lid]/[:vid]'   => ['vod/jwl', ['method' => 'GET|POST'], ['lid' => '\d+','vid' => '\w+']]],	
    '[chl]'     => ['[:lid]/[:vid]'   => ['vod/chl', ['method' => 'GET|POST'], ['lid' => '\d+','vid' => '\w+']]],   
    '[smu]'     => ['[:lid]/[:vid]'   => ['vod/smu', ['method' => 'GET|POST'], ['lid' => '\d+','vid' => '\w+']]],
    '[music]'     => ['[:lid]/[:vid]'   => ['vod/music', ['method' => 'GET|POST'], ['lid' => '\d+','vid' => '\w+']]],	
    '[api]'     => ['[:lid]/[:vid]'   => ['ajax/api', ['method' => 'GET|POST'], ['lid' => '\d+','vid' => '\w+']]], 
];