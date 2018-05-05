<?php
/*
 * 公共方法
 * @author     Mrlv(315141428@qq.com)
 * @copyright  上海比良网络科技有限公司
 * @Created    2017-11-26
 */
namespace addons\weshop\controller;
use addons\weshop\model\Mp_friends;
use app\common\controller\Addon;
use think\Db;
class Common extends Addon
{
    public $onlyWexinOpen = true;
    public $isWexinLogin = true;
    public $scope = 'snsapi_userinfo';//snsapi_base||snsapi_userinfo
    public $info;
    public $openid;

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $model  = new Mp_friends();
        $member = getMember();
        $openid = getOrSetOpenid();
        $this->openid   = isset($member['openid'])?$member['openid']:$openid;
        $this->info     = $info = getAddonInfo();
        if(isset($info['mp_config'])){
            if(isset($info['mp_config']['login_type']) && $info['mp_config']['login_type'] ==1){
                $this->wexinLogin();
            }
        }
        $this->assign('mpInfo',getMpInfo($this->mid));
        $this->assign('info', $info['mp_config']);
        $this->assign('action', strtolower(request()->action()));
        $this->assign('member', $member);
    }

    /*
     * 发送验证码
     * 登录页请求、个人信息页面请求
     * 如果是个人信息页面请求，同时新手机号和旧的如果是一样的，提示输入新手机号
     */
    public function getCode(){
        $input      = input();
        if(!isset($input['mobile'])){
            return ajaxMsg(0,'请输入手机号');
        }
        $model = new Mp_friends();
        $fansRow = $model->join('rh_mp_vip','rh_mp_vip.fid = rh_mp_friends.id')->where(['rh_mp_friends.openid'=>$this->openid,'rh_mp_friends.mpid'=>$this->mid])->field('rh_mp_friends.*')->find();
        if($fansRow){
            if($input['mobile'] == $fansRow['mobile']){            
                return ajaxMsg(-1,'请输入新的手机号');               
            }
        }        
        $info       = getAddonInfo();
        $code       = rand(1000,9999);
        //这里调用的是阿里云的短信验证码
        $sms        = new \Aliyun\DySDKLite\Sms\SmsApi($info['mp_config']['AccessKeyId'], $info['mp_config']['AccessKeySecret']);
        $response   = $sms->sendSms(
            $info['mp_config']['SignName'], // 短信签名
            $info['mp_config']['TemplateCode'], // 短信模板编号
            $input['mobile'], // 短信接收者
            Array (  // 短信模板中字段的值
                "code"=>$code
            )
        );
        if($response->Message == 'OK'){
            Session::set($input['mobile'].'_code',$code); 
            $status = 1;
            $str    = '验证码已发送';
        }else{
            $str    = $response->Message;
            $status = -1;
        }        
        return ajaxMsg($status,$str);
    }
    /*
     * 生成6位数邀请码
     */
    public function getInviteNumber($length=6){
        $model = new Mp_vip();
        $number = $this->get_random($length);
        $row = $model->where(['invite_number'=>$number])->find();
        while ($row) {
            $number = $this->get_random($length);
            $row = $model->where(['invite_number'=>$number])->find();
        }
        return $number;
    }

    //随机数  
    public function get_random($length = 4) {  
        $min = pow(10 , ($length - 1));  
        $max = pow(10, $length) - 1;  
        return mt_rand($min, $max);  
    }
}