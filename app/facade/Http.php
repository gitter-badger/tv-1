<?php
namespace app\facade;
use think\facade;



class Http extends Facade
{
	protected static function getfacadeClass()
    {		    
        
		return 'ext\Org\Http';
    }
    
		
}