<?php
namespace ext\Org;
//数据采集，doGET,doPOST,文件下载，
class Http
{
	static public $way=0;
	//手动设置访问方式
	static public function setWay($way)
	{
		self::$way=intval($way);
	}
	static public function getSupport()
	{	
		//如果指定访问方式，则按指定的方式去访问
		if(isset(self::$way)&&in_array(self::$way,array(1,2,3)))
			return self::$way;
			
		//自动获取最佳访问方式	
		if(function_exists('curl_init'))//curl方式
		{
			return 1;
		}
		else if(function_exists('fsockopen'))//socket
		{
			return 2;
		}
		else if(function_exists('file_get_contents'))//php系统函数file_get_contents
		{
			return 3;
		}
		else
		{
			return 0;
		}	
	}
		//通过get方式获取数据
	static public function doGet($url,$timeout=60,$header="")
	{	
		if(empty($url)||empty($timeout))
			return false;
		if(!preg_match('/^(http|https)/is',$url))
			$url="http://".$url;
		$code=self::getSupport();
		switch($code)
		{
			case 1:return self::curlGet($url,$timeout,$header);break;
			case 2:return self::socketGet($url,$timeout,$header);break;
			case 3:return self::phpGet($url,$timeout,$header);break;
			default:return false;	
		}
	}
	//通过POST方式发送数据
	static public function doPost($url, $post_data=array(), $timeout=60,$header="") 
	{
		if(empty($url)||empty($post_data)||empty($timeout))
			return false;
		if(!preg_match('/^(http|https)/is',$url))
			$url="http://".$url;
		$code=self::getSupport();
		switch($code)
		{
			case 1:return self::curlPost($url,$post_data,$timeout,$header);break;
			case 2:return self::socketPost($url,$post_data,$timeout,$header);break;
			case 3:return self::phpPost($url,$post_data,$timeout,$header);break;
			default:return false;	
		}
	}	
	function file_get_contents($url,$params=array()){
        $ip = empty($params["ip"]) ? self::rand_ip() : $params["ip"]; 
        $header = array('X-FORWARDED-FOR:'.$ip,'CLIENT-IP:'.$ip);
        if(isset($params["httpheader"])){
            $header = array_merge($header,$params["httpheader"]);
        }
        $referer = empty($params["ref"]) ? $url : $params["ref"];
        $user_agent = empty($params["ua"]) ? $_SERVER['HTTP_USER_AGENT'] : $params["ua"] ;
        $ch = curl_init();                                                      //初始化 curl
        curl_setopt($ch, CURLOPT_URL, $url);                                    //要访问网页 URL 地址
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);                          //伪装来源 IP 地址
        curl_setopt($ch, CURLOPT_REFERER, $referer);                            //伪装网页来源 URL
        curl_setopt($ch, CURLOPT_USERAGENT,$user_agent);                        //模拟用户浏览器信息
        curl_setopt($ch, CURLOPT_NOBODY, false);                                //设定是否输出页面内容
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                         //返回字符串，而非直接输出到屏幕上
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, false);                        //连接超时时间，设置为 0，则无限等待
        curl_setopt($ch, CURLOPT_TIMEOUT, 3600);                                //数据传输的最大允许时间超时,设为一小时
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);                       //HTTP验证方法
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);                        //不检查 SSL 证书来源
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);                        //不检查 证书中 SSL 加密算法是否存在
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);                         //跟踪爬取重定向页面
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);                            //当Location:重定向时，自动设置header中的Referer:信息
        curl_setopt($ch, CURLOPT_ENCODING, '');                                 //解决网页乱码问题
        curl_setopt($ch, CURLOPT_HEADER, empty($params["header"])?false:true);  //不返回 header 部分
        if(!empty($params["fields"])){
            curl_setopt($ch, CURLOPT_POST, true);                                  //设置为 POST 
            curl_setopt($ch, CURLOPT_POSTFIELDS,$params["fields"]);                //提交数据
        }
        if(!empty($params["cookie"])){
            curl_setopt($ch, CURLOPT_COOKIE, $params["cookie"]);                  //从字符串传参来提交cookies
        }
        if(!empty($params["proxy"])){
            curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);                  //代理认证模式
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);                  //使用http代理模式
            curl_setopt($ch, CURLOPT_PROXY, $params["proxy"]);                    //代理服务器地址 host:post的格式
            if(!empty($params["proxy_userpwd"])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $params["proxy_userpwd"]); //http代理认证帐号，username:password的格式
            }
        }
        $data = curl_exec($ch);                                                 //运行 curl，请求网页并返回结果
        curl_close($ch);                                                        //关闭 curl
        return $data;
	}
	public static function rand_ip(){
		$ip_long = array(
			array('607649792', '608174079'), //36.56.0.0-36.63.255.255
			array('1038614528', '1039007743'), //61.232.0.0-61.237.255.255
			array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255
			array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255
			array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255
			array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255
			array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255
			array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255
			array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255
			array('-569376768', '-564133889') //222.16.0.0-222.95.255.255
		);
		$rand_key = mt_rand(0, 9);
		$ip = long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
		return $ip;
	}
	//通过curl get数据
	static public function curlGet($url,$timeout=60,$header="")
	{
		$header=empty($header)?"":$header;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));//模拟的header头
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	
	//通过curl post数据
	static public function curlPost($url, $post_data=array(), $timeout=60,$header="") 
	{
		$header=empty($header)?"":$header;
		$post_string = http_build_query($post_data);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));//模拟的header头
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	//通过socket get数据
	static public function socketGet($url,$timeout=60,$header="")
	{
		$header=empty($header)?"":$header;
		$url2 = parse_url($url);
		$url2["path"] = isset($url2["path"])? $url2["path"]: "/" ;
		$url2["port"] = isset($url2["port"])? $url2["port"] : 80;
		$url2["query"] = isset($url2["query"])? "?".$url2["query"] : "";
		$host_ip = @gethostbyname($url2["host"]);

		if(($fsock = fsockopen($host_ip, $url2['port'], $errno, $errstr, $timeout)) < 0){
			return false;
		}
		$request =  $url2["path"] .$url2["query"];
		$in  = "GET " . $request . " HTTP/1.0\r\n";
		if(false===strpos($header, "Host:"))
		{	
			 $in .= "Host: " . $url2["host"] . "\r\n";
		}
		$in .= $header;
		$in .= "Connection: Close\r\n\r\n";
		
		if(!@fwrite($fsock, $in, strlen($in))){
			@fclose($fsock);
			return false;
		}
		return self::GetHttpContent($fsock);
	}
	//通过socket post数据
	static public function socketPost($url, $post_data=array(), $timeout=60,$header="")
	{
		$header=empty($header)?"":$header;
		$post_string = http_build_query($post_data);  
		
		
		$url2 = parse_url($url);
		$url2["path"] = ($url2["path"] == "" ? "/" : $url2["path"]);
		$url2["port"] = ($url2["port"] == "" ? 80 : $url2["port"]);
		$host_ip = @gethostbyname($url2["host"]);
		$fsock_timeout = $timeout; //超时时间
		if(($fsock = fsockopen($host_ip, $url2['port'], $errno, $errstr, $fsock_timeout)) < 0){
			return false;
		}
		$request =  $url2["path"].($url2["query"] ? "?" . $url2["query"] : "");
		$in  = "POST " . $request . " HTTP/1.0\r\n";
		$in .= "Host: " . $url2["host"] . "\r\n";
		$in .= $header;
		$in .= "Content-type: application/x-www-form-urlencoded\r\n";
		$in .= "Content-Length: " . strlen($post_string) . "\r\n";
		$in .= "Connection: Close\r\n\r\n";
		$in .= $post_string . "\r\n\r\n";
		unset($post_string);
		if(!@fwrite($fsock, $in, strlen($in))){
			@fclose($fsock);
			return false;
		}
		return self::GetHttpContent($fsock);
	}

	//通过file_get_contents函数get数据
	static public function phpGet($url,$timeout=10,$header="") 
	{
		$header=empty($header)?self::defaultHeader():$header;
		$opts = array( 
				'http'=>array(
							'protocol_version'=>'1.0', //http协议版本(若不指定php5.2系默认为http1.0)
							'method'=>"GET",//获取方式
							'timeout' => $timeout ,//超时时间
							'header'=> $header)
				  ); 
		$context = stream_context_create($opts);    
		return  @file_get_contents($url,false,$context);
	}
	//通过file_get_contents 函数post数据
	static public function phpPost($url, $post_data=array(), $timeout=10,$header="") 
	{
		$header=empty($header)?self::defaultHeader():$header;
		$post_string = http_build_query($post_data);  
		$header.="Content-length: ".strlen($post_string);
		$opts = array( 
				'http'=>array(
							'protocol_version'=>'1.0',//http协议版本(若不指定php5.2系默认为http1.0)
							'method'=>"POST",//获取方式
							'timeout' => $timeout ,//超时时间 
							'header'=> $header,  
							'content'=> $post_string)
				  ); 
		$context = stream_context_create($opts);    
		return  @file_get_contents($url,false,$context);
	}
	
	//默认模拟的header头
	static private function defaultHeader()
	{
		$header="User-Agent:Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12\r\n";
		$header.="Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n";
		$header.="Accept-language: zh-cn,zh;q=0.5\r\n";
		$header.="Accept-Charset: GB2312,utf-8;q=0.7,*;q=0.7\r\n";
		return $header;
	}
	//获取通过socket方式get和post页面的返回数据
	static private function GetHttpContent($fsock=null)
	{
		$out = null;
		while($buff = @fgets($fsock, 2048)){
			 $out .= $buff;
		}
		fclose($fsock);
		$pos = strpos($out, "\r\n\r\n");
		$head = substr($out, 0, $pos);    //http head
		$status = substr($head, 0, strpos($head, "\r\n"));    //http status line
		$body = substr($out, $pos + 4, strlen($out) - ($pos + 4));//page body
		if(preg_match("/^HTTP\/\d\.\d\s([\d]+)\s.*$/", $status, $matches))
		{
			if(intval($matches[1]) / 100 == 2)
			{
				return $body;  
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	/*
     功能： 下载文件
     参数:$filename 下载文件路径
     $showname 下载显示的文件名
     $expire  下载内容浏览器缓存时间
	*/
	static public function download($filename, $showname='',$expire=1800) 
		{
			if(file_exists($filename)&&is_file($filename)) 
			{
				$length = filesize($filename);
			}
			else 
			{
			  die('下载文件不存在！');
			}

			$type = mime_content_type($filename);

			//发送Http Header信息 开始下载
			header("Pragma: public");
			header("Cache-control: max-age=".$expire);
			//header('Cache-Control: no-store, no-cache, must-revalidate');
			header("Expires: " . gmdate("D, d M Y H:i:s",time()+$expire) . "GMT");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s",time()) . "GMT");
			header("Content-Disposition: attachment; filename=".$showname);
			header("Content-Length: ".$length);
			header("Content-type: ".$type);
			header('Content-Encoding: none');
			header("Content-Transfer-Encoding: binary" );
			readfile($filename);
			return true;
		}
    }

    if( !function_exists ('mime_content_type')) {
    /**
     +----------------------------------------------------------
     * 获取文件的mime_content类型
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function mime_content_type($filename)
    {
       static $contentType = array(
			'ai'	=> 'application/postscript',
				'aif'	=> 'audio/x-aiff',
				'aifc'	=> 'audio/x-aiff',
				'aiff'	=> 'audio/x-aiff',
				'asc'	=> 'application/pgp', //changed by skwashd - was text/plain
				'asf'	=> 'video/x-ms-asf',
				'asx'	=> 'video/x-ms-asf',
				'au'	=> 'audio/basic',
				'avi'	=> 'video/x-msvideo',
				'bcpio'	=> 'application/x-bcpio',
				'bin'	=> 'application/octet-stream',
				'bmp'	=> 'image/bmp',
				'c'	=> 'text/plain', // or 'text/x-csrc', //added by skwashd
				'cc'	=> 'text/plain', // or 'text/x-c++src', //added by skwashd
				'cs'	=> 'text/plain', //added by skwashd - for C# src
				'cpp'	=> 'text/x-c++src', //added by skwashd
				'cxx'	=> 'text/x-c++src', //added by skwashd
				'cdf'	=> 'application/x-netcdf',
				'class'	=> 'application/octet-stream',//secure but application/java-class is correct
				'com'	=> 'application/octet-stream',//added by skwashd
				'cpio'	=> 'application/x-cpio',
				'cpt'	=> 'application/mac-compactpro',
				'csh'	=> 'application/x-csh',
				'css'	=> 'text/css',
				'csv'	=> 'text/comma-separated-values',//added by skwashd
				'dcr'	=> 'application/x-director',
				'diff'	=> 'text/diff',
				'dir'	=> 'application/x-director',
				'dll'	=> 'application/octet-stream',
				'dms'	=> 'application/octet-stream',
				'doc'	=> 'application/msword',
				'dot'	=> 'application/msword',//added by skwashd
				'dvi'	=> 'application/x-dvi',
				'dxr'	=> 'application/x-director',
				'eps'	=> 'application/postscript',
				'etx'	=> 'text/x-setext',
				'exe'	=> 'application/octet-stream',
				'ez'	=> 'application/andrew-inset',
				'gif'	=> 'image/gif',
				'gtar'	=> 'application/x-gtar',
				'gz'	=> 'application/x-gzip',
				'h'	=> 'text/plain', // or 'text/x-chdr',//added by skwashd
				'h++'	=> 'text/plain', // or 'text/x-c++hdr', //added by skwashd
				'hh'	=> 'text/plain', // or 'text/x-c++hdr', //added by skwashd
				'hpp'	=> 'text/plain', // or 'text/x-c++hdr', //added by skwashd
				'hxx'	=> 'text/plain', // or 'text/x-c++hdr', //added by skwashd
				'hdf'	=> 'application/x-hdf',
				'hqx'	=> 'application/mac-binhex40',
				'htm'	=> 'text/html',
				'html'	=> 'text/html',
				'ice'	=> 'x-conference/x-cooltalk',
				'ics'	=> 'text/calendar',
				'ief'	=> 'image/ief',
				'ifb'	=> 'text/calendar',
				'iges'	=> 'model/iges',
				'igs'	=> 'model/iges',
				'jar'	=> 'application/x-jar', //added by skwashd - alternative mime type
				'java'	=> 'text/x-java-source', //added by skwashd
				'jpe'	=> 'image/jpeg',
				'jpeg'	=> 'image/jpeg',
				'jpg'	=> 'image/jpeg',
				'js'	=> 'application/x-javascript',
				'kar'	=> 'audio/midi',
				'latex'	=> 'application/x-latex',
				'lha'	=> 'application/octet-stream',
				'log'	=> 'text/plain',
				'lzh'	=> 'application/octet-stream',
				'm3u'	=> 'audio/x-mpegurl',
				'man'	=> 'application/x-troff-man',
				'me'	=> 'application/x-troff-me',
				'mesh'	=> 'model/mesh',
				'mid'	=> 'audio/midi',
				'midi'	=> 'audio/midi',
				'mif'	=> 'application/vnd.mif',
				'mov'	=> 'video/quicktime',
				'movie'	=> 'video/x-sgi-movie',
				'mp2'	=> 'audio/mpeg',
				'mp3'	=> 'audio/mpeg',
				'mpe'	=> 'video/mpeg',
				'mpeg'	=> 'video/mpeg',
				'mpg'	=> 'video/mpeg',
				'mpga'	=> 'audio/mpeg',
				'ms'	=> 'application/x-troff-ms',
				'msh'	=> 'model/mesh',
				'mxu'	=> 'video/vnd.mpegurl',
				'nc'	=> 'application/x-netcdf',
				'oda'	=> 'application/oda',
				'patch'	=> 'text/diff',
				'pbm'	=> 'image/x-portable-bitmap',
				'pdb'	=> 'chemical/x-pdb',
				'pdf'	=> 'application/pdf',
				'pgm'	=> 'image/x-portable-graymap',
				'pgn'	=> 'application/x-chess-pgn',
				'pgp'	=> 'application/pgp',//added by skwashd
				'php'	=> 'application/x-httpd-php',
				'php3'	=> 'application/x-httpd-php3',
				'pl'	=> 'application/x-perl',
				'pm'	=> 'application/x-perl',
				'png'	=> 'image/png',
				'pnm'	=> 'image/x-portable-anymap',
				'po'	=> 'text/plain',
				'ppm'	=> 'image/x-portable-pixmap',
				'ppt'	=> 'application/vnd.ms-powerpoint',
				'ps'	=> 'application/postscript',
				'qt'	=> 'video/quicktime',
				'ra'	=> 'audio/x-realaudio',
				'rar'=>'application/octet-stream',
				'ram'	=> 'audio/x-pn-realaudio',
				'ras'	=> 'image/x-cmu-raster',
				'rgb'	=> 'image/x-rgb',
				'rm'	=> 'audio/x-pn-realaudio',
				'roff'	=> 'application/x-troff',
				'rpm'	=> 'audio/x-pn-realaudio-plugin',
				'rtf'	=> 'text/rtf',
				'rtx'	=> 'text/richtext',
				'sgm'	=> 'text/sgml',
				'sgml'	=> 'text/sgml',
				'sh'	=> 'application/x-sh',
				'shar'	=> 'application/x-shar',
				'shtml'	=> 'text/html',
				'silo'	=> 'model/mesh',
				'sit'	=> 'application/x-stuffit',
				'skd'	=> 'application/x-koan',
				'skm'	=> 'application/x-koan',
				'skp'	=> 'application/x-koan',
				'skt'	=> 'application/x-koan',
				'smi'	=> 'application/smil',
				'smil'	=> 'application/smil',
				'snd'	=> 'audio/basic',
				'so'	=> 'application/octet-stream',
				'spl'	=> 'application/x-futuresplash',
				'src'	=> 'application/x-wais-source',
				'stc'	=> 'application/vnd.sun.xml.calc.template',
				'std'	=> 'application/vnd.sun.xml.draw.template',
				'sti'	=> 'application/vnd.sun.xml.impress.template',
				'stw'	=> 'application/vnd.sun.xml.writer.template',
				'sv4cpio'	=> 'application/x-sv4cpio',
				'sv4crc'	=> 'application/x-sv4crc',
				'swf'	=> 'application/x-shockwave-flash',
				'sxc'	=> 'application/vnd.sun.xml.calc',
				'sxd'	=> 'application/vnd.sun.xml.draw',
				'sxg'	=> 'application/vnd.sun.xml.writer.global',
				'sxi'	=> 'application/vnd.sun.xml.impress',
				'sxm'	=> 'application/vnd.sun.xml.math',
				'sxw'	=> 'application/vnd.sun.xml.writer',
				't'	=> 'application/x-troff',
				'tar'	=> 'application/x-tar',
				'tcl'	=> 'application/x-tcl',
				'tex'	=> 'application/x-tex',
				'texi'	=> 'application/x-texinfo',
				'texinfo'	=> 'application/x-texinfo',
				'tgz'	=> 'application/x-gtar',
				'tif'	=> 'image/tiff',
				'tiff'	=> 'image/tiff',
				'tr'	=> 'application/x-troff',
				'tsv'	=> 'text/tab-separated-values',
				'txt'	=> 'text/plain',
				'ustar'	=> 'application/x-ustar',
				'vbs'	=> 'text/plain', //added by skwashd - for obvious reasons
				'vcd'	=> 'application/x-cdlink',
				'vcf'	=> 'text/x-vcard',
				'vcs'	=> 'text/calendar',
				'vfb'	=> 'text/calendar',
				'vrml'	=> 'model/vrml',
				'vsd'	=> 'application/vnd.visio',
				'wav'	=> 'audio/x-wav',
				'wax'	=> 'audio/x-ms-wax',
				'wbmp'	=> 'image/vnd.wap.wbmp',
				'wbxml'	=> 'application/vnd.wap.wbxml',
				'wm'	=> 'video/x-ms-wm',
				'wma'	=> 'audio/x-ms-wma',
				'wmd'	=> 'application/x-ms-wmd',
				'wml'	=> 'text/vnd.wap.wml',
				'wmlc'	=> 'application/vnd.wap.wmlc',
				'wmls'	=> 'text/vnd.wap.wmlscript',
				'wmlsc'	=> 'application/vnd.wap.wmlscriptc',
				'wmv'	=> 'video/x-ms-wmv',
				'wmx'	=> 'video/x-ms-wmx',
				'wmz'	=> 'application/x-ms-wmz',
				'wrl'	=> 'model/vrml',
				'wvx'	=> 'video/x-ms-wvx',
				'xbm'	=> 'image/x-xbitmap',
				'xht'	=> 'application/xhtml+xml',
				'xhtml'	=> 'application/xhtml+xml',
				'xls'	=> 'application/vnd.ms-excel',
				'xlt'	=> 'application/vnd.ms-excel',
				'xml'	=> 'application/xml',
				'xpm'	=> 'image/x-xpixmap',
				'xsl'	=> 'text/xml',
				'xwd'	=> 'image/x-xwindowdump',
				'xyz'	=> 'chemical/x-xyz',
				'z'	=> 'application/x-compress',
				'zip'	=> 'application/zip',
        );
        $type = strtolower(substr(strrchr($filename, '.'),1));
        if(isset($contentType[$type])) {
            $mime = $contentType[$type];
        }else {
       	    $mime = 'application/octet-stream';
        }
        return $mime;
    }
}
