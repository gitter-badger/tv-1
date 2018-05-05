<?php
/*
 * 法物流通
 * @author     Mrlv(315141428@qq.com)
 * @copyright  上海比良网络科技有限公司
 * @Created    2017-11-26
 */
namespace addons\weshop\controller;
use addons\weshop\model\Weshop_banner;
use addons\weshop\model\Weshop_goods;
use addons\weshop\model\Weshop_type;
use addons\weshop\model\Weshop_order;
use addons\weshop\model\Weshop_car;
use addons\weshop\model\Weshop_address;
use addons\weshop\model\Weshop_comment;
use addons\weshop\model\Mp_friends;
use addons\weshop\model\Mp_vip;
use think\Db;
use think\facade\Request;
use think\facade\Session;
use app\common\model\Payment;
class Index extends Common
{
    public function initialize()
    {
        parent::initialize();
    }    
    /*
     * 登录
     * 如果是页面请求，先判断验证码，再根据用户openid和公众号id查询用户信息，
     * 更新用户手机号码，同时生成会员信息
     */
    public function login(){
        if(Request::isAjax()){  
            if($this->openid){
                $input = input();
                if(!$input['code']){
                    return ajaxMsg(-1,'请输入验证码');
                }
                $tempCode = Session::get($input['phone'].'_code');
                if($tempCode != $input['code']){
                    return ajaxMsg(-1,'验证码错误');
                }
                $model = new Mp_friends();
                $vipmodel = new Mp_vip();
                $fansRow = $model->where(['openid'=>$this->openid,'mpid'=>$this->mid])->find();
                if(!$fansRow){
                    return ajaxMsg(-2,'请刷新页面重新登录');
                }else{
                    $model->where(['openid'=>$this->openid,'mpid'=>$this->mid])->update(['mobile'=>$input['phone']]);
                    $viprow = $vipmodel->where(['fid'=>$fansRow['id'],'mpid'=>$this->mid])->find();
                    if(!$viprow){
                        $invite_number = $this->getInviteNumber();
                        $vipmodel->insert(['fid'=>$fansRow['id'],'invite_number'=>$invite_number,'mpid'=>$this->mid]);
                    }       
                    Session::clear($input['phone'].'_code');             
                    return ajaxMsg(1,'登录成功');
                }
            }else{
                //游客身份
                return ajaxMsg(-2,'请刷新页面重新登录');
            }
        }else{
            if($this->openid){
                $model = new Mp_friends();
                $fansRow = $model->join('rh_mp_vip','rh_mp_vip.fid = rh_mp_friends.id')->where(['rh_mp_friends.openid'=>$this->openid,'rh_mp_friends.mpid'=>$this->mid])->find();
                if($fansRow['mobile']){
                    $this->redirect('/app/weshop/index/index/mid/'.$this->mid);
                }
            }
        }
        $this->fetch();
    }
    /*************个人中心start**************/
    /*
     * 个人中心
     */
    public function account(){        
        if($this->openid){            
            $model = new Mp_friends();
            $fansRow = $model->join('rh_mp_vip','rh_mp_vip.fid = rh_mp_friends.id')->where(['rh_mp_friends.openid'=>$this->openid,'rh_mp_friends.mpid'=>$this->mid])->find();
            if(!$fansRow['mobile']){
                $this->redirect('/app/weshop/index/login/mid/'.$this->mid);
            }
            $this->assign('fansRow',$fansRow);
        }else{
            $this->redirect('/app/weshop/index/login/mid/'.$this->mid);
        }        
        $this->fetch();
    }
    /*
     * 个人信息详情页
     * 如果是页面请求，先判断字段是否为空，再根据用户openid和公众号id查询用户信息，
     * 如果用户更新手机号码，判断验证码，然后跟新用户信息
     * 如果用户手机号是空的，跳转到登录页面绑定手机
     */
    public function info(){
        if(Request::isAjax()){
            if($this->openid){
                $input = input();       
                if(!$input['birthday']){
                    return ajaxMsg(-3,'请选择出生年月');
                }         
                $model = new Mp_friends();
                $vipmodel = new Mp_vip();
                $fansRow = $model->join('rh_mp_vip','rh_mp_vip.fid = rh_mp_friends.id')->where(['rh_mp_friends.openid'=>$this->openid,'rh_mp_friends.mpid'=>$this->mid])->field('rh_mp_friends.*')->find();
                if(!$fansRow){
                    return ajaxMsg(-2,'请刷新页面重新登录');
                }else{
                    if($input['mobile'] != $fansRow['mobile']){
                        $tempCode = Session::get($input['mobile'].'_code');
                        if($tempCode){
                            if($tempCode != $input['code']){
                                return ajaxMsg(-1,'验证码错误');
                            }
                        }
                    }
                    $model->where(['openid'=>$this->openid,'mpid'=>$this->mid])->update(['mobile'=>$input['mobile'],'sex'=>$input['sex'],'nickname'=>$input['nickname']]);
                    $vipmodel->where(['fid'=>$fansRow['id'],'mpid'=>$this->mid])->update(['birthday'=>$input['birthday'],'card_number'=>$input['card_number']]);
                    return ajaxMsg(1,'保存成功');
                }
            }else{
                //游客身份
                return ajaxMsg(-2,'请刷新页面重新登录');
            }
        }else{
            if($this->openid){
                $model = new Mp_friends();
                $fansRow = $model->join('rh_mp_vip','rh_mp_vip.fid = rh_mp_friends.id')->where(['rh_mp_friends.openid'=>$this->openid,'rh_mp_friends.mpid'=>$this->mid])->find();
                if(!$fansRow['mobile']){
                    $this->redirect('/app/weshop/index/login/mid/'.$this->mid);
                }
                $this->assign('fansRow',$fansRow);
            }else{
                $this->redirect('/app/weshop/index/login/mid/'.$this->mid);
            }        
            $this->fetch();
        }        
    }
    /*
     * 订单页面
     */
    public function order(){        
        if($this->openid){
            $model = new Mp_friends();
            $fansRow = $model->join('rh_mp_vip','rh_mp_vip.fid = rh_mp_friends.id')->where(['rh_mp_friends.openid'=>$this->openid,'rh_mp_friends.mpid'=>$this->mid])->find();
            if(!$fansRow['mobile']){
                $this->redirect('/app/weshop/index/login/mid/'.$this->mid);
            }
            $order_model = new Weshop_order;
            $goods_model = new Weshop_goods;
            $goodsIdList = array();
            $orderList   = $order_model->where('mpid',$this->mid)->where('openid',$this->openid)->order('id DESC')->paginate(10);
            foreach ($orderList as $key => $value) {
                $goodsIds           = json_decode($value['goods_id'],true);
                foreach ($goodsIds as $gkey => $gvalue) {
                    array_push($goodsIdList, $gvalue[0]);
                }
            }
            $goodsInfo      = $this->getGoodsInfo($goodsIdList);
            foreach ($orderList as $key => &$value) {
                $title          = '';
                $money          = 0;
                $goodsInfoArr   = array();
                $goodsIds       = json_decode($value['goods_id'],true);
                foreach ($goodsIds as $gkey => $gvalue) {
                    $gid = $gvalue[0];
                    if(isset($goodsInfo[$gid])){
                        $goodsInfo[$gid]['count'] = $gvalue[1];
                        array_push($goodsInfoArr, $goodsInfo[$gid]);
                        if($title == ''){
                            $title = $goodsInfo[$gid]['title'];
                        }else{
                            $title .= '-'.$goodsInfo[$gid]['title'];
                        }
                        $money += $goodsInfo[$gid]['price'];
                    }
                }
                $value['title']              = $title;
                $value['money']              = $money;
                $value['goodsInfo']          = $goodsInfoArr;
                $value['is_comment']         = $this->getComment($value['id']);
                $value['date']               = date("Y-m-d H:i:s",$value['time']);
            }
            $this->assign('orderList',$orderList);
        }else{
            $this->redirect('/app/weshop/index/login/mid/'.$this->mid);
        }        
        $this->fetch();      
    }
    //取消订单
    public function orderCancel(){
        $id = input('id');
        $model = new Weshop_order();
        $row = $model->where(['mpid'=>$this->mid,'openid'=>$this->openid,'id'=>$id])->find();
        if(!$row){
            return ajaxMsg(0,'订单不存在');
        }   
        $model->where(['id'=>$row['id']])->delete();
        return ajaxMsg(1,'取消成功');
    }

