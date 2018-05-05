<?php
namespace app\api\controller;
class Base
{
    /**
     * Base constructor.
     * 构造函数初始化签名验证
     */	 
    public function __construct()
    {
        $this->checkParams();
    }
    /**
     * 校验签名
     */
    private function checkParams()
    {   
	
        
    }
}
