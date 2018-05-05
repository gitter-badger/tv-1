<?php
/*       
 * @author     Mrlv(315141428@qq.com)
 * @copyright  上海比良网络科技有限公司
 * @Created    2017-11-26
 */

namespace addons\weshop\model;
use think\Model;

class Payment extends Model
{

    public function getList($where=[],$order='id DESC',$row=10){
       return $this->where($where)
            ->order($order)
            ->paginate($row);
    }
}