    //商品详情
    public function getGoodsInfo($goodsIdList=array()){
        $returnData = array();
        if(!empty($goodsIdList)){
            $model = new Weshop_goods();  
            $goodsList = $model->where('mpid',$this->mid)->where('id','in',$goodsIdList)->select();
            foreach ($goodsList as $key => $value) {
                $value['images']    = json_decode($value['images'],true);
                $value['image']     = current($value['images']);
                $returnData[$value['id']] = $value;
            }
        }
        return $returnData;
    }

    //评价详情
    public function getComment($id){
        $returnData = 0;        
        $model = new Weshop_comment();  
        $commentRow = $model->where('mpid',$this->mid)->where('order_id',$id)->find();
        if ($commentRow) {
            $returnData = 1;
        }        
        return $returnData;
    }
    //取订单列表
    public function getOrder(){
        $goodsIdList = array();
        $model = new Weshop_order();
        $itemList = $model->where(['mpid'=>$this->mid,'openid'=>$this->openid])
            ->order('id DESC')
            ->paginate(10);
        foreach ($itemList as $key => $value) {
            $goodsIds           = json_decode($value['goods_id'],true);
            foreach ($goodsIds as $gkey => $gvalue) {
                array_push($goodsIdList, $gvalue);
            }
        }
        $goodsInfo      = $this->getGoodsInfo($goodsIdList);
        foreach ($itemList as $key => &$value) {
            $title          = '';
            $money          = 0;
            $goodsInfoArr = array();
            $goodsIds       = json_decode($value['goods_id'],true);
            foreach ($goodsIds as $gkey => $gvalue) {
                $gid = $gvalue[0];
                if(isset($goodsInfo[$gid])){
                    $goodsInfo[$gid]['count'] = $gvalue[1];
                    array_push($goodsInfoArr, $goodsInfo[$gid]);
                    if($title == ''){
                        $title = $goodsInfo[$gid]['title'];
                    }else{
                        $title .= '-'.$goodsInfo[$gid]['title'];
                    }
                    $money += $goodsInfo[$gid]['price'];
                }
            }
            $value['title']              = $title;
            $value['money']              = $money;
            $value['goodsInfo']          = $goodsInfoArr;
            $value['date']               = date("Y-m-d H:i:s",$value['time']);
        }
        ajaxReturn($itemList);
    }  
    /*************个人中心end**************/
    /*************商城start**************/
    /*
     * 商城首页
     */
    public function index(){
        $banner_model   = new Weshop_banner();
        $type_model     = new Weshop_type();   
        $goods_model    = new Weshop_goods();
        $bannerList     = $banner_model->where(['mpid'=>$this->mid,'status'=>1])->order('sort desc')->select();
        $typeList       = $type_model->where(['mpid'=>$this->mid])->order('sort desc')->select();     
        foreach ($typeList as $key => &$value) {
            $goodsList          = $goods_model->where(['mpid'=>$this->mid,'status'=>1,'index'=>1,'type'=>$value['id']])->order('id desc')->select(); 
            foreach ($goodsList as $skey => &$svalue) {
                $images             = json_decode($svalue['images'],true);  
                $svalue['images']   = current($images);
            }
            $value['goodsList'] = $goodsList;
        }
        $this->assign('typeList',$typeList);
        $this->assign('bannerList',$bannerList);
        $this->fetch();
    }
    /*
     * 分类页
     * 
     */
    public function type(){   
        $type_model     = new Weshop_type();   
        $goods_model    = new Weshop_goods();
        $typeList       = $type_model->where(['mpid'=>$this->mid])->order('sort desc')->select();     
        foreach ($typeList as $key => &$value) {
            $goodsList          = $goods_model->where(['mpid'=>$this->mid,'status'=>1,'index'=>1,'type'=>$value['id']])->order('id desc')->select(); 
            foreach ($goodsList as $skey => &$svalue) {
                $images             = json_decode($svalue['images'],true);  
                $svalue['images']   = current($images);
            }
            $value['goodsList'] = $goodsList;
        }
        $this->assign('typeList',$typeList);      
        $this->fetch();  
    }

