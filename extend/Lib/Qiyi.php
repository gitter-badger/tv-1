<?php

namespace ext\lib;

class Qiyi
{
    /**
     * [parse 解析网页获取视频ID]
     * @param  [type] $url  [description]
     * @return [type]       [description]
     */
    public static function parse($url)
    {
        $html = self::curl($url);

        if($html){
            preg_match('#vid":"(.*)",#iU',$html,$vids);
            preg_match('#tvId":(.*),"#iU',$html,$tvids);
            preg_match('#param\[\'isMember\'\]\s*=\s*"(.*)";#',$html,$bool);
            preg_match('#tvName:"(.*)"#iU',$html,$tvName);
            preg_match('#property="og:image"\s*content="(.*)"#',$html,$images);
            if (!$vids[1]) {
                preg_match('#data-player-videoid="(.*)"#iU',$html,$vids);
            }
            if (!$tvids[1]) {
                preg_match('#data-player-tvid="(.*)"#iU',$html,$tvids);
            }
            if (!$tvName[1]) {
                preg_match('#name="irTitle"\s*content="(.*)"#',$html,$tvName);
            }
            $ids['uid'] = empty($_uid[1])?"":$_uid[1];                          //用户需从cookie里P00010获取,请自行修改
            $ids['qyid'] = empty($_qyid[1])? "d56mj2ujli317a4mvdxq5i73":$_qyid[1];//用户需从cookie里QC006获取,请自行修改
            $ids['agenttype'] = 13;
            $ids['type'] = 'mp4';
            $time = number_format(microtime(true),3,'','');
            $ids['sgti'] = "{$ids['agenttype']}_{$ids['qyid']}_{$time}";

            $ids['title'] = $tvName[1];
            $ids['member'] = $bool[1];//是否为付费视频
            $ids['vid'] = $vids[1];
            $ids['tvid'] = $tvids[1];
            $ids['image'] = isset($images[1]) ? $images[1] : '';
            $ids['src'] = '02020031010000000000';
            $ids['url'] = $url;
            $ids['ref'] = $url;

            $api = 'https://mixer.video.iqiyi.com/jp/mixin/videos/'.$ids['tvid'];
            $html = self::curl($api,$ids);
            $html = str_replace('var tvInfoJs=','',$html);
            $json = json_decode($html,true);

            $ids['title'] = isset($json['name']) ? $json['name'] : $ids['title'];
            $ids['des'] = isset($json['des']) ? $json['des'] : $ids['des'];
            $ids['poster'] = isset($json['imageUrl']) ? str_replace(".jpg", "_480_270.jpg",$json['imageUrl']) : str_replace(".jpg", "_480_270.jpg",$ids['image']) ; 

            //print_r($ids);exit;

            //
            //
            //
            $videoinfo = self::get_video($ids);
            return json_encode($videoinfo);
        }

    }
    /**
     * [get_tmts_video 解析视频地址]
     * @param  [type] $tvid [description]
     * @param  [type] $vid  [description]
     * @return [type]       [description]
     */
    public static function get_video($ids)
    {
        if($ids['member']==='true'){//付费视频
            $url = './yun/?url='.$ids['url'];
            $videoinfo['code'] = 200;
            $videoinfo['play'] = 'iframe';
            //$videoinfo['msg'] = $ids['title'] . '为爱奇异VIP资源！VIP资源全网搜索中....';
            $videoinfo['data']['url'] = $url; 
            return $videoinfo;exit;
        }else{
            $api = self::get_tmts_data($ids);
            $ids['cookie'] = 'P00001=';
            $content = self::curl($api,$ids);
        }

        $body = preg_replace("#var tvInfoJs=#","",$content);

        $json = json_decode($body,true);
        //print_r($json);exit;

        if ($json['code']=='A00000' && isset($json['data'])){
            $data = $json['data'];
            $vidl = $data['vidl'];              //视频地址列表
                $datainfo['code'] = 200;
                $datainfo['play'] = 'ajax';
                $datainfo['type'] = 'iqiyi';
                $datainfo['msg'] = '';
                $datainfo['data']["vid"] = $ids['vid'];
                $datainfo['data']["tvid"] = $ids['tvid'];
                $datainfo['data']["uid"] = $ids['uid'];
                $datainfo['data']["qyid"] = $ids['qyid'];
                $datainfo['data']["type"] = $ids['type'];
                $datainfo['data']["src"] = $ids['src'];
                $datainfo['data']["agenttype"] = $ids['agenttype'];
                $datainfo['data']["jsdir"] = GlobalBase::https_url().'/static';
                $datainfo['data']["poster"] = $ids['poster'];

        }
        return $datainfo;
    }
    public static function get_tmts_url($ids) {//付费视频提交cookie
        preg_match('#P00010=(\d+);#iU',COOKIE_IQIYI,$_uid);
        preg_match('#QC006=(.*);#iU',COOKIE_IQIYI,$_qyid);
        $domain = "http://cache.m.iqiyi.com";
        $uid = empty($_uid[1])?"":$_uid[1];                         //用户需从cookie里P00010获取,请自行修改
        $qyid = empty($_qyid[1])? "d56mj2ujli317a4mvdxq5i73":$_qyid[1];//用户需从cookie里QC006获取,请自行修改
        $agenttype = 13;
        $time = number_format(microtime(true),3,'','');
        $type = $_REQUEST['isphone'] == 0 ? 'm3u8':'mp4';
        $tm = mb_substr($time,0,11);
        $sgti = "{$agenttype}_{$qyid}_{$time}";
        $src = '1702633101b340d8917a69cf8a4b8c7c' ; // "02020031010000000000";
        $tmtsreq = "/jp/tmts/".$ids['tvid']."/".$ids['vid']."/?uid={$uid}&cupid=qc_100001_100186&platForm=h5&qyid={$qyid}&agenttype={$agenttype}&type={$type}&rate=2&sgti={$sgti}&qdv=1&qdx=n&qdy=x&qds=0&tm={$tm}&src={$src}";//platForm=PHONE
        $vf= md5($tmtsreq."t6hrq6k0n6n6k6qdh6tje6wpb62v7654"); //参数校验码
        $url = $domain.$tmtsreq."&vf={$vf}";

        //echo $url;eixt;

        return $url;
    }

