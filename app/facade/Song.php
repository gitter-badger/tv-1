<?php
namespace app\facade;
use think\facade;



class Song extends Facade
{
	protected static function getfacadeClass()
    {		    
        
		return 'ext\api\song';
    }
    
		
}