    /*
     * 分类商品页
     * 
     */
    public function typeList(){   
        $type           = input('type');//分类ID
        $order          = input('order');
        $order_type     = input('order_type');
        if(!$order){
            $order      = 'id';
        }
        if(!$order_type){
            $order_type = 'DESC';
        }
        $goods_model    = new Weshop_goods();
        $goodsList      = $goods_model->where(['mpid'=>$this->mid,'status'=>1,'type'=>$type])->order($order.' '.$order_type)->paginate(10);
        foreach ($goodsList as $key => &$value) {
            $images            = json_decode($value['images'],true);  
            $value['images']   = current($images);
        }
        if($order_type == 'DESC'){
            $order_type = 'ASC';
        }else{
            $order_type = 'DESC';
        }
        $this->assign('goodsList',$goodsList);
        $this->assign('type',$type);
        $this->assign('order',$order);
        $this->assign('order_type',$order_type);      
        $this->fetch();  
    }

    //获取商品列表
    public function getGoodsList(){
        $type           = input('type');//分类ID
        $order          = input('order');
        $order_type     = input('order_type');
        if(!$order){
            $order      = 'id';
        }
        if(!$order_type){
            $order_type = 'DESC';
        }
        $goods_model    = new Weshop_goods();
        $goodsList      = $goods_model->where(['mpid'=>$this->mid,'status'=>1,'type'=>$type])->order($order.' '.$order_type)->paginate(10);
        foreach ($goodsList as $key => &$value) {
            $images            = json_decode($value['images'],true);  
            $value['images']   = current($images);
        }
        ajaxReturn($goodsList);
    }

