<?php
namespace app\home\model;

use think\Model;

class Ads extends Model
{
     // 设置当前模型对应的完整数据表名称
    
     protected $_validate=array(
        array('ads_name','require','广告标识必须填写！',1,'',1),
        array('ads_name','','该广告标识已经存在,请重新填写一个广告标识！',1,'unique',1),
     );
     protected $_auto=array(
        array('ads_name','trim',3,'function'),
        array('ads_content','trim',3,'function'),
        array('ads_content','stripslashes',3,'function'),
     );
}