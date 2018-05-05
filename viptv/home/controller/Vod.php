<?php
namespace app\home\controller;
use app\common\controller\HomeBase;
use think\Controller; 
use think\Validate;
class Vod extends HomeBase
{
    public function vist($id='') 
    {
        $title=Model("Nav")->nav_icon($id)->nav_title;
        $data=Model("Vod")->detail($title);
        if(!isset($title)){
            $error = '本频道被禁用或已删除！';
            $this->error($error);
        }
        $this->assign([                
            'data'  => $data,
            'title'  => $title
        ]); 
        return view();  
    }
    public function play($id='')    
    {           
        $db=Model("Vod")->find($id);
        $result = $this->validate($db, 'Vod');      
        if (true !== $result) $this->error($result);
        $this->assign([             
            'title'  =>'『 '. $db['vod_title'] . ' 』 ' ,
            'api'  => url('Ajax/api',['lid'=>1,'vid'=>$id]),
        ]);
        return view();
    }
    public function load()
	{         
        return view();
    }	
    public function vod()
    {       
        $this->assign([
            'api'  => url('Ajax/api',['lid'=>input('param.lid'),'vid'=>input('param.vid')]),
            'ptitle' => 'CMP播放器'
        ]);
        return view();
    }	
    public function cmp()
    {       
        $this->assign([
            'api'  => url('Ajax/api',['lid'=>input('param.lid'),'vid'=>input('param.vid')]),
            'ptitle' => 'CMP播放器'
        ]);
        return view();
    }
    public function smu()
    {  
        $this->assign([
            'api'  => url('Ajax/api',['lid'=>input('param.lid'),'vid'=>input('param.vid')]),
            'ptitle' => 'SMU播放器'
        ]);
        return view();
    } 
    public function music()
    {  
        $this->assign([
		    'lid' => input('param.lid'),
			'vid' => input('param.vid'),
            'api'  => url('Ajax/api',['lid'=>input('param.lid'),'vid'=>input('param.vid')]),
            'ptitle' => 'Music播放器'
        ]);
        return view();
    } 	
    public function api()
    {  
        $this->assign([		    
            'api'  => url('Ajax/api',['lid'=>'400']),
            'ptitle' => 'API万能播放接口'
        ]);
        return view();
    }	
    public function tv()
    {  
        $this->assign([
            'api'  => url('Ajax/api',['lid'=>input('param.lid'),'vid'=>input('param.vid')]),
            'ptitle' => 'tv播放器'
        ]);
        return view();
    } 	
    public function mus()
    {  
        $this->assign([
            'api'  => url('Ajax/api',['lid'=>input('param.lid'),'vid'=>input('param.vid')]),
            'ptitle' => 'SMU播放器'
        ]);
        return view();
    } 	
    public function ckp()
    {  
        $this->assign([
            'api'  => url('Ajax/api',['lid'=>input('param.lid'),'vid'=>input('param.vid')]),
            'vid' => input('param.vid'),
            'ptitle' => 'CKP播放器'
        ]);
        return view();
    }
    public function chp()
    {  
        $this->assign([
            'api'  => url('Ajax/api',['lid'=>input('param.lid'),'vid'=>input('param.vid')]),
            'ptitle' => 'CHP播放器'
        ]);
        return view();
    }   
    public function ckl()
    {   
        $this->assign([
            'api'  => url('Ajax/api',['lid'=>input('param.lid'),'vid'=>input('param.vid')]),
            'ptitle' => 'CkL列表播放器'
        ]); 
        return view();
    }
    public function chl()
    {   
        $this->assign([
            'api'  => url('Ajax/api',['lid'=>input('param.lid'),'vid'=>input('param.vid')]),
            'ptitle' => 'CHL列表播放器'
        ]); 
        return view();
    }   
    public function jwp()
    {   
        $this->assign([
            'api'  => url('Ajax/api',['lid'=>input('param.lid'),'vid'=>input('param.vid')]),
            'ptitle' => 'JWP播放器'
        ]); 
        return view();
    }
    public function jwl()
    {   
        $this->assign([
            'api'  => url('Ajax/api',['lid'=>input('param.lid'),'vid'=>input('param.vid')]),
            'ptitle' => 'JWL播放器'
        ]); 
        return view();
    }	
    public function cup()
    {   
        $this->assign([
            'api'  => url('Ajax/api',['lid'=>input('param.lid'),'vid'=>input('param.vid')]),
            'ptitle' => 'CUP播放器'
        ]); 
        return view();
    }
}