    /*
     * 商品详情页面
     * 
     */
    public function goods(){
        $id    = input('id');
        if(!$id){
            $this->redirect('/app/weshop/index/index/mid/'.$this->mid);
        }
        $goods_model    = new Weshop_goods(); 
        $row = $goods_model->where(['mpid'=>$this->mid,'id'=>$id,'status'=>1])->find();   
        if(!$row){
            $this->redirect('/app/weshop/index/index/mid/'.$this->mid);
        }  
        $row['images'] = json_decode($row['images'],true);
        $row['image'] = current($row['images']);
        $this->assign('row',$row);               
        $this->fetch();  
    }

    /*
     * 添加到购物车
     * 
     */
    public function addCar(){  
        $goodsId        = input('goodsId');
        $count          = input('count');
        if(!$goodsId){
            ajaxMsg(0,'请选择商品');
        }
        $goods_model    = new Weshop_goods(); 
        $car_model      = new Weshop_car();
        $goodsInfo      =$goods_model
            ->where(['mpid'=>$this->mid,'id'=>$goodsId,'status'=>1])
            ->find(); 
        if(!$goodsInfo){
            ajaxMsg(0,'商品不存在');
        }
        $carRow = $car_model->where(['mpid'=>$this->mid,'goods_id'=>$goodsId,'openid'=>$this->openid])->find();
        if($carRow){
            $car_model->where('id',$carRow['id'])->setField('count',$carRow['count']+$count);
        }else{
            $car_model->insert(['mpid'=>$this->mid,'goods_id'=>$goodsId,'count'=>$count,'openid'=>$this->openid,'time'=>time()]);
        }        
        ajaxMsg(1,'添加成功'); 
    }

