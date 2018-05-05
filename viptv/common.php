<?php
    error_reporting(E_ALL ^ E_NOTICE); 
    use think\Db;
	const ADDON_PATH = './Addons/';
	function is_referer(){
		global $wap;
		//没有设置防盗链
		if(REFERER_URL=='') return true; 
		//部分手机浏览器没有来路
		if(empty($_SERVER['HTTP_REFERER']) && $wap==1){
			return true;
		}else{
			//开始验证
			$ext = explode("|",REFERER_URL);
			for($i=0;$i<count($ext);$i++){
				if(strpos(strtolower($_SERVER['HTTP_REFERER']),strtolower($ext[$i])) !== FALSE){
				   return true; 
				}
			}
		}
		return false;
	}	
    function ext($file){    
       $info = pathinfo($file);
       return $info['extension'];
    }
    //数组转XML
    function ckp_xml($str,$param){
        global $hd;
        $param = str_replace('&','&amp;',$param);
        $xml='<ckplayer><!-- CKmov视频解析组件,QQ群:572443024 --><flashvars>{lv->0}{v->80}{e->0}{p->1}{q->start}{h->3}{f->'.YOU_URL.'?'.$param.'&amp;[$pat]}{a->hd='.$hd.'}{defa->hd=1|hd=2|hd=3|hd=4}{deft->标清|高清|超清|原画}</flashvars>
        <video>';
        $arr = $str['url'];
        if(is_array($arr)){
                 for($i=0;$i<count($arr);$i++){
                     $xml.='<file><![CDATA['.$arr[$i]['purl'].']]></file>';
                     if(isset($arr[$i]['size'])) $xml.='<size>'.$arr[$i]['size'].'</size>';
                     if(isset($arr[$i]['sec'])) $xml.='<seconds>'.$arr[$i]['sec'].'</seconds>';     
                 }
        }else{
                 $xml.='<file><![CDATA['.$str['url'].']]></file>';
                 if(isset($str['size'])) $xml.='<size>'.$str['size'].'</size>';
                 if(isset($str['sec'])) $xml.='<seconds>'.$str['sec'].'</seconds>';  
        }
        $xml.='</video></ckplayer>';
        return $xml;
    }

	function is_mobile(){  
		$_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';  
		$mobile_browser = '0';  
		if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))  
			$mobile_browser++;  
		if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false))  
			$mobile_browser++;  
		if(isset($_SERVER['HTTP_X_WAP_PROFILE']))  
			$mobile_browser++;  
		if(isset($_SERVER['HTTP_PROFILE']))  
			$mobile_browser++;  
			$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));  
			$mobile_agents = array(  
			'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',  
			'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',  
			'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',  
			'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',  
			'newt','noki','oper','palm','pana','pant','phil','play','port','prox',  
			'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',  
			'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',  
			'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',  
			'wapr','webc','winw','winw','xda','xda-'
			);  
		if(in_array($mobile_ua, $mobile_agents))  
			$mobile_browser++;  
		if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)  
			$mobile_browser++;  
		// Pre-final check to reset everything if the user is on Windows  
		if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)  
			$mobile_browser=0;  
		// But WP7 is also Windows, with a slightly different characteristic  
		if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)  
			$mobile_browser++;  
		if($mobile_browser>0)  
			return true;  
		else
			return false;
	}


    /**
     * 邮件发送函数
     * @param string to      要发送的邮箱地址
     * @param string subject 邮件标题
     * @param string content 邮件内容
     * @return array
     */
    function SendMail($to, $subject, $content) {
        require_cache(VENDOR_PATH."PHPMailer/class.smtp.php");
        require_cache(VENDOR_PATH."PHPMailer/class.phpmailer.php");
        $mail = new PHPMailer();
        // 装配邮件服务器
        $mail->IsSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = $GLOBALS['CONFIG']['mailSmtp'];
        $mail->SMTPAuth = $GLOBALS['CONFIG']['mailAuth'];
        $mail->Username = $GLOBALS['CONFIG']['mailUserName'];
        $mail->Password = $GLOBALS['CONFIG']['mailPassword'];
        $mail->CharSet = 'utf-8';
        // 装配邮件头信息
        $mail->From = $GLOBALS['CONFIG']['mailAddress'];
        $mail->AddAddress($to);
        $mail->FromName = $GLOBALS['CONFIG']['mailSendTitle'];
        $mail->IsHTML(true);
        // 装配邮件正文信息
        $mail->Subject = $subject;
        $mail->Body = $content;
        // 发送邮件
        $rs =array();
        if (!$mail->Send()) {
            $rs['status'] = 0;
            $rs['msg'] = $mail->ErrorInfo;
            return $rs;
        } else {
            $rs['status'] = 1;
            return $rs;
        }
    }
    /**
     * 发送短信
     * 此接口要根据不同的短信服务商去写，这里只是一个参考
     * @param string $phoneNumer  手机号码
     * @param string $content     短信内容
     */
    function SendSMS2($phoneNumer,$content){
        $url = 'http://223.4.21.214:8180/service.asmx/SendMessage?Id='.$GLOBALS['CONFIG']['smsOrg']."&Name=".$GLOBALS['CONFIG']['smsKey']."&Psw=".$GLOBALS['CONFIG']['smsPass']."&Timestamp=0&Message=".$content."&Phone=".$phoneNumer;
        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置否输出到页面
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30 ); //设置连接等待时间
        curl_setopt($ch, CURLOPT_ENCODING, "gzip" );
        $data=curl_exec($ch);
        curl_close($ch);
        return "$data";
    }
    /**
     * @param unknown_type $phoneNumer
     * @param unknown_type $content
     */
    function SendSMS($phoneNumer,$content){
        $url = 'http://utf8.sms.webchinese.cn/?Uid='.$GLOBALS['CONFIG']['smsKey'].'&Key='.$GLOBALS['CONFIG']['smsPass'].'&smsMob='.$phoneNumer.'&smsText='.$content;
        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置否输出到页面
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30 ); //设置连接等待时间
        curl_setopt($ch, CURLOPT_ENCODING, "gzip" );
        $data=curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    /**
     * 字符串替换
     * @param string $str     要替换的字符串
     * @param string $repStr  即将被替换的字符串
     * @param int $start      要替换的起始位置,从0开始
     * @param string $splilt  遇到这个指定的字符串就停止替换
     */
    function StrReplace($str,$repStr,$start,$splilt = ''){
        $neVIPr = substr($str,0,$start);
        $breakNum = -1;
        for ($i=$start;$i<strlen($str);$i++){
            $char = substr($str,$i,1);
            if($char==$splilt){
                $breakNum = $i;
                break;
            }
            $neVIPr.=$repStr;
        }
        if($splilt!='' && $breakNum>-1){
            for ($i=$breakNum;$i<strlen($str);$i++){
                $char = substr($str,$i,1);
                $neVIPr.=$char;
            }
        }
        return $neVIPr;
    }
    /**
     * 循环删除指定目录下的文件及文件夹
     * @param string $dirpath 文件夹路径
     */
    function DelDir($dirpath){
        $dh=opendir($dirpath);
        while (($file=readdir($dh))!==false) {
            if($file!="." && $file!="..") {
                $fullpath=$dirpath."/".$file;
                if(!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    VIPDelDir($fullpath);
                    rmdir($fullpath);
                }
            }
        }    
        closedir($dh);
        $isEmpty = 1;
        $dh=opendir($dirpath);
        while (($file=readdir($dh))!== false) {
            if($file!="." && $file!="..") {
                $isEmpty = 0;
                break;
            }
        }
        return $isEmpty;
    }
    /**
     * 获取网站域名
     */
    function Domain(){
        $server = $_SERVER['HTTP_HOST'];
        $http = is_ssl()?'https://':'http://';
        return $http.$server.__ROOT__;
    }
    /**
     * 获取系统根目录
     */
    function RootPath(){
        return dirname(dirname(dirname(dirname(__File__))));
    }
    /**
     * 获取网站根域名
     */
    function VIPRootDomain(){
        $server = $_SERVER['HTTP_HOST'];
        $http = is_ssl()?'https://':'http://';
        return $http.$server;
    }
    /**
     * 设置当前页面对象
     * @param int 0-用户  1-商家
     */
    function VIPLoginTarget($target = 0){
        $VIP_USER = session('VIP_USER');
        $VIP_USER['loginTarget'] = $target;
        session('VIP_USER',$VIP_USER);
    }

    /**
     * 生成缓存文件
     */
    function DataFile($name, $path = '',$data=array()){
        $key = C('DATA_CACHE_KEY');
        $name = md5($key.$name);
        if(is_array($data) && !empty($data)){
            if($data['mallLicense']==''){
                if(stripos($data['mallTitle'],'Powered By VIPMall')===false)$data['mallTitle'] = $data['mallTitle']." - Powered By VIPMall";
            }
            $data   =   serialize($data);
            if( C('DATA_CACHE_COMPRESS') && function_exists('gzcompress')) {
                //数据压缩
                $data   =   gzcompress($data,3);
            }
            if(C('DATA_CACHE_CHECK')) {//开启数据校验
                $check  =  md5($data);
            }else {
                $check  =  '';
            }
            $data    = "<?php\n//".sprintf('%012d',$expire).$check.$data."\n?>";
            $result  =   file_put_contents(DATA_PATH.$path.$name.".php",$data);
            clearstatcache();
        }else if(is_null($data)){
            unlink(DATA_PATH.$path.$name.".php");
        }else{
            if(file_exists(DATA_PATH.$path.$name.'.php')){
                $content    =   file_get_contents(DATA_PATH.$path.$name.'.php');
                if( false !== $content) {
                    $expire  =  (int)substr($content,8, 12);
                    if(C('DATA_CACHE_CHECK')) {//开启数据校验
                        $check  =  substr($content,20, 32);
                        $content   =  substr($content,52, -3);
                        if($check != md5($content)) {//校验错误
                            return null;
                        }
                    }else {
                        $content   =  substr($content,20, -3);
                    }
                    if(C('DATA_CACHE_COMPRESS') && function_exists('gzcompress')) {
                        //启用数据压缩
                        $content   =   gzuncompress($content);
                    }
                    $content    =   unserialize($content);
                    return $content;
                }
            }
            return null;
        }
    }
    /**
     * 建立文件夹
     * @param string $aimUrl
     * @return viod
     */
    function CreateDir($aimUrl) {
        $aimUrl = str_replace('', '/', $aimUrl);
        $aimDir = '';
        $arr = explode('/', $aimUrl);
        $result = true;
        foreach ($arr as $str) {
            $aimDir .= $str . '/';
            if (!file_exists_case($aimDir)) {
                $result = mkdir($aimDir,0777);
            }
        }
        return $result;
    }

    /**
     * 建立文件
     * @param string $aimUrl
     * @param boolean $overWrite 该参数控制是否覆盖原文件
     * @return boolean
     */
    function CreateFile($aimUrl, $overWrite = false) {
        if (file_exists_case($aimUrl) && $overWrite == false) {
            return false;
        } elseif (file_exists_case($aimUrl) && $overWrite == true) {
            VIPUnlinkFile($aimUrl);
        }
        $aimDir = dirname($aimUrl);
        CreateDir($aimDir);
        touch($aimUrl);
        return true;
    }

    /**
     * 删除文件
     * @param string $aimUrl
     * @return boolean
     */
    function UnlinkFile($aimUrl) {
        if (file_exists_case($aimUrl)) {
            unlink($aimUrl);
            return true;
        } else {
            return false;
        }
    }


    function ReadExcel($file){
        Vendor("PHPExcel.PHPExcel");
        Vendor("PHPExcel.PHPExcel.IOFactory");
        return PHPExcel_IOFactory::load(VIPRootPath()."/Upload/".$file);
    }
    /**
     * 检测字符串不否包含
     * @param $srcword 被检测的字符串
     * @param $filterWords 禁用使用的字符串列表
     * @return boolean true-检测到,false-未检测到
     */
    function  CheckFilterWords($srcword,$filterWords){
        $flag = true;
        $filterWords = str_replace("，",",",$filterWords);
        $words = explode(",",$filterWords);
        for($i=0;$i<count($words);$i++){
            if(strpos($srcword,$words[$i]) !== false){
                $flag = false;
                break;
            }
        }
        return $flag;
    }

    /**
     * 比较两个日期相差的天数
     * @param $date1 开始日期  Y-m-d
     * @param $date2 结束日期  Y-m-d
     */
    function CompareDate($date1,$date2){
        $time1 = strtotime($date1);
        $time2 = strtotime($date2);
        return ceil(($time1-$time2)/86400);
    }
    /**
     * 截取字符串
     */
    function MSubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
        $neVIPr = '';
        if (function_exists ( "mb_substr" )) {
            if ($suffix)
                $neVIPr = mb_substr ( $str, $start, $length, $charset );
            else
                $neVIPr = mb_substr ( $str, $start, $length, $charset );
        } elseif (function_exists ( 'iconv_substr' )) {
            if ($suffix)
                $neVIPr = iconv_substr ( $str, $start, $length, $charset );
            else
                $neVIPr = iconv_substr ( $str, $start, $length, $charset );
        }
        if($neVIPr==''){
        $re ['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re ['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re ['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re ['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all ( $re [$charset], $str, $match );
        $slice = join ( "", array_slice ( $match [0], $start, $length ) );
        if ($suffix)
            $neVIPr = $slice;
        }
        return (strlen($str)>strlen($neVIPr))?$neVIPr."...":$neVIPr;
    }
    /*-------------------------------------------------文件夹与文件操作开始------------------------------------------------------------------*/
    /**
     * 写入配置文件
     * @param  array $config 配置信息
     */
    function write_config($config){
        if(is_array($config)){
            //读取配置内容
            $conf = file_get_contents(MODULE_PATH . 'Data/conf.tpl');
            //替换配置项
            foreach ($config as $name => $value) {
                $conf = str_replace("[{$name}]", $value, $conf);
            }
            //写入应用配置文件
            if(!IS_WRITE){
                return '由于您的环境不可写，请复制下面的配置文件内容覆盖到相关的配置文件，然后再登录后台。<p>'.realpath(APP_PATH).'/Common/Conf/config.php</p>
                <textarea name="" style="width:650px;height:185px">'.$conf.'</textarea>';
            }else{
                if(file_put_contents(APP_PATH . 'Common/Conf/config.php', $conf)){
                    show_msg('配置文件写入成功');
                } else {
                    show_msg('配置文件写入失败！', 'error');
                    session('error', true);
                }
                return '';
            }

        }
    }
    /**
     * 及时显示提示信息
     * @param  string $msg 提示信息
     */
    function show_msg($msg, $class = ''){
        echo "<script type=\"text/javascript\">showmsg(\"{$msg}\", \"{$class}\")</script>";
        flush();
        ob_flush();
    }
    /**
     * get_ip_lookup  获取ip地址所在的区域
     * @param null $ip
     * @return bool|mixed
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    function get_ip_lookup($ip=null){
        if(empty($ip)){
            $ip = get_client_ip(0);
        }
        $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);
        if(empty($res)){ return false; }
        $jsonMatches = array();
        preg_match('#\{.+?\}#', $res, $jsonMatches);
        if(!isset($jsonMatches[0])){ return false; }
        $json = json_decode($jsonMatches[0], true);
        if(isset($json['ret']) && $json['ret'] == 1){
            $json['ip'] = $ip;
            unset($json['ret']);
        }else{
            return false;
        }
        return $json;
    }
    function is_sae()
    {
        return function_exists('sae_debug');
    }
    /*-------------------------------------------------加密解密函数开始------------------------------------------------------------------*/
    /**
     * 系统加密方法
     * @param string $data 要加密的字符串
     * @param string $key  加密密钥
     * @param int $expire  过期时间 单位 秒
     * @return string
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    function think_encrypt($data, $key = '', $expire = 0) {
        $key  = md5(empty($key) ? Config('DATA_AUTH_KEY') : $key);
        $data = base64_encode($data);
        $x    = 0;
        $len  = strlen($data);
        $l    = strlen($key);
        $char = '';

        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) $x = 0;
            $char .= substr($key, $x, 1);
            $x++;
        }

        $str = sprintf('%010d', $expire ? $expire + time():0);

        for ($i = 0; $i < $len; $i++) {
            $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
        }
        return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
    }

    /**
     * 系统解密方法
     * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
     * @param  string $key  加密密钥
     * @return string
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    function think_decrypt($data, $key = ''){
        $key    = md5(empty($key) ? Config('DATA_AUTH_KEY') : $key);
        $data   = str_replace(array('-','_'),array('+','/'),$data);
        $mod4   = strlen($data) % 4;
        if ($mod4) {
           $data .= substr('====', $mod4);
        }
        $data   = base64_decode($data);
        $expire = substr($data,0,10);
        $data   = substr($data,10);

        if($expire > 0 && $expire < time()) {
            return '';
        }
        $x      = 0;
        $len    = strlen($data);
        $l      = strlen($key);
        $char   = $str = '';

        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) $x = 0;
            $char .= substr($key, $x, 1);
            $x++;
        }

        for ($i = 0; $i < $len; $i++) {
            if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
                $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
            }else{
                $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
            }
        }
        return base64_decode($str);
    }
    // 具有时效性的php加密解密函数代码
    // 加密:$psa=encode_pass("woshi ceshi yong de ","taintainxousad","encode",120);
    // 解密:encode_pass($psa,"taintainxousad",'decode',120);
    function encode_pass($tex,$key,$type="encode",$expiry=0){
        $chrArr=array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','0','1','2','3','4','5','6','7','8','9');
        if($type=="decode"){
            if(strlen($tex)<14)return false;
            $verity_str=substr($tex, 0,8);
            $tex=substr($tex, 8);
            if($verity_str!=substr(md5($tex),0,8)){
                //完整性验证失败
                return false;
            }    
        }
        $key_b=$type=="decode"?substr($tex,0,6):$chrArr[rand()%62].$chrArr[rand()%62].$chrArr[rand()%62].$chrArr[rand()%62].$chrArr[rand()%62].$chrArr[rand()%62];
        $rand_key=$key_b.$key; 
        $modnum=0;$modCount=0;$modCountStr="";
        if($expiry>0){
            if($type=="decode"){
                $modCountStr=substr($tex,6,1);
                $modCount=$modCountStr=="a"?10:floor($modCountStr);
                $modnum=substr($tex,7,$modCount);
                $rand_key=$rand_key.(floor((time()-$modnum)/$expiry));
            }else{
                $modnum=time()%$expiry;
                $modCount=strlen($modnum);
                $modCountStr=$modCount==10?"a":$modCount;

                $rand_key=$rand_key.(floor(time()/$expiry));            
            }
            $tex=$type=="decode"?base64_decode(substr($tex, (7+$modCount))):"qq2236639958".$tex;
        }else{
            $tex=$type=="decode"?base64_decode(substr($tex, 6)):"qq2236639958".$tex;
        }
        $rand_key=md5($rand_key);
        $texlen=strlen($tex);
        $reslutstr="";
        for($i=0;$i<$texlen;$i++){
            $reslutstr.=$tex{$i}^$rand_key{$i%32};
        }
        if($type!="decode"){
            $reslutstr=trim(base64_encode($reslutstr),"==");
            $reslutstr=$modCount?$modCountStr.$modnum.$reslutstr:$reslutstr;
            $reslutstr=$key_b.$reslutstr;
            $reslutstr=substr(md5($reslutstr), 0,8).$reslutstr;
        }else{
            if(substr($reslutstr,0, 5)!="xugui"){
                return false;
            }
            $reslutstr=substr($reslutstr, 5);
        }
        return $reslutstr;
    }
    //Discuz!经典代码authcode加密函数
    //用法：$str = 'abcdef'; $key = 'www.helloweba.com';
    //加密  $sts = authcode($str,'ENCODE',$key,0);  
    //解密  $stt = authcode($sts,'DECODE',$key,0);
    function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) { 
        $ckey_length = 4;  
        $key = md5($key ? $key : $GLOBALS['discuz_auth_key']);
        $keya = md5(substr($key, 0, 16)); 
        $keyb = md5(substr($key, 16, 16)); 
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): 
    substr(md5(microtime()), -$ckey_length)) : '';
        $cryptkey = $keya.md5($keya.$keyc);   
        $key_length = strlen($cryptkey);  
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) :  
    sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;   
        $string_length = strlen($string);   
        $result = '';   
        $box = range(0, 255);   
        $rndkey = array(); 
        for($i = 0; $i <= 255; $i++) {   
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);   
        }
        for($j = $i = 0; $i < 256; $i++) {   
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;   
            $tmp = $box[$i];   
            $box[$i] = $box[$j];   
            $box[$j] = $tmp;   
        } 
        for($a = $j = $i = 0; $i < $string_length; $i++) {   
            $a = ($a + 1) % 256;   
            $j = ($j + $box[$a]) % 256;   
            $tmp = $box[$a];   
            $box[$a] = $box[$j];   
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));   
        }   
        if($operation == 'DECODE') { 
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&  
    substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {   
                return substr($result, 26);   
            } else {   
                return '';   
            }   
        } else { 
            return $keyc.str_replace('=', '', base64_encode($result));   
        }   
    }
    /**echo jiami('air','iippcc','12');**/
    function jiami($path,$key='',$overtime=''){
        $ctime=time()+$overtime; // 授权十分钟后过期
        $sign=substr(md5($path.'&'.$key.'&'.$ctime),23,8).$ctime;
        return $sign;
    }
    /**echo jiemi('519f1b781433058076','air','iippcc','12');;**/
    function jiemi($str,$path,$key='',$overtime=''){
        $md5=substr($str,0,8);
        $ctime=substr($str, 8,10);
        $sign=substr(md5($path.'&'.$key.'&'.$ctime),23,8);
        $z=(((time()-$ctime)<$overtime)&&($sign==$md5))?1:0;
        return $z;
    }
    function getadsurl($str,$charset="utf-8"){
        return '<script type="text/javascript" src="'.C('site_path').C('admin_ads_file').'/'.$str.'.js" charset="'.$charset.'"></script>';
    }
    /*---------------------------------------ThinkPhp扩展函数库开始------------------------------------------------------------------
     * @category   Think
     * @package  Common
     * @author   liu21st <liu21st@gmail.com>*/
    // 获取客户端IP地址
    function get_client_ip(){
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
           $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
           $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
           $ip = getenv("REMOTE_ADDR");
        else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
           $ip = $_SERVER['REMOTE_ADDR'];
        else
           $ip = "unknown";
        return($ip);
    }
    /*-------------------------------------------------字符串处理开始------------------------------------------------------------------*/
    // UT*转GBK
    function u2g($str){
        return iconv("UTF-8","GBK",$str);
    }
    // GBK转UTF8
    function g2u($str){
        return iconv("GBK","UTF-8//ignore",$str);
    }
    // 转换成JS
    function t2js($l1, $l2=1){
        $I1 = str_replace(array("\r", "\n"), array('', '\n'), addslashes($l1));
        return $l2 ? "document.write(\"$I1\");" : $I1;
    }
    // 去掉换行
    function nr($str){
        $str = str_replace(array("<nr/>","<rr/>"),array("\n","\r"),$str);
        return trim($str);
    }
    //去掉连续空白
    function nb($str){
        $str = str_replace(" ",' ',str_replace("&nbsp;",' ',$str));
        $str = ereg_replace("[\r\n\t ]{1,}",' ',$str);
        return trim($str);
    }
    /*-------------------------------------------------采集函数开始------------------------------------------------------------------*/
    // 采集-匹配规则结果
    function tv_preg_match($rule,$html){
        $arr = explode('$$$',$rule);
        if(count($arr) == 2){
            preg_match('/'.$arr[1].'/', $html, $data);
            return $data[$arr[0]].'';
        }else{
            preg_match('/'.$rule.'/', $html, $data);
            return $data[1].'';
        }
    }
    // 采集-匹配规则结果all
    function tv_preg_match_all($rule,$html){
        $arr = explode('$$$',$rule);
        if(count($arr) == 2){
            preg_match_all('/'.$arr[1].'/', $html, $data);
            return $data[$arr[0]];
        }else{
            preg_match_all('/'.$rule.'/', $html, $data);
            return $data[1];
        }
    }
