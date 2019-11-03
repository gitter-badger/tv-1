<?php
/*   
 * echo json_encode(MiaoPai::parse($url));        
 */
namespace ZhiBo;
class MiaoPai  
{  
    public static function parse($url)  
    {  
        $content = self::curl($url);  
        preg_match('#"videoSrc":"(.*?)",#',$content,$vurl);  
        preg_match('#"poster":"(.*?)"#',$content,$_img);  
        $videoinfo["poster"] = $_img[1];  
        $videoinfo['code'] = 200;  
        $videoinfo["data"]["url"] = $vurl[1];  
        $videoinfo["play"] = "h5mp4";  
        return $videoinfo;  
    }  
    public static function curl($url)  
    {  
        $params["ua"] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";  
        return GlobalBase::curl($url,$params);  
    }  
}  