    /*
     * 购物车删除
     * 
     */
    public function delCar(){  
        $id        = input('id');
        if(!$id){
            ajaxMsg(0,'请选择商品');
        }
        $car_model      = new Weshop_car();
        $goodsInfo      =$car_model
            ->where(['mpid'=>$this->mid,'id'=>$id])
            ->delete();        
        ajaxMsg(1,'删除成功'); 
    }

    /*
     * 购物车增减
     * 
     */
    public function editCar(){  
        $id        = input('id');
        if(!$id){
            ajaxMsg(0,'请选择商品');
        }
        $type        = input('type');//1增加；0减少
        $car_model      = new Weshop_car();
        $goodsInfo      =$car_model->where(['mpid'=>$this->mid,'openid'=>$this->openid,'id'=>$id])->find();
        if(!$goodsInfo){
            ajaxMsg(0,'商品不存在');
        }
        if($type == 1){
            $car_model->where(['mpid'=>$this->mid,'id'=>$goodsInfo['id']])->setField('count',$goodsInfo['count']+1);
        }else{
            if($goodsInfo['count']-1 == 0){
                $car_model->where(['mpid'=>$this->mid,'id'=>$goodsInfo['id']])->delete();
            }else{
                $car_model->where(['mpid'=>$this->mid,'id'=>$goodsInfo['id']])->setField('count',$goodsInfo['count']-1);
            }
        }
        ajaxMsg(1,'操作成功'); 
    }

    /*
     * 购物车
     * 
     */
    public function goodsCar(){  
        $model      = new Weshop_car();
        $goods_model= new Weshop_goods(); 
        $itemList   = $model
            ->where(['mpid'=>$this->mid,'openid'=>$this->openid])
            ->order('id DESC')
            ->select(); 
        foreach ($itemList as $key => &$value) {
            $value['goods']     = $goods_model->where(['mpid'=>$this->mid,'id'=>$value['goods_id'],'status'=>1])->find(); 
            $value['date']      = date("Y-m-d H:i:s",$value['time']);
            $value['goods']['images']    = json_decode($value['goods']['images'],true);
            $value['goods']['image']     = current($value['goods']['images']);
        }
        $count = count($itemList);
        $this->assign('itemList',$itemList);  
        $this->assign('count',$count);           
        $this->fetch();  
    }
    //结算页面
    public function settlement(){
        $car_id   = input('post.car_id/a');
        if($car_id){
            Session::delete('car_id');
            Session::set('car_id',$car_id);
        }else{
            $car_id = Session::get('car_id');
        }
        var_dump($car_id);
        $address_id = input('address_id');        
        $model      = new Weshop_car();
        $goods_model= new Weshop_goods(); 
        $itemList   = $model
            ->where(['mpid'=>$this->mid,'openid'=>$this->openid])
            ->where('id','in',$car_id)
            ->order('id DESC')
            ->select(); 
        
        foreach ($itemList as $key => &$value) {
            $value['goods']     = $goods_model->where(['mpid'=>$this->mid,'id'=>$value['goods_id'],'status'=>1])->find(); 
            $value['date']      = date("Y-m-d H:i:s",$value['time']);
            $value['goods']['images']    = json_decode($value['goods']['images'],true);
            $value['goods']['image']     = current($value['goods']['images']);
        }
        $count              = count($itemList);            
        $address_model      = new Weshop_address();
        if($address_id){
            $addressInfo        =$address_model->where(['mpid'=>$this->mid,'id'=>$address_id,'openid'=>$this->openid])->find();
        }else{
            $addressInfo        =$address_model->where(['mpid'=>$this->mid,'default'=>1,'openid'=>$this->openid])->find();
        }          
        $this->assign('addressInfo',$addressInfo);
        $this->assign('itemList',$itemList);  
        $this->assign('count',$count);
        $this->fetch();  
    }
    //地址列表
    public function addressList(){
        $model = new Weshop_address();
        $itemList = $model->where(['mpid'=>$this->mid,'openid'=>$this->openid])
            ->order('id DESC')
            ->select();
        $this->assign('itemList',$itemList);           
        $this->fetch(); 
    }

