<?php
/*
 * 商品管理
 * @author     Mrlv(315141428@qq.com)
 * @copyright  上海比良网络科技有限公司
 * @Created    2017-11-26
 */
namespace addons\weshop\controller;
use addons\weshop\model\Weshop_type;
use addons\weshop\model\Weshop_goods;
use app\common\controller\Addon;
use think\Controller;
use think\facade\Request;
class Goods extends Addon
{
    public $adminLogin=true;//需要管理员登录才可操作本控制器
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub        
    }

    public function index(){    
        $model = new Weshop_goods();        
        $result=$model
            ->where(['mpid'=>$this->mid])
            ->order('id DESC')
            ->paginate(20);
        foreach ($result as $key => &$value) {
            $value['images'] = current(json_decode($value['images'],true));
        }
        $this->assign('data',$result);    
        $this->fetch();
    }

    public function edit(){
        $datamodel = new Weshop_goods();
        $typemodel = new Weshop_type();
        $id = input('id');
        $result = array();
        if(isset($id)){              
            $result=$datamodel                
                ->where(['mpid'=>$this->mid,'id'=>$id])
                ->find();
            $result['images'] = json_decode($result['images'],true);
        }        
        if(Request::isAjax()){ 
            $images = input('post.images/a');
            $goodsData = [
                'title'=>input('title'),//标题
                'type'=>input('type'),//分类
                'images'=>json_encode($images),//图片
                'price'=>input('price'),//价格
                'freight'=>input('freight'),//运费
                'content'=>input('content'),//内容
                'count'=>input('count'),//库存
                'status'=>input('status'),//状态
                'index'=>input('index'),//首页展示
            ];
            if($result){            
                $datamodel
                ->where(['mpid'=>$this->mid,'id'=>$id])
                ->update($goodsData);
                ajaxReturn(null, 1, '操作成功');
            }else{   
                $goodsData['mpid'] = $this->mid;   
                $datamodel
                ->insert($goodsData);
                ajaxReturn(null, 1, '操作成功');
            }     
        }  
        $typeList=$typemodel                
                ->where(['mpid'=>$this->mid])
                ->select();
        $this->assign('data',$result);
        $this->assign('typeList',$typeList);
        $this->fetch();
    }

    public function del(){
        if(Request::isAjax()){ 
            $id = input('id');            
            $model = new Weshop_goods();        
            $result=$model
                ->where(['mpid'=>$this->mid,'id'=>$id])
                ->find();                        
            if($result){
                $model
                ->where(['mpid'=>$this->mid,'id'=>$id])
                ->delete();
                ajaxReturn(null, 1, '操作成功');
            }else{
                ajaxReturn(null, 0, '数据不存在');
            }
        }else{
            ajaxReturn(null, 0, '数据不存在');
        }
    }
}