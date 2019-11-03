<?php
/*   
 * echo json_encode(MeiPai::parse($url));        
 */
namespace ZhiBo;
class MeiPai  
{  
    public static function parse($url){  
        $content = self::curl($url);  
        preg_match('#<title>(.*?)</title>#',$content,$_title);  
        preg_match('#<meta content="(.*?)" property="og:image">#',$content,$_img);  
        preg_match('#data-video="(.*?)"#',$content,$_video);  
        $vurl = self::decode($_video[1]);  
  
        $videoinfo["poster"] = $_img[1];  
        $videoinfo['code'] = 200;  
        $videoinfo["data"]["url"] = $vurl;  
        $videoinfo["play"] = "h5mp4";  
        return $videoinfo;  
    }  
    public static function decode($string){  
        $hex = str_split(hexdec(implode(array_Reverse(str_split(substr($string,0,4))))));  
        $splt1 = $hex[0];  
        $size1 = $hex[1];  
        $size2 = $hex[3];  
        $str = substr($string,4);  
        $first = substr($str,0,$splt1).substr($str,$splt1+$size1);  
        $splt2 = strlen($first) - ($hex[2]) - $size2;  
        $second = substr($first,0,$splt2).substr($first,$splt2+$size2);  
        return base64_decode($second);  
    }  
    public static function curl($url)  
    {  
        $params["ua"] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";  
        return GlobalBase::curl($url,$params);  
    }  
}  