    /*
     * 删除地址
     * 
     */
    public function delAddress(){  
        $id        = input('id');
        if(!$id){
            ajaxMsg(0,'请选择地址');
        }
        $model          = new Weshop_address();
        $addressInfo      =$model
            ->where(['mpid'=>$this->mid,'id'=>$id,'openid'=>$this->openid])
            ->delete();        
        ajaxMsg(1,'删除成功'); 
    }

    /*
     * 设置默认地址
     * 
     */
    public function setAddress(){  
        $id        = input('id');
        if(!$id){
            ajaxMsg(0,'请选择地址');
        }
        $model          = new Weshop_address();
        $addressInfo      =$model
            ->where(['mpid'=>$this->mid,'id'=>$id,'openid'=>$this->openid])
            ->find();  
        if($addressInfo){
            $model->where(['mpid'=>$this->mid,'openid'=>$this->openid])->update(['default'=>0]);
            $model->where(['mpid'=>$this->mid,'id'=>$id,'openid'=>$this->openid])->setField('default',1);
        }
        ajaxMsg(1,'设置成功'); 
    }

    //添加地址
    public function editAddress(){
        $this->fetch(); 
    }

    //保存地址
    public function saveAddress(){
        $provincestr    = input('province');        
        $address        = input('address');
        $name           = input('name');
        $mobile         = input('mobile');
        if(!$provincestr || !$address || !$name || !$mobile){
            ajaxMsg(0,'信息不完整');
        }
        $provinceArr    = explode(' ', $provincestr);
        $province       = $provinceArr[0];
        $city           = $provinceArr[1];
        $area           = $provinceArr[2];
        $model          = new Weshop_address();
        $id = $model->insertGetId(['mpid'=>$this->mid,'province'=>$province,'city'=>$city,'openid'=>$this->openid,'area'=>$area,'address'=>$address,'name'=>$name,'mobile'=>$mobile,'time'=>time()]);
        if($id){
            ajaxMsg(1,'添加成功');
        }else{
           ajaxMsg(0,'添加失败'); 
       }        
    }

    //提交订单
    public function sendOrder(){
        $ids           = input('post.id/a');//购物车表的ID
        $address_id    = input('address_id');
        if(count($ids) == 0){
            ajaxMsg(0,'请选择商品'); 
        }
        if(!$address_id){
            ajaxMsg(0,'请选择地址'); 
        }
        $goodsCarArr = array();        
        $car_model = new Weshop_car();
        $goodsCarList = $car_model->where('id','in',$ids)->where('openid',$this->openid)->where('mpid',$this->mid)->select();
        foreach ($goodsCarList as $key => $value) {
            array_push($goodsCarArr,array($value['goods_id'],$value['count']));
        }
        $goodsIds = json_encode($goodsCarArr);
        $model = new Weshop_order();
        $id = $model->insertGetId(['mpid'=>$this->mid,'openid'=>$this->openid,'goods_id'=>$goodsIds,'address_id'=>$address_id,'time'=>time()]);
        if($id){
            //清除掉购物车里对应的商品
            $car_model = new Weshop_car();
            $car_model->where('id','in',$ids)->where('openid',$this->openid)->where('mpid',$this->mid)->delete();
            //通过查询商品的标题组装成付款的标题
            $goodsIdsInCar = array();
            foreach ($goodsCarList as $key => $value) {
                array_push($goodsIdsInCar,$value['goods_id']);
            }
            $goods_model= new Weshop_goods();
            $goodsList  = $goods_model->where('id','in',$goodsIdsInCar)->select();
            $goodsTitle = array();            
            foreach ($goodsList as $key => $value) {
                $goodsTitle[] = $value['title'];  
                //更改商品库存
                foreach ($goodsCarList as $gkey => $gvalue) {
                    $goods_model->where('id',$gvalue['goods_id'])->setField('count',($value['count']-$gvalue['count']));
                }              
            }
            $title = implode('-', $goodsTitle); 
            $return = array(
                'status'=>1,
                'title' => $title,
                'order_id' =>$id
            );
            ajaxReturn($return);
        }else{
            ajaxMsg(0,'订单保存失败'); 
        }
    }

