<?php
namespace app\home\controller;
use app\common\controller\HomeBase;

class Index extends HomeBase
{    
    public function index()
    {           
        return view();
    } 
	public function demo()
    {         
              
        $path = "application\\home\\controller\\index.php";

        // �����������
        $html = "<p>���� [path] �ļ���index����</p>";

        // ����temphook����, ʵ�ֹ���ҵ��
        hook('temphook', ['data'=>$html]);

        // �滻path��ǩ
        return str_replace('[path]', $path, $html);
    } 
}
        