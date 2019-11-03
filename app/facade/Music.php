<?php
namespace app\facade;

use think\Facade;

class Music extends Facade
{
    protected static function getFacadeClass()
    {
    	return 'ext\lib\Music';
    }
}