    public static function get_mp4_data($ids) {//付费视频提交cookie
        preg_match('#P00010=(\d+);#iU',COOKIE_IQIYI,$_uid);
        preg_match('#QC006=(.*);#iU',COOKIE_IQIYI,$_qyid);
        $domain = "http://cache.m.iqiyi.com";
        $uid = empty($_uid[1])?"":$_uid[1];                         //用户需从cookie里P00010获取,请自行修改
        $qyid = empty($_qyid[1])? "d56mj2ujli317a4mvdxq5i73":$_qyid[1];//用户需从cookie里QC006获取,请自行修改
        $agenttype = 13;
        $time = number_format(microtime(true),3,'','');
        $type = $_REQUEST['isphone'] == 0 ? 'm3u8':'mp4';
        $tm = mb_substr($time,0,11);
        $sgti = "{$agenttype}_{$qyid}_{$time}";
        $src='02020031010010000000';
        $a='/tmts/'.$ids['tvid'].'/'.$ids['vid'].'/?uid=&platForm=h5&agenttype=13&type=mp4&k_ft1=8&rate=2&p=&codeflag=1&qdv=1&qdx=n&qdy=x&qds=0&t='.$tm.'&src='.$src;
        $vf=md5($a.'3sj8xof48xof4tk9f4tk9ypgk9ypg5ul');
        $api='http://cache.m.iqiyi.com'.$a.'&vf='.$vf;
        return $api;
    }

