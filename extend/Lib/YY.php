<?php
/*  
 *$url = "http://www.yy.com/sv/9221285915528344768";  
 * $url = "http://www.yy.com/shenqu/play/id_1134094401301923854.html"; 
 * echo json_encode(YY::parse($url));           
 */
namespace ZhiBo;
class YY  
{  
    public static function parse($url)  
    {  
        $content = self::curl($url);  
  
        if(strstr($url,"/shenqu/play/")==true){  
            preg_match('#snapshot":"(.*)","conv#',$content,$img);  
            preg_match('#worksUrl":"(.*)","likeCou#',$content,$vurl);  
            preg_match('#worksName":"(.*).","singerPho#',$content,$name);  
           // print_r('1:'.$name);exit;  
            $pic = "http:".$img[1];  
            $type = 'h5mp4';  
        }else if(strstr($url,"/sv/")==true){  
            preg_match('#window.resid = \'(.*)\';#',$content,$id);  
            preg_match('#window.owneruid = \'(.*)\';#',$content,$uid);  
  
            $url = "http://api-tinyvideo-web.yy.com/tinyVideo/getDetailsForVideo?appId=svwebpc&sign=&data=%7B%22resid%22%3A%22".$id['1']."%22%2C%22uid%22%3A1%7D&_=".GlobalBase::getMillisecond();  
            $data = self::curl($url,'http://www.yy.com/sv/'.$id['1']);  
            $data = json_decode($data,true);  
            $data = $data['data']['data'];  
            $vurl['1'] = $data['resurl'];  
            $pic = $data['snapshoturl'];  
            $type = 'h5mp4';  
            //print_r($data);exit;  
  
        }else if(strstr($url,"/x/")==true){  
            preg_match('#video:\s{0,}"(.*)",#',$content,$vurl);  
            preg_match('#title:\s{0,}"(.*)",#',$content,$name);  
            preg_match('#pic:\s{0,}"(.*)",#',$content,$img);  
            //print_r('3:'.$name);exit;  
            $pic = $img[1];  
            $type = 'hls';  
        }  
  
        $mp4 =  str_replace('\u002F','/',$vurl[1]);  
        $videoinfo['code'] = 200;  
        $videoinfo["poster"]= $pic;  
        $videoinfo['play'] = $type;  
        $videoinfo["data"]["url"] = $mp4;  
        return $videoinfo;  
    }  
    public static function curl($url,$ref='')  
    {  
        $params["ua"] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";  
        if ($ref) {  
            $params['ref'] = $ref;  
        }  
        return GlobalBase::curl($url,$params);  
    }  
}  