    //付款
    public function pay()
    {
        if (Request::isPost()) {
            if ($member = getMember()) {
                $money  = input('money');
                $title  = input('title');
                $order_id  = input('order_id');
                if (isset($member['openid']) && !empty($member['openid'])) {
                    if (empty($money) || $money < 0.09) {
                        ajaxMsg(0, '金额最小为0.1元');
                    } else {
                        $mid = $this->mid;
                        if (!$mid && $mid != $member['mpid']) {
                            ajaxMsg(0, '公众号标识与当前用户不匹配');
                        }
                        $model = new Payment();
                        $id = $model->addPayment($member['id'], $member['mpid'], $money, $title, '', 1 ,'');
                        if($id){
                            $order_model = new Weshop_order();
                            $order_model->where('id',$order_id)->where('openid',$this->openid)->update(['payment_id'=>$id]);
                            ajaxReturn(['url' => getWxPayUrl($this->mid,['payment_id' => $id,'view'=>$this->addonRoot.'view/common/pay.html'])]);
                        } else {
                            ajaxMsg(0, '下单失败');
                        }
                    }
                } else {
                    ajaxMsg(0, '支付参数：openid不存在');
                }
            } else {
                ajaxMsg(0, '用户不存在');
            }
        }
    }

    /*
     * 付款成功，更新订单状态
     * 
     */
    public function setOrderStatus(){  
        $id        = input('id');
        if(!$id){
            ajaxMsg(0,'请选择地址');
        }
        $order_model = new Weshop_order();
        $order_model->where('payment_id',$id)->where('mpid',$this->mid)->where('openid',$this->openid)->update(['status'=>1]);        
        ajaxMsg(1,'付款成功'); 
    }

    /*
     * 确认收货
     * 
     */
    public function finish(){  
        $id        = input('order_id');
        if(!$id){
            ajaxMsg(0,'请选择地址');
        }
        $order_model = new Weshop_order();
        $order_model->where('id',$id)->where('mpid',$this->mid)->where('openid',$this->openid)->update(['is_finish'=>1]);        
        ajaxMsg(1,'已确认收货'); 
    }

    /*
     * 物流页面
     */
    public function logistics(){        
        if($this->openid){
            $id = input('id');
            $model = new Mp_friends();
            $fansRow = $model->join('rh_mp_vip','rh_mp_vip.fid = rh_mp_friends.id')->where(['rh_mp_friends.openid'=>$this->openid,'rh_mp_friends.mpid'=>$this->mid])->find();
            if(!$fansRow['mobile']){
                $this->redirect('/app/weshop/index/login/mid/'.$this->mid);
            }
            $order_model = new Weshop_order;
            $goods_model = new Weshop_goods;
            $goodsIdList = array();
            $orderRow   = $order_model->where('mpid',$this->mid)->where('openid',$this->openid)->where('id',$id)->find();
            $goodsIds           = json_decode($orderRow['goods_id'],true);
            foreach ($goodsIds as $gkey => $gvalue) {
                array_push($goodsIdList, $gvalue);
            }
            $goodsInfo      = $this->getGoodsInfo($goodsIdList);
            $goodsInfoArr = array();
            foreach ($goodsIds as $gkey => $gvalue) {
                if(isset($goodsInfo[$gvalue])){
                    array_push($goodsInfoArr, $goodsInfo[$gvalue]);
                }
            }
            $orderRow['goodsInfo']          = $goodsInfoArr;
            $orderRow['date']               = date("Y-m-d H:i:s",$orderRow['time']);
            $orderRow['logistics']          = $this->getLogistics($orderRow['logistics_num']);
            $this->assign('orderList',$orderRow);
        }else{
            $this->redirect('/app/weshop/index/login/mid/'.$this->mid);
        }        
        $this->fetch();      
    }