    public static function get_tmts1_url($ids) {
        preg_match('#P00010=(\d+);#iU',COOKIE_IQIYI,$_uid);
        preg_match('#QC006=(.*);#iU',COOKIE_IQIYI,$_qyid);
        $domain = "https://cache.m.iqiyi.com";
        $uid = empty($_uid[1])?"":$_uid[1];                         //用户需从cookie里P00010获取,请自行修改
        $qyid = empty($_qyid[1])? "d56mj2ujli317a4mvdxq5i73":$_qyid[1];//用户需从cookie里QC006获取,请自行修改
        $agenttype = 13;
        $time = number_format(microtime(true),3,'','');

        $type = $_REQUEST['isphone'] == 0 ? 'm3u8':'mp4';
        $tm = mb_substr($time,0,11);
        $sgti = "{$agenttype}_{$qyid}_{$time}";
        $src = "02020031010000000000";
        $authkey = md5(''.$time.$tvid);
        $tmtsreq = "/jp/tmts/".$ids['tvid']."/".$ids['vid']."/?uid=&cupid=qc_100001_100186&platForm=h5&qyid=".$qyid."&agenttype=13&type=mp4&nolimit=&k_ft1=8&rate=2&sgti=".$sgti."&codeflag=1&preIdAll=&qd_v=1&qdy=a&qds=0&tm=".$tm."&src=02020031010000000000";//platForm=PHONE

        $vf= md5($tmtsreq."u6fnp3eok0dpftcq9qbr4n9svk8tqh7u"); //参数校验码
        $url = $domain.$tmtsreq."&vf={$vf}";

        return $url;
    }
    public static function get_tmts2_url($ids) {//付费视频提交cookie
        preg_match('#P00010=(\d+);#iU',COOKIE_IQIYI,$_uid);
        preg_match('#QC006=(.*);#iU',COOKIE_IQIYI,$_qyid);
        $domain = "http://cache.m.iqiyi.com";
        $uid = empty($_uid[1])?"":$_uid[1];                         //用户需从cookie里P00010获取,请自行修改
        $qyid = empty($_qyid[1])? "d56mj2ujli317a4mvdxq5i73":$_qyid[1];//用户需从cookie里QC006获取,请自行修改
        $agenttype = 12;
        $time = number_format(microtime(true),3,'','');
        $type = $_REQUEST['isphone'] == 0 ? 'm3u8':'mp4';
        $tm = mb_substr($time,0,11);
        $sgti = "{$agenttype}_{$qyid}_{$time}";
        //$src = "02020031010000000000";
        //$tmtsreq = "/jp/tmts/{$tvid}/{$vid}/?uid={$uid}&cupid=qc_100001_100186&platForm=h5&qyid={$qyid}&agenttype={$agenttype}&type={$type}&rate=2&sgti={$sgti}&qdv=1&qdx=n&qdy=x&qds=0&tm={$tm}&src={$src}";//platForm=PHONE
        //$vf= md5($tmtsreq."3sj8xof48xof4tk9f4tk9ypgk9ypg5ul"); //参数校验码

        $t = time();
        $k = "d5fb4bd9d50c4be6948c97edd7254b0e";
        $src = "76f90cbd92f94a2e925d83e8ccd22cb7";
        $sc=md5($t.$k.$vid);
        $url='http://cache.m.iqiyi.com/jp/tmts/'.$ids['tvid'].'/'.$ids['vid'].'/?t='.$t.'&sc='.$sc.'&src='.$src;
        return $url;
    }
    public static function get_app_data($ids) {  //app端的vf算法
        preg_match('#P00010=(\d+);#iU',COOKIE_IQIYI,$_uid);
        preg_match('#QC006=(.*);#iU',COOKIE_IQIYI,$_qyid);

        $uid = empty($_uid[1])?"":$_uid[1];     

        $platForm = "h5";

        $agenttype = "13";

        $type = "m3u8";

        $nolimit = 0;

        $k_ft1 = 8;

        $rate = 4;

        $p = "";

        $codeflag = "1";

        $qdv = "1";

        $qdx = "n";

        $qdy = "x";

        $qds = 0;

        $__jsT = "sgve";

        $qyid = empty($_qyid[1])? "d56mj2ujli317a4mvdxq5i73":$_qyid[1];//用户需从cookie里QC006获取,请自行修改

        $time = number_format(microtime(true),3,'','');

        $tm = mb_substr($time,0,11);

        $sgti = "{$agenttype}_{$qyid}_{$time}";

        $src = "02028001010000000000";

        $tmtsreq = "/tmts/".$ids['tvid']."/".$ids['vid']."/?uid={$uid}&platForm={$platForm}&agenttype={$agenttype}&qyid={$qyid}&platForm=IPHONE&type={$type}&nolimit={$nolimit}&k_ft1={$k_ft1}&rate={$rate}&p={$p}&codeflag={$codeflag}&qdv={$qdv}&qdx={$qdx}&qdy={$qdy}&qds={$qds}&__jsT={$__jsT}&t={$tm}&src={$src}";

        $vf= md5($tmtsreq."3sj8xof48xof4tk9f4tk9ypgk9ypg5ul"); //参数校验码
        $url = 'https://cache.m.iqiyi.com'.$tmtsreq."&vf={$vf}";
        //echo $url;exit;

        return $url;
    }

