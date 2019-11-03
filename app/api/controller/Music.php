<?php
namespace app\api\controller;
use app\BaseController;
use think\facade\Cache;
use app\facade\Music as Musc;
use app\facade\Http;


class Music extends BaseController
{
    public function Nbk($source='netsas',$type,$id="",$count=20,$pages=1,$callback=''){
    $api=Musc::site($source)->format(true);
        $cache = Cache::get('MusicApi_'.$source.'_'.$type.'_'.$id);
        if($cache){
            $data= $cache;
        }else{
            switch ($type) {
                case 'search': $data = json_decode($api->search($id,['limit'=>$count,'page'=>$pages]), true);break;
                case 'song': $data = json_decode($api->song($id), true);break;
                case 'album':$data = json_decode($api->album($id), true);break;
                case 'playlist':$data = json_decode($api->playlist($id), true);break;
                case 'pic':$data = json_decode($api->pic($id), true);break;
                case 'url':$data = json_decode($api->url($id), true);break;
                case 'lyric':$data = json_decode($api->lyric($id), true);break;
                case 'download':$data = json_decode($api->url($id), true)['url'];break;
                case 'userlist':$data = json_decode(Http::doGet('http://music.163.com/api/user/playlist/?offset=0&limit=1001&uid='.$id), true);break;
            }
            Cache::set('MusicApi_'.$source.'_'.$type.'_'.$id,$data);
        } 
    if($callback){
      return jsonp($data)->options(['default_jsonp_handler' => $callback,]);
    }   
    return $data ;               
  }
  public function Tam($type,$id=''){
        $cache = Cache::get('TaiheFm_'.$type.'_'.$id);
    if($cache){
      $data= $cache;
        }else{      
      switch ($type) {          
        case 'channel':
          $url=json_decode(Http::doGet('http://fm.taihe.com/dev/api/?tn=channellist'), true);               
          foreach($url['channel_list'] as $key =>$channels) {
            $data[$key]['channel_id']   =   $channels['channel_id'];
            $data[$key]['channel_name'] =   $channels['channel_name'];
            $data[$key]['cover_big']    =   'http://cloud.hunger-valley.com/music/'.$channels['channel_id'].'.jpg-big';
            $data[$key]['cover_middle'] =   'http://cloud.hunger-valley.com/music/'.$channels['channel_id'].'.jpg-middle';  
            $data[$key]['cover_small']  =   'http://cloud.hunger-valley.com/music/'.$channels['channel_id'].'.jpg-small'; 
          }
          break;
        case 'playlist':                
          $url=json_decode(Http::doGet('http://fm.taihe.com/dev/api/?tn=playlist&id='.$id), true);              
          foreach($url['list'] as $key =>$song) {
            $songData=json_decode(Http::doGet('http://music.taihe.com/data/music/links?songIds='.$song['id']), true)['data']['songList'];                   
            $data[$key]['songId']   =  $song['id'];
            $data[$key]['songName'] = $songData[0]['songName'];
            $data[$key]['songPicSmall'] = $songData[0]['songPicSmall'];
            $data[$key]['songPicBig']   =   $songData[0]['songPicBig'];
            $data[$key]['songPicRadio'] =   $songData[0]['songPicRadio'];
            $data[$key]['artistName']   =   $songData[0]['artistName'];  
            $data[$key]['lrcLink']  =   $songData[0]['lrcLink'];
            $data[$key]['songLink'] =   $songData[0]['songLink']; 
          }
          break;        
      }
      Cache::set('TaiheFm_'.$type.'_'.$id,$data);
    }           
    return  $data ;
  }
    public function Kug($type,$id=''){
        $api=Musc::site('kugou')->format(true);
        $cache = Cache::get('Kugou_'.$type.'_'.$id);        
        if($cache){
            $data = $cache;
        }else{
            switch ($type) {
                case 'top': $data = [['id'=>'new_songs','name'=>'新歌'],['id'=>'rank_list','name'=>'排行'],['id'=>'plist','name'=>'歌单'],['id'=>'singer','name'=>'歌手']];break;
                case 'new_songs': $data = json_decode(Http::doGet("http://m.kugou.com/&json=true"), true)['data'];break;
                case 'rank_list': $data = json_decode(Http::doGet("http://m.kugou.com/rank/list&json=true"), true)['rank']['list'];break;
                case 'rank_info_list': $data = json_decode(Http::doGet("http://m.kugou.com/rank/info/{$id}&json=true"), true)['info'];break;
                case 'plist': $data = json_decode(Http::doGet("http://m.kugou.com/plist/index&json=true"), true)['plist']['list']['info'];break;
                case 'plist_list': $data = json_decode(Http::doGet("http://m.kugou.com/rank/list/{$id}&json=true"), true)['rank']['list'];break;
                case 'singer': $data = json_decode(Http::doGet("http://m.kugou.com/singer/class&json=true"), true)['list'];break;
                case 'singer_list': $data = json_decode(Http::doGet("http://m.kugou.com/singer/list/{$id}&json=true"), true);break;
                case 'singer_info': $data = json_decode(Http::doGet("http://m.kugou.com/singer/info/{$id}&json=true"), true);break;
                case 'song_info': $data = json_decode(Music::site('kugou')->format(true)->url($id), true)['url'];break;         
                case 'search': $data = json_decode(Http::doGet("http://mobilecdn.kugou.com/api/v3/search/song?format=json&keyword={$id}&page=1&pagesize=20&showtype=1"), true)['data']['info'];break; 
                case 'url': $data = json_decode($api->url($id), true)['url'];break;
                case 'urls': $data = json_decode(Http::doGet("http://m.kugou.com/app/i/mv.php?cmd=100&hash=".$id."&ismp3=1&ext=mp4"), true);break;
                        
            }
            Cache::set('Kugou_'.$type.'_'.$id,$data);
        }
    return json($data);
  } 
    public function Diy($type,$id=''){
        $cache = Cache::get('diy_'.$type.'_'.$id);
        if($cache){
            $data = $cache;
        }else{
            switch ($type) {                
                case 'playlist':$url=json_decode(Http::doGet('http://iring.diyring.cc/contentv3/getrootprogcontent?tp=2051ea572a1486df&&pi=1&pno='.$id),true);$data=$url['model']['list'][1]['ProgContent'];break;
            } 
            Cache::set('diy_'.$type.'_'.$id,$data);
        }   
        return $data;  
    }
    public function Mgu($type,$id=''){
        $cache = Cache::get('Mgu_'.$type.'_'.$id);
        if($cache){
            $data = $cache;
        }else{      
            switch ($type) {
                case 'url':$data=json_decode(Http::file_get_contents('http://m.10086.cn/migu/remoting/cms_detail_tag?cid='.$id,'http://m.10086.cn'),true)['data'];break;
                case 'search':$data=json_decode(Http::file_get_contents('http://m.10086.cn/migu/remoting/scr_search_tag?type=2&keyword='.$id,'http://m.10086.cn'),true)['musics'];break;
                case 'playlist':$data=json_decode(Http::doGet('http://music.migu.cn/v3/api/music/audioPlayer/songs?type=3&playListId='.$id),true)['contentList'];break;
                case 'lyric':$data=json_decode(Http::doGet('http://music.migu.cn/v3/api/music/audioPlayer/getLyric?copyrightId='.$id),true)['lyric'];break;
                case 'pic':$data=json_decode(Http::doGet('http://music.migu.cn/v3/api/music/audioPlayer/getSongPic?songId='.$id),true);break;
            } 
            Cache::set('Mgu_'.$type.'_'.$id,$data);
        }
        return $data; 
    }   
    public function Duo($type,$id){
        $cache = Cache::get('Duo_'.$type.'_'.$id);
        if($cache){
            $data = $cache;
        }else{     
            switch ($type) {
                case 'channel': $data = ['最热铃声','最新铃声','流行金曲','影视广告','动漫游戏','信息短信','DJ舞曲','搞怪另类','网友上传','其它另类'];break;
                case 'playlist':
                    $url=simplexml_load_file('http://www.shoujiduoduo.com/ring.php?type=getlist&listid='.$id.'&page=0&pagesize=200');
                    foreach($url->children() as $period) {                   
                         $json[] = get_object_vars($period);
                    }
                    $data=stripslashes(str_replace('/ringres','http://cdnringbd.shoujiduoduo.com/ringres',json_encode($json)));
                    break;
            }
            Cache::set('Duo_'.$type.'_'.$id,$data);
        }   
        return $data;  
    }
    public function Qfm($type,$id=''){
       
        $cache = Cache::get('Qfm_'.$type.'_'.$id);
        if($cache){
            $data = $cache;
        }else{     
            switch ($type) {
                case 'channels':$data=$data=json_decode(Http::doGet('http://i.qingting.fm/capi/poplist?listType=channels'),true)['data'];break;
                case 'channel': $data=$data=json_decode(Http::doGet('http://d.qingting.fm/capi/channel/'.$id),true)['data'];break;
                case 'hotkeywords': $data=$data=json_decode(Http::doGet('http://i.qingting.fm/wapi/search/hotkeywords'),true)['data'];break;
                case 'search':  $data=$data=json_decode(Http::doGet('http://i.qingting.fm/wapi/search?k='.$id),true)['data']['data'][1]['doclist']['docs'];break;
            }
            Cache::set('Qfm_'.$type.'_'.$id,$data);
        }   
        return $data;  
    }
    public function Xim($type,$id='',$page =1){
    
        $cache = Cache::get('Qfm_'.$type.'_'.$id);
        if($cache){
            $data = $cache;
        }else{     
            switch ($type) {
                case 'categories':  $data=$data=json_decode(Http::doGet('http://mobile.ximalaya.com/mobile/discovery/v2/categories?channel=and-d8&device=android&picVersion=11&scale=2&version=5.4.45'),true)['list'];break;
                case 'hot': $data=$data=json_decode(Http::doGet('http://mobile.ximalaya.com/mobile/discovery/v3/recommend/hotAndGuess?device=android&version=5.4.45'),true);break;
                case 'search':  $data=$data=json_decode(Http::doGet('http://search.ximalaya.com/front/v1?kw='.$id.'&core=all&rows=10&page='.$page),true);break;
                case 'url': $data=$data=json_decode(Http::doGet('http://mobile.ximalaya.com/v1/track/ca/playpage/'.$id),true)['trackInfo'];break;
            }
            Cache::set('Qfm_'.$type.'_'.$id,$data);
        }   
        return $data;  
    }   
}