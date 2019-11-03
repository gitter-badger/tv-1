<?php
namespace app\facade;
use think\facade;


class Zhibo extends Facade
{
	protected static function getfacadeClass()
    {		    
        
		return 'app\api\Controller\Zhibo';
    }
    
		
}