    public static function get_vps_data($ids){ //pc 端接口
        preg_match('#P00010=(\d+);#iU',COOKIE_IQIYI,$_uid);
        preg_match('#QC006=(.*);#iU',COOKIE_IQIYI,$_qyid);

        $host = 'http://cache.video.qiyi.com';

        $src = '/vps?tvid='.$ids['tvid'].'&vid='.$ids['vid'].'&v=0&qypid='.$ids['tvid'].'_12&src=1702633101b340d8917a69cf8a4b8c7c&platforms=PC_APP&t='.GlobalBase::getMillisecond().'&k_tag=1&type=mp4&k_uid='.self::get_macid().'&rs=1';

        //$vf = self::get_vf($src);
        $vf= md5($src."u6fnp3eok0dpftcq9qbr4n9svk8tqh7u");

        $api = $host . $src . '&vf=' . $vf;

        if($ids['member']==='true'){//付费视频
            $ids['cookie'] = COOKIE_IQIYI;
            $data = self::curl($api,$ids);
        }else{
            $data = self::curl($api,$ids);
        }

        //print_r($data);exit;

        $data = json_decode($data,true);

        if ($data['code'] == 'A00000') {
            $list = $data['data']['vp']['tkl'][0]['vs'];
            $dom = $data['data']['vp']['du'];
            foreach ($list as $key => $value) {
                switch ($value['bid']) {
                    case 1:
                        $def = '普清';
                        break;
                    case 2:
                        $def = '高清';
                        break;
                    case 4:
                        $def = '超清';
                        break;
                    case 96:
                        $def = '流畅';
                        break;
                }

                foreach ($value['fs'] as $k => $v) {
                    $a[$k]['file'] = self::get_pc_url_data($dom.$v['l'],$ids);
                    $a[$k]['duration'] = $v['d']/1000;
                    $a[$k]['bytesTotal'] = $v['b'];
                }
                if ($value['bid'] == 4) {
                    $_loc[0]['video'] = $a;
                    $_loc[0]['type'] = 'mp4';
                    $_loc[0]['weight'] = 10;
                    $_loc[0]['definition'] = $def;
                }
                if ($value['bid'] == 2) {
                    $_loc[1]['video'] = $a;
                    $_loc[1]['type'] = 'mp4';
                    $_loc[1]['weight'] = 10;
                    $_loc[1]['definition'] = $def;
                }
                if ($value['bid'] == 1) {
                    $_loc[2]['video'] = $a;
                    $_loc[2]['type'] = 'mp4';
                    $_loc[2]['weight'] = 10;
                    $_loc[2]['definition'] = $def;
                }

            }
            for ($i=0; $i <= 2 ; $i++) {
                if ($_loc[$i] == '') {
                continue;
            }
            $key_arrays[]=$_loc[$i];
        }
        }
        $videoinfo['code'] = 200;
        $videoinfo['data']['poster'] = $ids['poster'];
        $videoinfo['play'] = 'mp4_list';
        $videoinfo['data']['video'][0] = $key_arrays[0];

        print_r(json_encode($videoinfo));exit;
    }

    public static function get_tmts_data($ids){ 
        $t = time()*1000;
        $src = "76f90cbd92f94a2e925d83e8ccd22cb7";
        $key = "d5fb4bd9d50c4be6948c97edd7254b0e";
        $vid = $ids['vid'];
        $tvid = $ids['tvid'];
        $sc = md5($t.$key.$vid);
        $url = "http://cache.m.iqiyi.com/jp/tmts/$tvid/$vid/?t=$t&sc=$sc&src=$src";
        return $url;
    }

    public static function get_pc_url_data($url,$ids){
        $data = file_get_contents($url);

        $data = json_decode($data,true);

        $u = $data['l'];
        return $u;
    }

    public static function get_macid(){
        //'''获取macid,此值是通过mac地址经过算法变换而来,对同一设备不变'''
        $macid='';
        $chars = 'abcdefghijklnmopqrstuvwxyz0123456789';
        $size = strlen($chars);
        for ($i=0; $i < 32 ; $i++) { 
            $a = mt_rand(0,($size-1));
            $macid .= $chars[$a];
        }
        return $macid;
    }
    public static function get_vf($url_params){
        $sufix='';
        for ($i=0; $i <8 ; $i++) { 
            for ($j=0; $j <4 ; $j++) { 
                $v4 = 13 * (66 * $j + 27 * $i) % 35;

                if ($v4 >= 10) {
                    $v8 = $v4 + 88;
                } else {
                    $v8 = $v4 + 49;
                }

                $sufix .= chr($v8);

            }
            $url_params .= $sufix;
        }
        //echo $url_params;exit;
        $vf = md5($url_params);
        return $vf;
    }
    public static function iqiyi_curl($url, $params = array()) {
        $ip = empty($params["ip"]) ? GlobalBase::rand_ip() : $params["ip"]; 
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
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1); 
            curl_setopt($ch, CURLOPT_PROXY, "58.251.230.220:9797");   //代理服务器地址 host:post的格式
            if(!empty($params["proxy_userpwd"])){
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $params["proxy_userpwd"]); //http代理认证帐号，username:password的格式
            }
        }
        $data = curl_exec($ch);                                                 //运行 curl，请求网页并返回结果
        curl_close($ch);                                                        //关闭 curl
        return $data;
    }
    public static function curl($url,$ids)
    {
        $params["ua"] = !empty($ids['ua']) ? $ids['ua'] : "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
        $params["ip"] = "14.21.98.117";//伪装为固定IP，否则会报异地登录，会封号，不要更改
        if (isset($ids['cookie'])) {
            $params["cookie"] = $ids['cookie'];
        }
        $params["ref"] = "https://www.iqiyi.com";

        //$params["proxy"] = PROXY;
        //return GlobalBase::iqiyi_curl($url,$params);
        return self::iqiyi_curl($url,$params);
    }
}
