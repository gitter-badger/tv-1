<?php
namespace app\api\controller;
use think\Controller;
use think\db;
use QL\QueryList;
use Org\Http;
use Org\Base64;
class Index extends Controller
{      
    protected $_lid=[1,6,7,8,9,11,12,13,14,c14,15,c15,16,17,18,23,24,25,c25,s25,26,l302,l303,l304,205];
    public function index()
    {   
       	$api=1;
        dump( Http::doGet(config('api')['joke'][0]['url']));
    }
	public function api($lid,$vid='')
    {   
        $base = $this->base($lid,$vid);
        if (request()->isPost()){
            return $base;
        }else if(in_array($lid,$this->_lid)){
            return  json($base,200);
		}else{	
            $this->error('�ף����Ҵ���С�ĵ��ף���');
        }    
    }
    private function base($lid,$vid='')
    {   
        switch ($lid) { 
            case 1:
                $db=Model("Vod")->find($vid); 
                $base = ['title'=>'�ź���·���ݽӿ�','base'=>$db['list']];          
            break;
            case 2: 
                $str = Http::doGet('http://vdn.live.cntv.cn/api2/liveHtml5.do?channel=pa://cctv_p2p_hd'.$vid.'&client=flash');
				preg_match('|"hds1":"(.*?)"|i', $str, $flv);				
                $cmp_xml.='<m type="2" src="'.$flv[1].'"  label="CCTVԴ - ��·1"/>'. PHP_EOL;
                $base = ['title'=>'CNTV��������Դ','base'=>$this->cmp_xml($cmp_xml)];
            break;
            case 3:
                $id=!empty($vid) ? $vid : '764502578';
                $url=Http::doGet('http://liveaccess.qt.qq.com/get_video_url_v3?module='.$id.'&videotype=flv');
                preg_match('|"urllist":"(.*?)"|i', $url, $vid);
                $t = explode(';', $vid[1]);
			    foreach($t as $ks){$pp++;
                    $cmp_xml.='<m type="2" src="'.$ks.'" label="QTֱ��Դ�ź�'.$pp.'" />' . PHP_EOL;
                }
				$lists=[['title'=>'��·1','play_xml'=>$this->cmp_xml($cmp_xml)],['title'=>'��·2','play_xml'=>$this->cmp_xml($cmp_xml)]];
                $base = ['title'=>'QT��������Դ','base'=>$this->cmp_xml($cmp_xml),'lists'=>$lists];
                break;
            case 4:
                $str = Http::doGet('http://live.api.max.mgtv.com/live/templet/getUserLive?uid=&token=&appVersion=1.0.0&device=PC&endType=mgtvpc&osType=2');
                $json = json_decode($str,true);
                $id = $json['data'];
                foreach($id as $ks){$pp++;
                    $cmp_xml.='<m type="2" src="' .$ks['uid']. '" rtmp="rtmp://liveshow.cdn.max.mgtv.com/mglive/" image="' .$ks['cover']. '"  label="' .$ks['nickName']. '"/>' . PHP_EOL;
                }              
                $base = ['title'=>'â��ֱ������Դ','base'=>$this->cmp_xml($cmp_xml)];
            break;              
            case 5:
                $cmp_xml.='<m type="2" src="'.$vid.'" rtmp="rtmp://ivi.bupt.edu.cn:1935/livetv/" label="�����ʵ�IPTV - ������·"/>'."\n";
                $cmp_xml.='<m type="2" src="'.$vid.'hd" rtmp="rtmp://ivi.bupt.edu.cn:1935/livetv/" label="�����ʵ�IPTV - ������·"/>'."\n";
                $base = ['title'=>'���ʹ���ֱ��Դ','base'=>$this->cmp_xml($cmp_xml)];
            break;
            case 6: 
                if ($vid) {
                    $djson = json_decode(Http::doGet('http://www.quanmin.tv/json/categories/' . $vid . '/list.json'),true);	
                    foreach($djson['data'] as $kds){$pp++;
                        $cmp_xml.='<m type="2" src=" '.$kds['stream']. '" image="' .$kds['live_thumb']. '" label="' .$kds['title']. '" />' . PHP_EOL;
                    }
					echo self::cmp_xml($cmp_xml);
                }else{
                    $json = json_decode(Http::doGet("http://www.quanmin.tv/json/categories/list.json"),true);
                    foreach($json as $ks){$pp++;
                        $cmp_xml.= '<m list_src="'.url('api',['lid'=>'6','vid'=>$ks['slug']]).'" label="' .$ks['name']. '"/>' . PHP_EOL;
                    }
					$base = ['title'=>'ȫ�����ֱ��Դ','base'=>$this->cmp_xml($cmp_xml)];
                }  
            break;
            case 7:
                if ($vid) {
                    $json =  json_decode(Http::doGet('http://rest.zhibo.tv/anchor/get-list-by-type-id?page=1&id='.$vid.'&equipment=1&size=20'),true);                   
                    foreach($json['data']['data'] as $ks){$pp++;
                        $cmp_xml.='<m type="2" src="' .$ks['userStreamName']. '" rtmp = "rtmp://live.zhibo.tv/8live/" label="' .$ks['title']. '" />' . PHP_EOL;
                    }					
                     echo self::cmp_xml($cmp_xml);
                }else{
                    $dson =  json_decode(Http::doGet('http://rest.zhibo.tv/schedule/get-type-list-new'),true);	
					$cmp_xml = '<m list_src="'.url('api',['lid'=>7,'vid'=>900000000]). '"  label="����ֱ��"/>' . PHP_EOL;
                    foreach ($dson['data']['typeList'] as $k => $v) {
                        $cmp_xml.= '<m list_src="'.url('api',['lid'=>7,'vid'=>$v['id']]). '"  label="' . $v['name'] . '"/>' . PHP_EOL;
                    }   
					$base = ['title'=>'�й�����ֱ��','base'=>self::cmp_xml($cmp_xml)];
                }                 
            break;
            case 8:
                if ($vid) {
                    $json = json_decode(Http::doGet('http://api.plu.cn/tga/streams?max-results=10000&start-index=0&sort-by=top&filter=0&game=' . $vid),true);
                    $data= $json['data']['items'];
                    foreach($data as $ks){$pp++;
                        $str = json_decode(Http::doGet('http://livestream.plu.cn/live/getlivePlayurl?roomId=' .$ks['channel']['id']),true);
                        $flv= $str['playLines'][0]['urls'][0]['securityUrl'];
                        $m3u8= $str['playLines'][0]['urls'][2]['securityUrl'];
                        $cmp_xml.='<m type="2" src="'.$flv.'" image="' .$ks['channel']['avatar']. '" label="'.$ks['channel']['name'].'" />' . PHP_EOL;
                    }
					echo self::cmp_xml($cmp_xml);
                }else{$dj = array (
                       '������ҫ' => '105',
                       '�����г�' => '247',
                       'Ӣ������' => '4',
                       '��Խ����' => '7',
                       '���³�����ʿ' => '5',
                       '��ս' => '14',
                       '��������' => '250',
                       '��������' => '235',
                    );
                    foreach ($dj as $k => $v) {
                        $cmp_xml.= '<m list_src="'.url('api',['lid'=>8,'vid'=>$v]). '"  label="' . $k . '"/>' . PHP_EOL;
                    }
					$base = ['title'=>'����ֱ��Դ','base'=>self::cmp_xml($cmp_xml)]; 
                }                             
            break;
            case 9:   
			    if ($vid) {
                    $data = json_decode(Http::doGet('http://api.qiecdn.com/api/v1/live/'.$vid),true);					
                    foreach($data['data'] as $ks){$pp++;                        
                        $cmp_xml.='<m type="2" src="'.url('api',['lid'=>'s9','vid'=>$ks['room_id']]).'" image="' .$ks['channel']['avatar']. '" label="'.$ks['room_name'].'" />' . PHP_EOL;
                    }
					echo self::cmp_xml($cmp_xml);
                }else{
                    $json = ['name'=>['NBA','̨��','����','CBA','����','����','Ӣ��ԭ��'],'id'=>['197','200','198','231','202','214','215']];
                    foreach ($json['id'] as $k => $v) {
                        $cmp_xml.= '<m list_src="'.url('api',['lid'=>9,'vid'=>$v]). '"  label="' . $json['name'][$k] . '"/>' . PHP_EOL;
                    }               
                   $base = ['title'=>'���ֱ��','base'=>self::cmp_xml($cmp_xml)]; 
				}
            break;
			case s9:   
			    $data = json_decode(Http::doGet('http://api.qiecdn.com/api/v1/room/'.$vid),true);
				header('Location: ' . $data['data']['rtmp_url'].'?'.$data['data']['rtmp_live']);
				exit();
            break;
            case 10:
                if ($vid) {
                   $json =json_decode(Http::doGet('https://m.douyu.com/html5/live?roomId='.$vid),true);
                    $data=$json['data'];
                    $cmp_xml.='<m type="m3u8"  src= "'.$data['hls_url'].'" label="'.$data['room_name'].'" />'."\n";
                }
                $base = ['title'=>'�������ֱ��Դ','base'=>self::cmp_xml($cmp_xml)];  
                
            break;      
            case 11:  //���ŵ�ַʧЧ��
                if ($vid) {
                    $data =json_decode(Http::doGet('http://www.kktv1.com/CDN/output/M/3/I/10002032/P/partId-'.$vid.'_start-0_offset-10000_platform-1/json.js'),true);
                    $roomList=$data['roomList'];
                    foreach($roomList as $ks){$pp++;
                        $cmp_xml.='<m type="2" src="http://pull.kktv8.com/livekktv/'.$ks['roomId'].'.flv"  label="'.$ks['nickname'].' " />' . PHP_EOL;
                    }
                    $base = self::cmp_xml($cmp_xml);    
                }else{
                    $type=array(
                        'KK�Ƽ�'=>'100',
                        '������'=>'101',
                        '������'=>'104',
                        '������'=>'103',
                        '������'=>'105',
                        '�ֻ���'=>'106',
                        '���涷��'=>'107'
                    );
                    foreach ($type as $k => $v) {
                        $cmp_xml .= '<m list_src="'.url('api',['lid'=>11,'vid'=>$v]). '" label="' . $k . '" />' . PHP_EOL;
                    }
                }               
                $base = ['title'=>'KKƽ̨����ֱ��Դ','base'=>self::cmp_xml($cmp_xml)];    
            case 12:
                if ($vid) {
                    $data =json_decode(Http::doGet('http://www.kaolafm.com/webapi/audios/list?id='.$vid.'&pagesize=20&pagenum=1&sorttype=1'),true);
                    $count=$data['result']['count'];
                    $num = $count /20 +1;
                    for($s=1;$s<=$num;$s++){
                        $url=json_decode(Http::doGet('http://www.kaolafm.com/webapi/audios/list?id='.$vid.'&pagesize=20&pagenum='. $s .'&sorttype=1'),true);
                        $roomList = $url['result']['dataList'];                         
                        foreach($roomList as $ks){$pp++;
                            $cmp_xml.='<m type="1" src="'.$ks['mp3PlayUrl'].'" image="'. $ks['audioPic'] .'" label="'.$ks['audioName'].' " />' . PHP_EOL;
                        }                   
                    }
                    return self::cmp_xml($cmp_xml);
                }else{
                    for($i=1;$i<=5;$i++){ //��128ҳ��һ�βɼ��긺�ɺܴ�ȡ5ҳ
                        $a = json_decode(Http::doGet('http://www.kaolafm.com/webapi/resource/search?cid=32&rtype=20000&sorttype=HOT_RANK_DESC&pagesize=24&pagenum='.$i),true);
                        $datalist = $a['result']['dataList'];
                        foreach ($datalist as $k){$pp++;
                            $cmp_xml .='<m list_src="'.url('api',['lid'=>12,'vid'=>$k['id']]). '" image="'. $k['pic'] .'" label="'. $k['albumName'] .'" />'. PHP_EOL;
                        }
                    }
                }
                $base = ['title'=>'��������ֱ��Դ','base'=>self::cmp_xml($cmp_xml)];  
            break;
            case 13:
                if ($vid) {
                    $data =json_decode(Http::doGet('http://info.zb.qq.com/?stream=1&cmd=2&cnlid='.$vid.'&sdtfrom=003'),true);
                    $back=$data['backurl_list'];
                    $cmp_xml .='<m src="'.$data['playurl']. '"  label="����·1" />'. PHP_EOL;
                    $cmp_xml .='<m src="'.$back[0]['url']. '"  label="������·2" />'. PHP_EOL;
                    $cmp_xml .='<m src="'.$back[0]['url']. '"  label="������·3" />'. PHP_EOL;
                    echo self::cmp_xml($cmp_xml);
                }else{                  
                    $a = json_decode(Http::doGet('http://data.v.qq.com/live/api/category/2/channel/query?client=web&with_current_program=1'),true);
                    $channels=$a['data']['channels'];
                    foreach ($channels as $k){$pp++;
                        $dat =json_decode(Http::doGet('http://info.zb.qq.com/?stream=1&cmd=2&cnlid='.$k['fluent_stream_id'].'&sdtfrom=003'),true);
                        $playurl=$dat['playurl'];
                        $backurl=$dat['backurl_list'];
                        $cmp_xml .='<m src="'.$playurl. '" image="'. $k['images'] .'" label="'. $k['name'] .' - ����·1" />'. PHP_EOL;
                        $cmp_xml .='<m src="'.$backurl[0]['url']. '" image="'. $k['images'] .'" label="'. $k['name'] .' - ������·2" />'. PHP_EOL;
                        $cmp_xml .='<m src="'.$$backurl[1]['url']. '" image="'. $k['images'] .'" label="'. $k['name'] .' - ������·3" />'. PHP_EOL;
                    }
                
                }
                $base = ['title'=>'��Ѷ����ֱ��Դ','base'=>self::cmp_xml($cmp_xml)];      
            break;
            case 14:
                if($vid){
                    $t = explode('-', $vid);
                    for ($i = 1; $i <= $t[1]; $i++) {
                        $cmp_xml.= '<m list_src="'.url('api',['lid'=>'c14','vid'=>$t[0].'-'.$i]).'" label="��' . $i . 'ҳ" />'. PHP_EOL;
                    } 
                    echo $this->cmp_xml($cmp_xml);
                }else{
                    $dj = array ('����' => 't10025-30','˵��' => 't10026-30','��������' => 't10028-7','����' => 't10029-9'); 
                    foreach ($dj as $k => $v) {
                        $cmp_xml .= '<m list_src="'.url('api',['lid'=>14,'vid'=>$v]).'" label="' . $k . '" />'. PHP_EOL;
                    }
                }
                $base = ['title'=>'YY��������ֱ��Դ','base'=>self::cmp_xml($cmp_xml)];        
            break;
            case 'c14':
                $t = explode('-', $vid);
                $b = Http::doGet('http://shenqu.yy.com/clist/'.$t[0].'_p'.$t[1].'.html');
                preg_match_all('|<a href="/shenqu/play/id_([0-9]+).html" title="(.*?)"|', $b, $c);
                foreach ($c[1] as $k=>$v) {
                    $cmp_xml.='<m type="2" src="'.url('api',['lid'=>'s14','vid'=>$v]).'"  label="'.$c[2][$k].'"/>'. PHP_EOL;
                }
                echo self::cmp_xml($cmp_xml);
            break;
            case 's14': 
                $data = json_decode(Http::doGet('http://shenqu.yy.com/show/info.do?resid='.$vid),true);
                header('Location: ' . $data['data']['resurl']);
                exit();
            break;
            case 16:
                if ($vid) {
                    $str =Http::doGet('http://m.zhangyu.tv/channel/'.$vid);
                    preg_match_all('|<a href="../tv/(.*?)"|', $str,$bb);
                    foreach ($bb[1] as $ks){$pp++;
                        $sur=Http::doGet('http://m.zhangyu.tv/tv/'.$ks);
                        preg_match("|_src='(.*?)'|i", $sur,$bbb);
                        preg_match("|<title>(.*?)</title>|i", $sur,$ttt);
                        $cmp_xml .= '<m type="m3u8" src="'.$bbb[1]. '" label=" '.$ttt[1]. '" />'. PHP_EOL;
                    }
                    echo self::cmp_xml($cmp_xml);
                }else{
                    $data =Http::doGet('http://m.zhangyu.tv/');
                    preg_match_all('|<div  id="([^"]+)" class="">([^"]+)</div></a></td>|i', $data,$b);
                    foreach ($b[1] as $k => $v) {
                        $cmp_xml .= '<m list_src="'.url('api',['lid'=>16,'vid'=>$v]).'" label="'.$b[2][$k].'" />'. PHP_EOL;
                    }
                }  
                $base = ['title'=>'�������ֱ��Դ','base'=>$this->cmp_xml($cmp_xml)];              
            break;
            case 17 :
                if ($vid) {
                    $str =json_decode(Http::doGet('http://qcc.flash127.com/public/playlist.html?id='.$vid));
                    foreach ($str as $ks){$pp++;
                        $cmp_xml .= '<m type="3" src="'.$ks->audio. '" label=" '.$ks->title. '" />'. PHP_EOL;
                    }
                    return self::cmp_xml($cmp_xml);
                }else{
                    $data =Http::doGet('http://qcc.flash127.com/play.html');
                    preg_match_all('|id="([^"]+)">([^"]+)</a>|U', $data,$b);
                    foreach ($b[1] as $k => $v) {
                        $cmp_xml .= '<m list_src="'.url('api',['lid'=>17,'vid'=>$v]).'" label="'.$b[2][$k].'" />'. PHP_EOL;
                    }
                }
                
                $base = ['title'=>'DJ���ֵ㲥Դ','base'=>self::cmp_xml($cmp_xml)];           
            break; 
            case 18 :
                if ($vid) {
                    $str =json_decode(Http::doGet('http://mobile.open.163.com/movie/'.$vid.'/getMoviesForAndroid.htm'),true);
                    $datad=$str['videoList'];
                    foreach ($datad as $ks){$pp++;
                        $cmp_xml .= '<m type="2" src="'.$ks['repovideourlmp4']. '" label=" '.$ks['title']. '" />'. PHP_EOL;
                    }
                    return $this->cmp_xml($cmp_xml);
                }else{
                    $data =json_decode(Http::doGet('http://mobile.open.163.com/movie/2/getPlaysForAndroid.htm'),true);
                    foreach ($data as $k => $v) {
                        $cmp_xml .= '<m list_src="'.url('api',['lid'=>18,'vid'=>$v['plid']]).'" label="'.$v['title'].'" />'. PHP_EOL;
                    }
                }
                $base = ['title'=>'���׹����ε㲥','base'=>$this->cmp_xml($cmp_xml)];           
            break;          
            case 19: 
                $api=Model('vod')->play($vid);
                $url=explode(chr(13),str_replace(array("\r\n", "\n", "\r"),chr(13),$api['vod_url']));
                foreach ( $url as $k=>$v) {
                    $i=$k+1;
                    if(strpos($v,'m3u8')){
                        $cmp_xml .= '<m type="m3u8" src="'.$v.'" label="'.$api['vod_name'].' - ��·'.$i.'" />'. PHP_EOL;
                    }else if (strpos($v,'mp3')){
                        $cmp_xml .= '<m type="3" src="'.$v.'" label="'.$api['vod_name'].'- ��·'.$i.'" />'. PHP_EOL;
                    }else{
                        $cmp_xml .= '<m type="2" src="'.$v.'" label="'.$api['vod_name'].'- ��·'.$i.'" />'. PHP_EOL;
                    }
                }   
                $base = ['title'=>'VIP������ƵԴ','base'=>$this->cmp_xml($cmp_xml)]; 
            break;
            case 20:
                $ar =explode("_",$vid);
                $data =explode("\n",Http::doGet(RUNTIME_PATH .'data/_txt/'.$ar[0].'.txt'));
                if($vid>count($data)){break;}
                $tr=$ar[1]-1;
                $arr=explode(",",$data[$tr]);
                $t = explode('|' , $arr[1]);
                foreach($t as $k=>$v){
                    $v = rtrim(ltrim($v));
                    $i = $k+1;                  
                    if($v){
                        if (strpos($v, 'm3u8')){
                            $cmp_xml.='<m type="m3u8" src= "'.$v.'" label="'.$arr[0].' - ��· '.$i.'" />'. PHP_EOL;

                        }else{
                            $cmp_xml.='<m type="2" src= "'.$v.'" label="'.$arr[0].' - ��· '.$i.'" />'. PHP_EOL;
                        }
                    }
                }
                $base = ['title'=>'�Զ������Դ','base'=>$this->cmp_xml($cmp_xml)];
            break;
            case 21:            
                switch ($vid) {
                    case in_array($vid, array(10044, 10497, 10613, 10765)):
                        $cmp_xml.='<m type="2" src="phd'.$vid.'" rtmp="rtmp://edge1.everyon.tv/etv1sb/" label="����Դ'.$vid.'-720*480"/>'."\n".'<m type="2" src="pld'.$vid.'" rtmp="rtmp://edge1.everyon.tv/etv1sb/" label="����Դ'.$vid.'-480*320" />'."\n".'<m type="2" src="audio'.$vid.'" rtmp="rtmp://edge1.everyon.tv/etv1sb/" label="��ƵԴ'.$vid.'-720*480"/>'. PHP_EOL;
                        break;
                    case in_array($vid, array(1003, 1005, 1006, 1008)):
                        $cmp_xml.='<m type="2" src="phd'.$vid.'" rtmp="rtmp://edge2.everyon.tv/etv2/" label="����Դ'.$vid.'-720*480"/>'."\n".'<m type="2" src="pld'.$vid.'" rtmp="rtmp://edge2.everyon.tv/etv2/" label="����Դ'.$vid.'-480*320" />'."\n".'<m type="2" src="audio'.$vid.'" rtmp="rtmp://edge.everyon.tv/etv2/" label="��ƵԴ'.$vid.'-720*480"/>'. PHP_EOL;
                        break;
                    default:
                        $cmp_xml.='<m type="2" src="phd'.$vid.'" rtmp="rtmp://edge2.everyon.tv/etv2sb/" label="����Դ'.$vid.'-720*480"/>'."\n".'<m type="2" src="pld'.$vid.'" rtmp="rtmp://edge2.everyon.tv/etv2sb/" label="����Դ'.$vid.'-480*320" />'."\n".'<m type="2" src="audio'.$vid.'" rtmp="rtmp://edge.everyon.tv/etv2sb/" label="��ƵԴ'.$vid.'-720*480"/>'. PHP_EOL;
                        break;
                }
                $base = ['title'=>'��18��������ƵԴ','base'=>$this->cmp_xml($cmp_xml)];            
            break;
            case 22:
                $cmp_xml.='<m type="2" list_src="http://345pk.vip/yy/'.$vid.'.xml" label="VIP����ƽ̨�ɼ���S8YY.COM��վ" />'. PHP_EOL;
                $base = ['title'=>'18��������ƵԴ','base'=>$this->cmp_xml($cmp_xml)]; 
            case 23:
                if ($vid) {
                    $x =Http::doGet('http://www.av.win/av/jx.php?id=2&url=http://www.ll0.com/categories/'.$vid.'/');
                    preg_match_all('|videos/(.*?)/(.*?)/" class="kt_imgrc" title="(.*?)"|i', $x,$y);
                    $cmp_xml ='<list>'. PHP_EOL;
                    foreach($y[1] as $r => $u){
                        $cmp_xml.='<m type= "2" label="'.$y[3][$r].'"  src="'.url('cmp',['lid'=>'s23','vid'=>$u.'_'.$y[2][$r]]). '" />'. PHP_EOL;
                    }
                    return $this->cmp_xml($cmp_xml);
                }else{
                    $data= Http::doGet('http://www.av.win/av/');
                    preg_match_all('|id=2&url=http://www.ll0.com/categories/(.*?)/"><span class="name">([^"]+)</span>|i', $data,$vip);
                    foreach ($vip[1] as $k => $v) {
                       $d = $vip[2];  
                       $cmp_xml.= '<m list_src="'.url('api',['lid'=>23,'vid'=>$v]). '" label="' . $d[$k] . '" />'. PHP_EOL;
                    }
                }
                $base = ['title'=>'VIP������ƵԴ','base'=>$this->cmp_xml($cmp_xml)]; 
            break;  
            case 's23':
                $t =explode("_",$vid);
                $data= Http::doGet('http://www.av.win/av/bf.php?id=2&url=http://www.ll0.com/videos/'.$t[0].'/'.$t[1]);   
                preg_match("|var URL='(.*?)'|U", $data,$src);
                header('Location: ' . $src[1]);
                exit();
            break; 
            case 24:
                if ($vid) {
                    $x =simplexml_load_string(Http::doGet('http://www.porntv.ro/tv/playlist.php?id='.$vid))->trackList->track;
                    foreach($x as $r){
                        $cmp_xml.='<m label="��������VIP��Ŀ��Ϊ�����'.$r->title.'"  src="'.trim($r->location).'?start={start_seconds}" text="����������VIP��Ŀ�ռ���QQ�ͷ���22366358��" />'."\n";
                    }
                    return $this->cmp_xml($cmp_xml);
                }else{
                    $vip = array (
                       'VIP�ʺ�1̨' => 'streamxa',
                       'VIP�ʺ�2̨' => 'streamxb',
                       'VIP�ʺ�3̨' => 'streamxc',
                       'VIP�ʺ�4̨' => 'streamxd',
                       'VIP�ʺ�5̨' => 'streamxe',
                       'VIP�ʺ�6̨' => 'streamxf',
                       'VIP�ʺ�7̨' => 'streamxg'
                    );
                    foreach ($vip as $k => $v) {                      
                       $cmp_xml .= '<m list_src="'.url('api',['lid'=>24,'vid'=>$v]). '" label="VIP��ĿԴ - ' . $k . '" />'. PHP_EOL;
                    }
                }
                $base = ['title'=>'VIP�ʺ�ֱ̨��Դ','base'=>$this->cmp_xml($cmp_xml)];    
            break;
            case 25:
                if($vid){
                    $t =explode("-",$vid);
                    $cmp_xml .= '<m list_src="'.url('api',['lid'=>'c25','vid'=>$t[0]]). '" label=" �� 1 ҳ" />'. PHP_EOL;
                    for ($i = 2; $i <= $t[1]; $i++) {
                        $cmp_xml.= '<m list_src="'.url('api',['lid'=>'c25','vid'=>$t[0].'-'.$i]). '" label=" �� '.$i.' ҳ" />'. PHP_EOL;
                    }
                    return $this->cmp_xml($cmp_xml);
                }else{
                    $vip = array ('�պ�Ů��' => '5-38','ŷ������' => '6-33','͵������' => '4-18','���˶���' => '8-11','��������' => '7-17','ǿ������' => '9-18','��̬����' => '10-10','�Ʒ�˿��'=>'11-16','����3P'=>'12-17');
                    foreach ($vip as $k => $v) {
                        $cmp_xml.= '<m list_src="'.url('api',['lid'=>'25','vid'=>$v]). '" label="' . $k . '" />'. PHP_EOL;                  
                    }
                }
                $base = ['title'=>'VIP������ƵԴ','base'=>$this->cmp_xml($cmp_xml)]; 
            break;
            case 'c25':
               $data=Http::doGet('http://www.56caiji.com/vodlist/'.$vid.'.html');
                preg_match_all('|<li class="name"><a href="/vod/([^"]+).html" title="([^"]+)"|U', $data,$json);
                foreach($json[1] as $k => $v){
                    $cmp_xml .= '<m type="" list_src="'.url('api',['lid'=>'s25','vid'=>$v]).'" label="'.$json[2][$k].'" />'. PHP_EOL;  
                } 
                return $this->cmp_xml($cmp_xml);
            break;  
            case 's25':
                $data = Http::doGet('http://www.56caiji.com/vod/'.$vid.'.html');
                preg_match('|name="m3u8url" value="([^"]+)"|U', $data,$json);
                preg_match('|name="gqurl" value="([^"]+)"|U', $data,$dson);
                $cmp_xml .= '<m type="m3u8" src="'.$json[1].'" label="������Դ" />'. PHP_EOL;
                $cmp_xml .= '<m type="m3u8" src="'.$dson[1].'" label="������Դ" />'. PHP_EOL;    
                return $this->cmp_xml($cmp_xml);
            break;
            case 26:
                $data =[
				    'id'=>['cctv1','hd_cctv1','cctv2','cctv3','cctv4','hd_cctv5','cctv6','cctv7','cctv8','cctv9','cctv10','cctv11','cctv12','cctv13','cctv14','cctv15','cctv3','cetv','bjws'.'hd_bjws','dfws','hd_dfws','tjws','cqws','hljws','hd_hljws','jlws','lnws','nxws','gsws','qhws','shxws','hebws','sxws','sdws','ahws','hd_ahws','henws','hbws','hd_hbws','hnws','jxws','jsws','hd_jsws','zjws','hd_zjws','dnws','gdws','hd_gdws','szws','gxws','ynws','gzws','xjws','lyws','htv1','htv2','htv3','htv4','yybb','cmpd','hqqg','sctx','jykt','cpd','dzty'],
					'title'=>['CCTV1�ۺ�','CCTV1�ۺ�HD','CCTV2�ƾ�','CCTV3����','CCTV4���Ĺ���','CCTV5����HD','CCTV6��Ӱ','CCTV7����ũҵ','CCTV8���Ӿ�','CCTV9��¼','CCTV10�ƽ�','CCTV11Ϸ��','CCTV12����뷨','CCTV13����','CCTV14�ٶ�','CCTV15����','CETV1�н�1̨','��������','��������HD','��������','��������HD','�������','��������','����������','����������HD','��������','��������','��������','��������','�ຣ����','��������','�ӱ�����','ɽ������','ɽ������','��������','��������HD','��������','��������','��������HD','��������','��������','��������','��������HD','�㽭����','�㽭����HD','��������','�㶫����','�㶫����HD','��������','��������','��������','��������','�½�����','��������','�����ۺ�','��������','����Ӱ��','�����ٶ�','���ű���','����Ƶ��','�������','�ղ�����','��ӥ��ͨ','��Ƶ��','��������']
				]; 
				if($vid){
					$vid=$vid-1;
					$cmp_xml='<m type="2" src="'.$data['id'][$vid].'" rtmp="rtmp://livertmp-al.wasu.cn/live1/" label="'.$data['title'][$vid].'" />'. PHP_EOL;
				}else{
					foreach ($data['id'] as $k => $v) {
                        $cmp_xml .='<m type="2" src="'.$v.'" rtmp="rtmp://livertmp-al.wasu.cn/live1/" label="'.$data['title'][$k].'" />'. PHP_EOL;                
                    }
				}
                $base = ['title'=>'VIP���廪����·','base'=>$this->cmp_xml($cmp_xml)]; 
            break;		
            case 100:  //101--200 ckp
                $url =Http::doGet('http://www.6080xsjue.com/html/'.$vid.'.html');
                preg_match('|<iframe src="(.*?)"|i', $url, $vid);	
				preg_match('|url=(.*?)"|i', Http::doGet($vid[1]), $src);
				dump($src[1]);exit();
                $base = ['title'=>'���������ĿԴ','base'=>$data['hls_url']];  
            break;			
            case 101:  //101--200 ckp
                $json =json_decode(Http::doGet('https://m.douyu.com/html5/live?roomId='.$vid),true);
                $data=$json['data'];               
                $base = ['title'=>'���������ĿԴ','base'=>$data['hls_url']];  
            break; 
            case 102:
                $id=!empty($vid) ? $vid : '764502578';					
                $url=Http::doGet('http://liveaccess.qt.qq.com/get_video_url_v3?module='.$id.'&videotype=flv');
                preg_match('|"urllist":"(.*?)"|i', $url, $vid);				
                $t = explode(';', $vid[1]);
			    foreach($t as $ks){$pp++;
                    $chp[]=[$ks, 'vide/mp4', '�ź�'.$pp, $pp];
                }                
                $base = ['title'=>'QTֱ��̨','base'=>$chp];
            break;    
            case 103: 
                $data=json_decode(Http::doGet('http://m.yinyuetai.com/mv/get-simple-video-info?&videoId='.$vid),true);			   		
			    $date=json_decode(Http::doGet('http://www.yinyuetai.com/api/info/get-video-urls?videoId='.$vid),true);	
                $base = ['title'=>'����̨  - '.$data['videoInfo']['title'],'base'=>$this->chp_api($date)];
            break;  
            case 104: //�乾��Ƶ
                $data=json_decode(Http::doGet('http://www.miguvideo.com//wap/resource/pc/data/detailData.jsp?cid='.$vid),true);
				$chl=[
				    [$data[0]['pilotPlay'], '', '�Զ�', 5],
					[$data[0]['pilotPlayList']['play41'], '', '����', 0],
					[$data[0]['pilotPlayList']['play42'], '', '����', 0],
					[$data[0]['pilotPlayList']['play43'], '', '����', 0]
				];
                $base = ['title'=>'�乾��Ƶ','base'=>$chl];   
            break;                       
            case 105:
                $json= ['4AC51C17-9FBE-47F2-8EE0-8285A66EAFF5','270DE943-3CDF-45E1-8445-9403F93E80C4','2c942450-2165-4750-80de-7dff9c224153','35383695-26c3-4ce5-b535-0001abce11e4'];
                $data = json_decode(Http::doGet('http://live.ifeng.com/liveAllocation.do?cid='.$json[$vid]),true);                
                $base = ['title'=>'��˹���ֱ��Դ','base'=>$data['link']]; 
            break;
			case 106:  //101--200 ckp
                $json =json_decode(Http::doGet('http://xiai09.com/ckplayer/video.php?url='.$vid),true);
                $data=$json['video'];   
                $base = ['title'=>'ϲ��ɫߣߣ�� - www.xiai09.com','base'=>$data[0]['file']];  
            break;
            case 107: 
                $str = Http::doGet('http://vdn.live.cntv.cn/api2/liveHtml5.do?channel=pa://cctv_p2p_hd'.$vid.'&client=flash');
				preg_match('|"hds2":"(.*?)"|i', $str, $flv);
                $base = ['title'=>'CNTV��������Դ','base'=> $flv[1]];
            break;			
            case 200:
                $ar= explode("_",$vid);
                $data =explode("\n",file_get_contents(RUNTIME_PATH .'data/_txt/'.$ar[0].'.txt'));
				$data =explode("\n",Http::doGet('http://' . $_SERVER['SERVER_NAME'] .'/daili/_txt/'.$ar[0].'.txt'));
                if($vid>count($data)){break;}
                $tr=$ar[1];
                $arr=explode(",",$data[$tr]);
                $t = explode('|' , $arr[1]);
                foreach($t as $k=>$v){
                    $v = rtrim(ltrim($v));
                    $i = $k+1;                  
                    $ckt_list[] = ['name'=>$arr[0].' - ��· '.$i,'lists'=>$v];
                }
                $base =  ['title'=>$data[0],'base'=>$ckt_list]; 
           break;          
           case 201:
                if($vid){
                    $arr=explode("\n",file_get_contents(RUNTIME_PATH .'data/_txt/'.$vid.'.txt'));
                    foreach($arr as $k=>$v){
						$i=$k+1;
                        $v=rtrim(ltrim($arr[$i]));
                        $v=preg_replace('|\s+|', ',', $v);
                        $t = explode(',', $v);
                        $ckt_list[] = ['name'=>$t[0],'lists'=>$t[1]];
                    }
                }else{
                    $data =json_decode(Http::doGet('http://dynamic.live.app.m.letv.com/android/dynamic.php?luamod=main&mod=live&ctl=channel&act=index&clientId=1003&belongArea=100&pcode=010110756&version=7.3&signal=5%2C7'),true);              
                    $bata = $data['body']['result']['rows'];                
                    foreach ($bata as $k=>$v) {                 
                        $ckt_list[] = ['name'=>$v['channelName'],'lists'=>'http://live.g3proxy.lecloud.com/gslb?stream_id='.$v['streams'][0]['streamName'].'&tag=live&ext=m3u8&sign=live_tv&platid=10&splatid=1009&format=letv&expect=3'];
                    }
                }   
                $base =  ['title'=>$arr[0],'base'=>$ckt_list]; 
            break;
            case 202:
                $str=empty($str) ? 'http://201610.shipinmp4.com/'.$vid : 'http://201610.shipinmp4.com/';
                $data =Http::doGet($str);
                preg_match_all('|">([^"]+)</A><br>|i', $data,$a);                
                $datad =Http::doGet($str.$a[1][$vid].'/');
                preg_match_all('|&lt;dir&gt; <A HREF="([^"]+)">([^"]+)</A>|i', $datad,$b);
                $c=$b[1];
                foreach ($b[2] as $k=>$v) {
                    $ckt_list[] = ['name'=>$v,'lists'=>'http://201610.shipinmp4.com/'.$c[$k].'/1/hls/index.m3u8'];
                }
                $base =  ['title'=>'�ɼ��ڵ�������վ','base'=>$ckt_list];               
            break;
            case 203  :
                if ($vid) {
                    $str =Http::doGet('http://m.zhangyu.tv/channel/'.$vid);
                    preg_match_all('|<a href="../tv/([^"]+)"|i', $str,$bb);
                    foreach ($bb[1] as $k=>$v) {
                        $sur=Http::doGet('http://m.zhangyu.tv/tv/'.$v);
                        preg_match("|_src='(.*?)'|i", $sur,$bbb);
                        preg_match("|<title>(.*?)-|i", $sur,$ttt);
                        $ckt_list[] = ['name'=>$ttt[1],'lists'=>$bbb[1]];
                    }
                    $title = '�����������ֱ��'; 
                }else{
                    $str =Http::doGet('http://m.zhangyu.tv');
                    preg_match_all('|<a href="../tv/([^"]+)"|i', $str,$bb);             
                    foreach ($bb[1] as $k=>$v) {
                        $sur=Http::doGet('http://m.zhangyu.tv/tv/'.$v);
                        preg_match("|_src='(.*?)'|i", $sur,$bbb);
                        preg_match("|<title>(.*?)-|i", $sur,$ttt);
                        $ckt_list[] = ['name'=>$ttt[1],'lists'=>$bbb[1]];
                    }
                    $title = '�������ȫ��ֱ��'; 
                }     
                $base =  ['title'=>$title,'base'=>$ckt_list];  
            break;
            case 204:   
                $data =json_decode(Http::doGet('http://vcis.ifeng.com/api/homePageList?platformType=androidPhone&channelId='.$vid.'&pageSize=60&requireTime=1497000367877&isNotModified=0&adapterNo=7.3.2&protocol=1.0.3&operation=down&userId=&deviceId=aa3586fc09d8348c46a8c795185bdc67&lastDoc=<,01549a84-66f7-4a73-8d2e-42c6d733aec7>&uptimes=1'));               
                $dList=$data->bodyList; 
                if(is_array($dList)){
                    foreach ($dList as $k=>$v) {
                        $Item=$v->memberItem;
                        $video=$Item->videoFiles;
                        $src=$video[2]->mediaUrl ? $video[2]->mediaUrl : $video[0]->mediaUrl;
                        $ckt_list[] = ['name'=>$Item->name,'lists'=>$src];
                    }  
                }               
                $base =  ['title'=>'�����Ƶ�б�','base'=>$ckt_list ];
            break;
            case 205:   
                $data =json_decode(Http::doGet('http://v5m.api.mgtv.com/remaster/vrs/getVideoListByPartId?abroad=0&partId='.$vid.'&pageNum=1&pageSize=1000&needLocate=1'),true); 
				foreach ($data['data']['list'] as $k=>$v) {
					$ckt_list[] = ['name'=>$v['title'].':'.$v['subtitle'],'lists'=>$this->base('s205',$v['partId'])];
				}  
                $base =  ['title'=>$data['data']['collName'].' - '.$data['data']['hint'],'base'=>$ckt_list ];
            break;
            case s205:   
				$data=json_decode(Http::doGet('http://pcweb.api.mgtv.com/player/video?video_id='.$vid),true);	
				$json=json_decode(Http::doGet('http://disp2.titan.mgtv.com'.$data['data']['stream']),true);
				foreach ($data['data']['stream'] as $x=>$y) {
					$json=json_decode(Http::doGet('http://disp2.titan.mgtv.com'.$y['url']),true);
					$base[] = [$json['info'], '', $y['name'], $x];
				}				
            break;			
            case 206:                 			
                $data =json_decode(Http::doGet('http://v5m.api.mgtv.com/remaster/listV5/search?abroad=0&fstlvlId='.$vid.'&ic=1&pageNum=1&pageSize=30&kind=a4'),true); 
				foreach ($data['data'] as $k=>$v) {
					$ckt_list[] = ['name'=>$v['name'].' - '.$v['subtitle'],'img'=>$v['image'],'lists'=>$this->base('s205',$v['info']['playPartId'])];
				}  
                $base =  ['title'=>$data['data']['info']['title'],'base'=>$ckt_list ];
            break;
            case 207:   
                $data =['title'=>['VIP��Ӱ','��Ӱ','���Ӿ�','����','����','����','��¼Ƭ'],'id'=>['3&ic=1&iv=1&vip=1','3&ic=1','2&ic=1','1&ic=1','7&ic=1','4&ic=0','5&ic=1']];
				$vid=$vid-1;
				$json=json_decode(Http::doGet('http://pianku.api.mgtv.com/rider/list/adapter?&src=pc&ty='.$data['id'][$vid].'&pc=100&pn=-1&st=-1'),true);				
				foreach ($json['data']['hitDocs'] as $k=>$v) {
					preg_match("/http:\/\/www.mgtv.com\/\w+\/\d+\/([0-9]+).html/", $v['url'],$src);
					$ckt_list[] = ['name'=>$v['title'].':'.$v['subtitle'],'lists'=>$this->base('s205',$src[1])];
				}  
                $base =  ['title'=>$data['title'][$vid],'base'=>$ckt_list];
            break;
            case 208:   
                $data =Http::doGet('http://z.wo0.cn/jp/jsonp/dy.json'); 
				preg_match_all('|"src":"([^"]+)","label":"([^"]+)"|u', $data,$ids); 
				$name=$ids[2];
				foreach ($ids[1] as $k=>$v) {
					$ckt_list[] = ['name'=>$name[$k],'lists'=>$v];
				}  
                $base =  ['title'=>'â����Ӱ','base'=>$ckt_list];
            break;	
			case 209: 
                $data =[
				    'id'=>['cctv1','hd_cctv1','cctv2','cctv3','cctv4','hd_cctv5','cctv6','cctv7','cctv8','cctv9','cctv10','cctv11','cctv12','cctv13','cctv14','cctv15','cctv3','cetv','bjws'.'hd_bjws','dfws','hd_dfws','tjws','cqws','hljws','hd_hljws','jlws','lnws','nxws','gsws','qhws','shxws','hebws','sxws','sdws','ahws','hd_ahws','henws','hbws','hd_hbws','hnws','jxws','jsws','hd_jsws','zjws','hd_zjws','dnws','gdws','hd_gdws','szws','gxws','ynws','gzws','xjws','lyws','htv1','htv2','htv3','htv4','yybb','cmpd','hqqg','sctx','jykt','cpd','dzty'],
					'title'=>['CCTV1�ۺ�','CCTV1�ۺ�HD','CCTV2�ƾ�','CCTV3����','CCTV4���Ĺ���','CCTV5����HD','CCTV6��Ӱ','CCTV7����ũҵ','CCTV8���Ӿ�','CCTV9��¼','CCTV10�ƽ�','CCTV11Ϸ��','CCTV12����뷨','CCTV13����','CCTV14�ٶ�','CCTV15����','CETV1�н�1̨','��������','��������HD','��������','��������HD','�������','��������','����������','����������HD','��������','��������','��������','��������','�ຣ����','��������','�ӱ�����','ɽ������','ɽ������','��������','��������HD','��������','��������','��������HD','��������','��������','��������','��������HD','�㽭����','�㽭����HD','��������','�㶫����','�㶫����HD','��������','��������','��������','��������','�½�����','��������','�����ۺ�','��������','����Ӱ��','�����ٶ�','���ű���','����Ƶ��','�������','�ղ�����','��ӥ��ͨ','��Ƶ��','��������']
				]; 
				if($vid){	
				    $vid=$vid-1;
					$base = ['title'=>$data['title'][$vid],'base'=>'rtmp://livertmp-al.wasu.cn/live1/'.$data['id'][$vid]];
				
				}else{
					foreach ($data['id'] as $k => $v) {
						$ckt_list[] = ['name'=>$data['title'][$k],'lists'=>'rtmp://livertmp-al.wasu.cn/live1/'.$v];             
					}	
					$base =  ['title'=>'����������·','base'=>$ckt_list ];
				}	
			break;	
            case 210:  //�乾ֱ����Ŀ��  vid=623899540
                $str =json_decode(Http::doGet('http://m.miguvideo.com/wap/resource/migu/detail/DetailLive_data.jsp?cid='.$vid),true);
                foreach ($str as $k=>$v){
                    $ckt_list[] = ['name'=>$v['playName'],'lists'=>url('api',['lid'=>'s210','vid'=>$v['playbillId']])];
                }
                $base =  ['title'=>'�乾ֱ����Ŀ��Ƶ','base'=>$ckt_list];
            break;	
	        case s210:  //�乾ֱ����Ŀ��  vid=62389954020170913001
                $str =json_decode(Http::doGet('http://m.miguvideo.com/wap/resource/migu/detail/DetailLiveBackSee_data.jsp?playbillId='.$vid),true);                
                $base =  $str['liveBackPlayUrl'];
				header('Location: ' . $base);
                exit();
            break;			
            case 211:  //â��ֱ��
			    $data =json_decode(file_get_contents('http://mpp.liveapi.mgtv.com/v1/epg/turnplay/getLiveAssetCategoryList?version=PCweb_1.0&platform=4&media_asset_id=TVStationAll&buss_id=2100001'),true);
                foreach ($data['data']['category'] as $k=>$v){					
					foreach ($v['channels'] as $s=>$w){
						$ckt_list[] = ['name'=>$w['name'],'lists'=>url('api',['lid'=>'s211','vid'=>$w['id']])];
					}
                }
				if($vid){
					$vid=$vid-1;
					$base = ['title'=>$ckt_list[$vid]['name'],'base'=>$ckt_list[$vid][lists]];
				}else{
					$base =  ['title'=>'â��ֱ����Ŀ��Ƶ','base'=>$ckt_list];
				}
            break;			
	        case s211: 
                $str =json_decode(file_get_contents('http://mpp.liveapi.mgtv.com/v1/epg/turnplay/getLivePlayUrlMPP?version=PCweb_1.0&platform=4&definition=std&definition=std&buss_id=2000001&definition=std&channel_id='.$vid),true);
                $data =  $str['data']['url'];
				header('Location: ' . $data);
                exit();
            break;	
	        case 212: 			    
                $title=['�պ�','����','ŷ��','����','����','����','��Ļ','����','����'];
				if($vid){	
					$baseurl='http://708porn.com/vodtypehtml/'.$vid.'.html';
				}else{
					$baseurl='http://708porn.com/';
				}	
				$vist=QueryList::Query($baseurl,[				    
					'name' => ['.text-overflow','title'],
					'lists' => ['.text-overflow>a','href','',function($content){	
						$arr = explode('/',$content); 	
						$arc = explode('.',$arr[2]); 
						return  $this->porn_api($arc[0]);
					}],				
				])->data;					
				$base =  ['title'=>'','base'=>$vist];
            break;
            case s212:               
				preg_match_all('|<script src="(.*?)"|i',Http::doGet($vid) , $src);
				preg_match("|http(.*?)'|i",Http::doGet('http://708porn.com/'.$src[1][7]) , $data);
				$base='http'.$data[1];
            break;
            case 213:			    
				$data = QueryList::Query('http://www.yusebt.com/',[
				    'title'=>['.item>a','title'],
				    'url'=>['.item>a','href','',function($content){	
						return 'http://www.yusebt.com'.$content;
					}],						
				])->data;				
				$vid=$vid-1;
				$base= QueryList::Query($data[$vid]['url'],[
				    'name'=>['.pic>img','alt'],
				    'lists'=>['.pic','href','',function($content){	
						$arr = explode('/',$content); 
						$ar = explode('.',$arr[4]);
						return Http::doGet('http://www.yusebt.com/ckplayer/video.php?wap=1&url='.$ar[0]);
					}],						
				])->data;	
				$base =  ['title'=>'Av��Ƶ -'.$data['title'][$vid],'base'=>$base];				
            break;			
            case 214:
                $data=json_decode(Http::doGet('http://open.douyucdn.cn/api/RoomApi/game'),true);
				$vid=$vid-1;
				$list=json_decode(Http::doGet('http://api.douyutv.com/api/v1/live/'.$data['data'][$vid]['cate_id']),true);
				foreach ($list['data'] as $k=>$v){					
                    $ckt_list[] = ['name'=>$v['room_name'],'lists'=> self::base('s214',$v['room_id'])];
                }				
				$base =  ['title'=>'���� - '.$list['data'][0]['game_name'],'base'=>$ckt_list];				
            break;
			case s214:
                $data=json_decode(Http::doGet('https://m.douyu.com/html5/live?roomId='.$vid),true);
				$base=$data['data']['hls_url'];
            break;
			case 215:
                $url = empty($vid)? 'http://www.jingpinzy.com/' : 'http://www.jingpinzy.com/?m=vod-type-id-'.$vid.'.html';
				$data = QueryList::Query($url,[
				    'name'=>['.l>a','text'],
				    'lists'=>['.l>a','href','',function($content){	
						$arr = explode('-',$content); 
						return self::base('s215',explode('.',$arr[3])[0]);
					}],						
				])->data;
				$base =  ['title'=>'��Ʒ��Դ  - '.$data['data'][0]['title'],'base'=>$data];	
            break;	
			case s215:
                $data=Http::doGet('http://www.jingpinzy.com/?m=vod-detail-id-'.$vid.'.html');
				preg_match('|http://hair.jingpin88.com/(.*?)index.m3u8|i',$data , $src);
				$base='http://hair.jingpin88.com/'.$src[1].'index.m3u8';
            break;
			case 216:
                $url = empty($vid)? 'http://135zy.com/' : 'http://135zy.com/?m=vod-type-id-'.$vid.'.html';
				$data = QueryList::Query($url,[
				    'name'=>['.xing_vb4>a','text'],
				    'lists'=>['.xing_vb4>a','href','',function($content){	
						$arr = explode('-',$content); 
						return self::base('s216',explode('.',$arr[3])[0]);
					}],						
				])->data;				
				$base =  ['title'=>'135��Դվ  - '.$data['data'][0]['title'],'base'=>$data];	
            break;	
			case s216:
                $data=Http::doGet('http://135zy.com/?m=vod-detail-id-'.$vid.'.html');
				preg_match('|https://v.xw0371.com/(.*?)index.m3u8|i',$data , $src);
				$base='https://v.xw0371.com/'.$src[1].'index.m3u8';
            break;	
			case 217:
                $a1='#EXTM3U' . PHP_EOL;
				$a1='#EXT-X-STREAM-INF:PROGRAM-ID=1,BANDWIDTH=1000000,RESOLUTION=1280x720' . PHP_EOL;
				$a4=
				$base='https://video.letv-cdn.com/'.$src[1].'.m3u8';
                break;	
			case 218:
			    $data=json_decode(Http::doGet('http://m.yinyuetai.com/mv/get-simple-video-info?&videoId='.$vid),true);
			    $ckt_list [] = ['name'=>$data['videoInfo']['title'],'lists'=>$this->chp_api(3,$vid)];
                $base =  ['title'=>'����̨  - '.$data['videoInfo']['title'],'base'=>$ckt_list];	
                break;
            case 301:
			    $vid= $vid ? $vid-1: 0;	
                $data = QueryList::Query('http://qcc.flash127.com/play.html',[
				    'title' => ['li>a','text'],
				    'id'=>['li>a','id']						
				])->data;
                $str =json_decode(Http::doGet('http://qcc.flash127.com/public/playlist.html?id='.$data[$vid]['id']));
                foreach ($str as $k=>$v){
                    $songlist[] = ['title'=>$v->title,'singer'=>$v->singer,'audio'=>$v->audio,'thumbnail'=>'','lyric'=>''];
                }
                $base =  ['name'=>'Qcc��������','title'=>'DJ��ȫ','base'=>$songlist,'description'=>'dj����,Qcc��������,newQccӢ��������,QQ����������,QQ�ݵ����Ƽ�,Myqzone����,Qcc��������2014,�ֳ�����,��ҡ����,�ư�CLub,��ҡ����,��������,Ӣ������','lists'=>$data];
            break; 
            case 302 :   
			    $vid= $vid ? $vid-1: 0;	
                $data = QueryList::Query('http://fm.migu.cn/',[
				    'title' => ['title','text','',function($content){	
						$arr = explode('-',$content); 							
						return  $arr[0];
					}],
				    'description'=>['meta[name=description]','content']						
				])->data;
				$ids =  QueryList::Query('http://fm.migu.cn/',[	
                 	'title' => ['.channel_item_txt','text'],		    
					'song_ids' => ['.channel_box>span','song_ids'],
					'data_log'=>['.channel_box>span','data_log'],	
				])->data; 	
				$json=json_decode(Http::doGet('http://fm.migu.cn/webfront/musicfm/list.do?ids='.$ids[$vid]['song_ids'].'&platform=0&'.$ids[$vid]['data_log']),true);				
                $msg= $json ['msg'] ;
                foreach ($msg  as $k=>$v) {	
                    $songlist[$k]= ['title'=>$v['albumname'],'singer'=>$v['singername'],'audio'=>$v['mp3'],'thumbnail'=>$v['poster'],'lyric'=>url('api',['lid'=>'l302','vid'=>$v['songid']])];
                }				
                $base =  ['name'=>$data[0]['title'],'title'=>$ids[$vid]['title'],'base'=>$songlist,'description'=>$data[0]['description'],'lists'=>$ids]; 
            break;  
            case 'l302' :
                $data=Http::doGet('http://fm.migu.cn/webfront/musicfm/lyrics.do?id='.$vid);
                $lrc=str_replace(array('\r\n','\n','/'),'',$data); 
                preg_match('|"(.*?)"|i',$lrc , $lyric);
                $base=$lyric[1];
            break;  
            case 303 :
			    $vid= $vid ? $vid-1: 0;	
			    $data = json_decode(Http::doGet('http://m.kugou.com/rank/list&json=true'),true)['rank']['list']; 
                $list= json_decode(Http::doGet('http://m.kugou.com/rank/info/?rankid='.$data[$vid]['rankid'].'&page=1&json=true'), true)['songs']['list'];
				foreach ($list as $v) {	
				    $mp3 = json_decode(Http::doGet('http://m.kugou.com/app/i/getSongInfo.php?cmd=playInfo&hash='.$v['hash']),true);
					$songlist[] = ['title'=>$v['remark'],'singer'=>$mp3['singerName'],'audio'=>url('api',['lid'=>'s303','vid'=>$v['hash']]),'thumbnail'=>url('api',['lid'=>'p303','vid'=>$v['hash']]),'lyric'=>url('api',['lid'=>'l303','vid'=>$v['hash'].'_'.$mp3['time']])];                      
				}
				foreach ($data as $pp) {	
				    $lists[] = ['id'=>$pp['rankid'],title=>$pp['rankname']];
				}
                $base = ['name'=>'�ṷ����','title'=>$data[$vid]['rankname'],'base'=>$songlist,'description'=>$data[$vid]['intro'],'lists'=>$lists];  
            break; 
            case 's303' :
                $mp3 = json_decode(Http::doGet('http://m.kugou.com/app/i/getSongInfo.php?cmd=playInfo&hash='.$vid),true);
                header('Location: ' . $mp3['url']);
                exit();
            break;			
            case 'l303' :
                $a = explode("_",$vid);
                $data=Http::doGet('http://m.kugou.com/app/i/krc.php?cmd=100&hash='.$a[0].'&timelength='.$a[1]);
                $lrc = str_replace(array("\r\n", "\n", "\r"),'',$data);
                return $lrc;
            break; 
            case 'p303' :
                $img = json_decode(Http::doGet('http://tools.mobile.kugou.com/api/v1/singer_header/get_by_hash?hash='.$vid.'&size=200&format=jsonp'),true);
                header('Location: ' . $img['url']);
                exit();
            break;			
            case 304 : //�����������б���		
			    $netease_cookie = '';
                $data1= QueryList::Query('http://music.163.com/discover/toplist',[
				    'title' => ['.name','text'],
					'id' => ['.mine','data-res-id']
				])->data;
				$data2= QueryList::Query('http://music.163.com/discover/playlist',[
				    'title' => ['.dec>a','text'],
					'id' => ['.bottom>a','data-res-id']
				])->data;				
				$dasa=array_merge($data1,$data2);
				$vid= $vid ? $vid-1: 0;	            
                $API = new Music('netease');
				$API->cookie($netease_cookie);  
				$data =json_decode($API->playlist($dasa[$vid]['id']),true);
				foreach ($data['playlist']['tracks'] as $k=>$v) {	
                    $songlist[] = ['title'=>$v['name'],'singer'=>$v['ar'][0]['name'],'audio'=>url('api',['lid'=>'s304','vid'=>$v['id']]),'thumbnail'=>$v['al']['picUrl'],'lyric'=>url('api',['lid'=>'l304','vid'=>$v['id']])];
                }
				$base =  ['name'=>'����������','title'=>$data['playlist']['name'],'coverImgUrl'=>$data['playlist']['coverImgUrl'],'base'=>$songlist,'description'=>$data['playlist']['description'],'lists'=>$dasa]; 
            break;
            case 's304' :
			    $API = new Music('netease');
                $data = json_decode($API->url($vid),true);
				header('Location: ' . $data['data'][0]['url']);
                exit();
            break;			
            case 'l304' :
                $lrc=json_decode(str_replace('\n','',Http::doGet('http://music.163.com/api/song/lyric?os=pc&id='.$vid.'&lv=-1&kv=-1&tv=-1')),true);
                $lrc = str_replace(array('\r\n', '\n', '\r'),'',$lrc);
                return $lrc['lrc']['lyric'];
            break; 
            case 305 :
			    $vid= $vid ? $vid-1: 0;	
			    $dase= QueryList::Query('http://www.xiami.com/collect/recommend',[				    
					'id' => ['.block_cover>a','href','',function($content){	
						$arr = explode('/',$content); 							
						return  $arr[2];
					}],
					'title' => ['.block_cover>a','title']
				])->data;             	
			    $API = new Music('xiami');
				$data = json_decode($API->playlist($dase[$vid]['id']), true);
				foreach ($data['data']['songs'] as $k=>$v) {	
                    $songlist[] = ['title'=>$v['song_name'],'singer'=>$v['artist_name'],'audio'=>$v['listen_file'],'thumbnail'=>$v['album_logo'],'lyric'=>$v['lyric']];
                }
				$base =  ['name'=>'Ϻ������','title'=>$data['data']['collect_name'],'coverImgUrl'=>$data['data']['logo'],'base'=>$songlist,'description'=>$data['data']['user_name'],'lists'=> $dase]; 
            break;
            case 306 :
			    define('NO_HTTPS', true);  
			    $vid= $vid ? $vid-1: 0;	
			    $dase= QueryList::Query('http://music.baidu.com/songlist',[				    
					'id' => ['.text-title>a','href','',function($content){	
						$arr = explode('/',$content); 							
						return  $arr[2];
					}],
					'title' => ['.text-title>a','title']
				])->data; 
			    $API = new Music('baidu');
				$data = json_decode($API->playlist($dase[$vid]['id']), true);					
				foreach ($data['content'] as $k=>$v) {
					$json=json_decode($API->url($v['song_id']),true);
                    $songlist[] = ['title'=>$v['title'],'singer'=>$v['author'],'audio'=>str_replace('http://yinyueshiting.baidu.com', 'https://gss0.bdstatic.com/y0s1hSulBw92lNKgpU_Z2jR7b2w6buu', $json['data']['songList'][0]['songLink']),'thumbnail'=>$json['data']['songList'][0]['songPicSmall'],'lyric'=>$json['data']['songList'][0]['lrcLink']];
                }
				$base =  ['name'=>'�ٶ�����','title'=>$data['title'],'coverImgUrl'=>$data['pic_w700'],'base'=>$songlist,'description'=>$data['desc'],'lists'=> $dase]; 
            break;
           case 307 :	
                $vid= $vid ? $vid-1: 0;		   
			    $API = new Music('tencent');
				$data = json_decode($API->playlist($vid), true);	
				foreach ($data['data']['cdlist'][0]['songlist'] as $k=>$v) {
                    $songlist[] = ['title'=>$v['title'],'singer'=>$v['singer'][0]['name'],'audio'=>'http://ws.stream.qqmusic.qq.com/'.$v['id'].'.m4a?fromtag=46','thumbnail'=>$data['data']['cdlist'][0]['logo'],'lyric'=>''];
                }
				$base =  ['name'=>'QQ����','title'=>$data['data']['cdlist'][0]['dissname'],'coverImgUrl'=>$data['data']['company_new']['headPic'],'base'=>$songlist,'description'=>$data['data']['desc']]; 
            break;	         
            case 308 :   
			    $vid= $vid ? $vid-1: 0;	
                $data = ['id'=>[0,1,2,3,5,6,7],'title'=>['�����¸��','�����ȸ��','�����պ���','����ŷ����','���Ҿ����','�ٶ��ȸ��','�ٶ��¸��']];	
                $json =	json_decode($this->trimall(Http::doGet('http://player.kuwo.cn/webmusic/gu/getwebbang?syId='.$data['id'][$vid])),true);	
				foreach ($json['list'] as $k=>$v) {
					$songlist[] = ['title'=>$v['name'],'singer'=>$v['art'],'album'=>$v['album'],'audio'=>url('api',['lid'=>'s308','vid'=>$v['rid']])];
                }	
				foreach ($data['title'] as $x=>$y) {
					$a=$x+1;
					$lists[] = ['title'=>$y,'id'=>$a];
                }
                $base =  ['name'=>'��������̨','title'=>$data['title'][$vid],'base'=>$songlist,'description'=>$data['title'][$vid],'lists'=>$lists]; 
            break;
            case s308 :    
                $base=Http::doGet('http://antiserver.kuwo.cn/anti.s?rid='.$vid.'&format=mp3&type=convert_url&response=url','http://www.kuwo.cn/yinyue/'.$vid);
				header('Location: ' . $base);
                exit();
            break;	
            case 309 : 
			    $vid= $vid ? $vid-1: 0;	 			
                $data = ['id'=>[27517,27518,27519,27520,27521],'title'=>['��������','��������','���Ը�Ц','DJ����','ŷ���պ�']];	
                $json =	json_decode(Http::doGet('http://iring.diyring.cc/contentv3/getrootprogcontent?tp=2051ea572a1486df&&pi=1&pno='.$data['id'][$vid]),true);	
				$list = empty($json['model']['list'][0]['AssResBoxId']) ? $json['model']['list'][1]['ProgContent'] : $json['model']['list'][0]['ProgContent'];	
				foreach ($list as $k=>$v) {
					$songlist[] = ['title'=>$v['WorksName'],'singer'=>$v['Singer'],'audio'=>$v['WorksFileUrl']];
                }
				foreach ($data['title'] as $x=>$y) {
					$a=$x+1;
					$lists[] = ['title'=>$y,'id'=>$a];
                }
                $base =  ['name'=>'�ֻ�����','title'=>$data['title'][$vid],'base'=>$songlist,'description'=>$data[0]['description'],'lists'=>$lists]; 
            break;	
            case 310 : 	
			    $vid= $vid ? $vid-1: 0;	
                $data = ['��������','��������','���н���','Ӱ�ӹ��','������Ϸ','��Ϣ����','DJ����','�������','�����ϴ�','��������'];
				$songlist = QueryList::Query('http://www.shoujiduoduo.com/ring.php?type=getlist&listid='.$vid.'&page=0&pagesize=200',[
					'title' => ['ring','name'],
					'thumbnail' => ['ring','head_url'],
					'singer'=>['ring','artist'],
					'audio'=>['ring','mp3url','',function($mp3url){										
						return  'http://cdnringbd.shoujiduoduo.com'.$mp3url;
					}],				
				])->data;	
				foreach ($data as $x=>$y) {
					$a=$x+1;
					$lists[] = ['title'=>$y,'id'=>$a];
                }				
                $base =  ['name'=>'�������','title'=>'������� - '.$data[$vid],'base'=>$songlist,'description'=>$data[$vid],'lists'=>$lists]; 
            break;
	        case 311 : 
			    $vid= $vid ? $vid-1: 0;	 			
                $data = ['id'=>[1,2,3,4,5,6,7,8,9],'title'=>['��Ц����','��������','��������','�������','DJ����','��������','�������','�պ�����','ŷ������']];	               	
				$songlist = QueryList::Query('http://m.haolingsheng.com/lingsheng/'.$data['id'][$vid].'/',[
					'title' => ['.list>li>a>span','text'],
					'singer'=>['.list>li>span','text'],
					'audio'=>['.list>li>a','href','',function($content){
						$arr=explode('/',$content); $ar=explode('.',$arr[2]); 
						return url('api',['lid'=>s311,'vid'=>$ar[0]]);
					}],				
				])->data;	
				foreach ($data['title'] as $x=>$y) {
					$a=$x+1;
					$lists[] = ['title'=>$y,'id'=>$a];
                }
                $base =  ['name'=>'������','title'=>'������ - '.$data['title'][$vid],'base'=>$songlist,'description'=>$data[0]['description'],'lists'=>$lists]; 
            break;		
            case s311 :  
			   $data=QueryList::Query('http://m.haolingsheng.com/lingsheng/'.$vid.'.html',[
					'src' => ['.dbtn','href'],
				])->data;
				header('Location: ' . $data[0]['src']);
                exit();
            break;
	        case 312 : 
			    $vid= $vid ? $vid-1: 0;	 			
                $data = ['id'=>['gaoxiao-5','gexing-7','duanxin-6','jingdian-8','yingshi-11','guanggao-12','yingwen-13','zuhe-17','rihan-14','nansheng-15','nvsheng-16','DJ-9'],'title'=>['��Ц�������а�','�����������а�','�����������а�','�����������а�','Ӱ���������а�','����������а�','Ӣ���������а�','����������а�','�պ��������а�','�и������а�','Ů�������а�','DJ�������а�']];	               	
				$songlist = QueryList::Query('http://m.51lingsheng.com/'.$data['id'][$vid].'.html',[
					'title' => ['.ringtop>li>a','title'],
					'audio'=>['.ringtop>li>a','href','',function($content){
						$arr=explode('/',$content); $ar=explode('.',$arr[2]); 
						return url('api',['lid'=>s312,'vid'=>$ar[0]]);
					}],				
				])->data;					
				foreach ($data['title'] as $x=>$y) {
					$a=$x+1;
					$lists[] = ['title'=>$y,'id'=>$a];
                }
                $base =  ['name'=>'�Ұ�������','title'=>'�Ұ������� - '.$data['title'][$vid],'base'=>$songlist,'description'=>$data[0]['description'],'lists'=>$lists]; 
            break;		
            case s312 :  
			   $data=QueryList::Query('http://m.51lingsheng.com/ringmp3/'.$vid.'.html',[
					'src' => ['.downaddress>a','href','',function($content){
						return 'http://m.51lingsheng.com'.$content;
					}],				
				],'','utf-8')->data;
				header('Location: ' . $data[0]['src']);
                exit();
            break;	
            case 313 : 
			    $vid= $vid ? $vid-1: 0;	 			
                $data = ['id'=>['Kushiyaki','chinese','foreign','recommend','free','remix','car_songdownkwl'],'title'=>['��������','��������','��������','�Ƽ�����','�������','ԭ������','����DJ����']];
				$url='http://www.djkk.com/dance/sort/'.$data['id'][$vid].'_1.html';
                $songlist = QueryList::Query($url,[
					'title' => ['.aleft','text'],
					'singer'=>['.source a','text'],
					'description'=>['meta[name=Description]','content'],
					'audio'=>['.cbox>input','value','',function($content){
						return url('api',['lid'=>s313,'vid'=>$content]);
					}],				
				],'','utf-8')->data;		
				foreach ($data['title'] as $x=>$y) {
					$a=$x+1;
					$lists[] = ['title'=>$y,'id'=>$a];
                }
                $base =  ['name'=>'DJ������','title'=>$data['title'][$vid],'base'=>$songlist,'description'=>$songlist['description'],'lists'=>$lists]; 
            break;	
            case s313 :    
                $data=Http::doGet('http://www.djkk.com/dance/play/'.$vid.'.html');
				preg_match('|m4a:(.*?)"|U', $data,$src);
				$arr = explode('"',$src[1]); 
                $src=	'http://mx.djkk.com/mix'.$arr[1];			
				header('Location: ' . $src);
                exit();
            break;	
	        case 314 : 
			    $vid= $vid ? $vid-1: 0;	 			
                $data = ['id'=>['myxc','xc','zw','club','ywclub','my','yw','jy'],'title'=>['��ҡ����','�ֳ�����','��������','�ư�Club','Ӣ��Club','��ҡ����','Ӣ��Disco','��������']];
				$url='http://mobi.dj97.com/'.$data['id'][$vid];
                $songlist = QueryList::Query($url,[
					'title' => ['.list_border>li>a','title'],
					'audio'=>['.list_border>li>a','href','',function($content){
						$arr=explode('/',$content); 
						if($arr[2]){	
							return url('api',['lid'=>s314,'vid'=>$arr[2]]);
						}
					}],				
				])->data;
				foreach ($data['title'] as $x=>$y) {
					$a=$x+1;
					$lists[] = ['title'=>$y,'id'=>$a];
                }
                $base =  ['name'=>'DJ������','title'=>$data['title'][$vid],'base'=>$songlist,'description'=>$songlist['description'],'lists'=>$lists]; 
            break;	
            case s314 :    
                $data=QueryList::Query('http://mobi.dj97.com/m/'.$vid,[
					'src' => ['audio','src'],
				])->data;				
				header('Location: ' . $data[0]['src']);
                exit();
            break;		
            case 400 :
				$data = [
				    ['name'=>'vip����','api'=>'http://www.a305.org/x3/tong.php?v='],
				    ['name'=>'С������','api'=>'https://api.daidaitv.com/index/?url='],
					['name'=>'Ʒ�Ž���','api'=>'https://api.daidaitv.com/index/?url='],
					['name'=>'�ٶ�ţ����','api'=>'http://api.haitian.tv/vip/?url='],
					['name'=>'��Ƭ����','api'=>'http://api.nepian.com/ckparse/?url='],	
					['name'=>'OFFLV����','api'=>'https://api.vparse.org/?url='],				
					['name'=>'ʯͷ����','api'=>'http://jx.maoyun.tv/index.php?id='],
					['name'=>'�������','api'=>'http://api.xfsub.com/index.php?url='],
					['name'=>'�ƽ���','api'=>'https://api.daidaitv.com/index/?url='],
					['name'=>'Sup����','api'=>'http://player.jidiaose.com/supapi/iframe.php?v=']		
				];
				$tags ='acfun,sohu,ku6,iqiyi,youku,sina,tudou,letv,leyun,bilibili,wasu,56,cntv,fun,mgtv,pptv,ppyun,qq,tangdou,vlook,waqu,aipai,yinyuetai,youmi,m1905,miaopai,meipai,6cn,163,baofeng,baomihua,huya,huyazb,ifeng,longzhu,yy,yyzb,kuaishou,zhibo,douyu';
				$keywords='';
		        $base =  ['title'=>'VIP���ܽ����ӿ�','base'=>$data,'tags'=>$tags];
            break;	
            case 401 : 
			    $vid= $vid ? $vid: 1;	
                $data= QueryList::Query('http://list.youku.com/category/show/c_96_a_%E5%A4%A7%E9%99%86_s_6_d_1_p_'.$vid.'.html',[
					'title' => ['.p-thumb>a','title'],
					'url'=>['.p-thumb>a','href' ,'',function($content){
						$arr=explode('//',$content); 
						return 'http://'.$arr[1];
						
					}],
				])->data;
                $base =  ['name'=>'�ſ��ӰƵ��','title'=>$data['title'][$vid],'description'=>$songlist['description'],'lists'=>$data]; 
            break;		
		    case 501 :		
                $head = QueryList::Query('http://www.wlds.net/tvlist/epg.asp?tid='.$vid,[
					'title' => ['img','title'],
					'img'=>['img','src'],
					'date' => ['.epgdate','text'],
				])->data;
			    $epg = QueryList::Query('http://www.wlds.net/tvlist/epg.asp?tid='.$vid,[
				    'am'=>['li>span','text'],
					'name'=>['li>i','text'],
				],'','UTF-8','GB2312',true)->data;
				 $content = QueryList::Query('http://www.wlds.net/tvlist/'.$vid.'.html',[
					'content' => ['.tvintro_content','text'],
				],'','UTF-8','GB2312',true)->data;
				$base = ['title'=>$head[0]['title'],'content'=>$content[0]['content'],'img'=>$head[0]['img'],'date'=>$head[0]['date'],'epg'=>$epg];
            break;	
			case 601:
			    $vid=$vid-1;
			    $json=[
				        ['name'=>'����','id'=>'hg'],
						['name'=>'�պ�','id'=>'aq'],
						['name'=>'��̨','id'=>'gt'],
						['name'=>'ŷ��','id'=>'js'],
						['name'=>'����','id'=>'qt'],
					];					
                $data = QueryList::Query('http://www.6080xsjue.com/html/'.$json[$vid]['id'].'/list_1.html',[
				    'name' => ['.movie_name','text'],
				    'lists'=>['.movie_name','href','',function($content){							 						      
						return   $this->base('s219',$content);
					}]						
				])->data;
				$base =  ['title'=>'API  - '.$json[$vid]['name'],'base'=>$data];	
                break;	
            case s601: 
                $url =Http::doGet($vid);
                preg_match('|<iframe src="(.*?)"|i', $url, $vid);	
                $base = $vid[1];  
                break;			
            default:return null;
        } 		
        return $base;        
    }
    private function cmp_xml($data)
    {
        header("Content-type:text/xml;charset=utf-8");
        $xml  = '<list>' . PHP_EOL . $data. '</list>';
        return $xml;
    }
	private function trimall($str){
		$qian=array("'");$hou=array('"');
		return str_replace($qian,$hou,$str);    
	} 
}
   