    //物流详情（我没有帐号，所以这里没有用到）
    public function getLogistics($number){
        $info       = getAddonInfo();
        $key        = $info['mp_config']['key'];
        $id         = $info['mp_config']['name'];
        $url        = 'http://api.aikuaidi.cn/rest/?key={$key}&order={$number}&id={$id}&ord=desc&show=json';
        $data       = file_get_contents($url);
        $returnData = json_decode($data,true);
        return $returnData;
    }

    /*
     * 评价页面
     */
    public function comment(){        
        if($this->openid){
            $id = input('id');
            $model = new Mp_friends();
            $fansRow = $model->join('rh_mp_vip','rh_mp_vip.fid = rh_mp_friends.id')->where(['rh_mp_friends.openid'=>$this->openid,'rh_mp_friends.mpid'=>$this->mid])->find();
            if(!$fansRow['mobile']){
                $this->redirect('/app/weshop/index/login/mid/'.$this->mid);
            }
            $order_model        = new Weshop_order;
            $goods_model        = new Weshop_goods;
            $comment_model      = new Weshop_comment;
            $goodsIdList        = array();
            $orderRow           = $order_model->where('mpid',$this->mid)->where('openid',$this->openid)->where('id',$id)->find();
            $goodsIds           = json_decode($orderRow['goods_id'],true);
            foreach ($goodsIds as $gkey => $gvalue) {
                array_push($goodsIdList, $gvalue[0]);
            }
            $goodsInfo      = $this->getGoodsInfo($goodsIdList);
            $goodsInfoArr = array();
            foreach ($goodsIds as $gkey => $gvalue) {
                if(isset($goodsInfo[$gvalue[0]])){
                    $goodsInfo[$gvalue[0]]['count'] = $gvalue[1];
                    array_push($goodsInfoArr, $goodsInfo[$gvalue[0]]);
                }
            }
            $orderRow['goodsInfo']          = $goodsInfoArr;
            $orderRow['date']               = date("Y-m-d H:i:s",$orderRow['time']);
            $this->assign('orderRow',$orderRow);
        }else{
            $this->redirect('/app/weshop/index/login/mid/'.$this->mid);
        }        
        $this->fetch();      
    }

    /*
     * 提交评价
     * 
     */
    public function commentPost(){  
        $id             = input('order_id');
        $content        = input('content');
        $images         = input('images');
        $star1          = input('star1');
        $star2          = input('star2');
        $star3          = input('star3');
        if(!$id){
            ajaxMsg(0,'请选择订单');
        }
        $order_model    = new Weshop_order();
        $comment_model  = new Weshop_comment();
        $orderRow       = $order_model->where('id',$id)->where('mpid',$this->mid)->where('openid',$this->openid)->find();
        if($orderRow){
            $comment_model->insertGetId(['mpid'=>$this->mid,'openid'=>$this->openid,'order_id'=>$id,'star1'=>$star1,'star2'=>$star2,'star3'=>$star3,'content'=>$content,'images'=>$images,'time'=>time()]);
            
        }              
        ajaxMsg(1,'评价成功'); 
    }    
    /*************商城end**************/
}