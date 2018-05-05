<?php
namespace app\home\controller;
use think\Controller;
use think\db;
use Api\Music;


class Musicc extends Controller
{ 
    public function json()
    {
        $source = $this->getParam('source', 'netease');  // 歌曲源
		if($source == 'kugou' || $source == 'baidu') define('NO_HTTPS', true);    // 酷狗和百度音乐源暂不支持 https
		$API = new Music($source);
		$API->format(true); // 启用格式化功能
		switch($this->getParam('types'))   // 根据请求的 Api，执行相应操作
		{
			case 'url':   // 获取歌曲链接
				$id = $this->getParam('id');  // 歌曲ID        
				$data = $API->url($id);        
				$this->echojson($data);
				break;        
			case 'pic':   // 获取歌曲链接
				$id = $this->getParam('id');  // 歌曲ID        
				$data = $API->pic($id);        
				$this->echojson($data);
				break;    
			case 'lyric':       // 获取歌词
				$id = getParam('id');  // 歌曲ID        
				$data = $API->lyric($id);        
				$this->echojson($data);
				break;        
			case 'download':    // 下载歌曲(弃用)
				$fileurl = $this->getParam('url');  // 链接        
				header('location:$fileurl');
				exit();
				break;    
			case 'userlist':    // 获取用户歌单列表
				$uid = $this->getParam('uid');  // 用户ID        
				$url= 'http://music.163.com/api/user/playlist/?offset=0&limit=1001&uid='.$uid;
				$data = file_get_contents($url);        
				$this->echojson($data);
				break;        
			case 'playlist':    // 获取歌单中的歌曲
				$id = $this->getParam('id');  // 歌单ID        
				$data = $API->format(false)->playlist($id);        
				$this->echojson($data);
				break;     
			case 'search':  // 搜索歌曲
				$s = $this->getParam('name');  // 歌名
				$limit = $this->getParam('count', 20);  // 每页显示数量
				$pages = $this->getParam('pages', 1);  // 页码        
				$data = $API->search($s, $pages, $limit);        
				$this->echojson($data);
				break;        
			default:
				echo '<!doctype html><html><head><meta charset="utf-8"><title>信息</title><style>* {font-family: microsoft yahei}</style></head><body> <h2>MKOnlinePlayer</h2><h3>Github: https://github.com/mengkunsoft/MKOnlineMusicPlayer</h3><br>';
				if(!defined('DEBUG') || DEBUG !== true) {   // 非调试模式
					echo '<p>Api 调试模式已关闭</p>';
				} else {
					echo '<p><font color="red">您已开启 Api 调试功能，正常使用时请在 api.php 中关闭该选项！</font></p><br>';            
					echo '<p>PHP 版本：'.phpversion().' （本程序要求 PHP 5.4+）</p><br>';            
					echo '<p>服务器函数检查</p>';
					echo '<p>curl_exec: '.checkfunc('curl_exec',true).' （用于获取音乐数据）</p>';
					echo '<p>file_get_contents: '.checkfunc('file_get_contents',true).' （用于获取音乐数据）</p>';
					echo '<p>json_decode: '.checkfunc('json_decode',true).' （用于后台数据格式化）</p>';
					echo '<p>hex2bin: '.checkfunc('hex2bin',true).' （用于数据解析）</p>';
					echo '<p>openssl_encrypt: '.$this->checkfunc('openssl_encrypt',true).' （用于数据解析）</p>';
				}        
				echo '</body></html>';
		}
    }
	private function checkfunc($f,$m = false) {
		if (function_exists($f)) {
			return '<font color="green">可用</font>';
		} else {
			if ($m == false) {
				return '<font color="black">不支持</font>';
			} else {
				return '<font color="red">不支持</font>';
			}
		}
	}	
	private function getParam($key, $default='')
	{
		return trim($key && is_string($key) ? (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $default)) : $default);
	}
	private function echojson($data)    //json和jsonp通用
	{
		header('Content-type: application/json');
		$callback = getParam('callback');
		
		if(defined('HTTPS') && HTTPS === true && !defined('NO_HTTPS')) {    // 替换链接为 https
			$data = str_replace('http:\/\/', 'https:\/\/', $data);
			$data = str_replace('http://', 'https://', $data);
		}
		
		if($callback) //输出jsonp格式
		{
			die(htmlspecialchars($callback).'('.$data.')');
		} else {
			die($data);
		}
	}	
}  