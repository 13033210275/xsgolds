<?php

namespace app\home\controller;
use think\Session;
use app\home\model\Member;

class Login extends Home{


    public function index(){
        $u = input('u','');
        $key = md5(\think\Request::instance()->url(true));
        Session::set('key',$key);
        $golds_source = input('golds_source');
        $this->assign('isshow',$golds_source);
        $this->assign('u',$u);
        $this->assign('key',$key);

        $this->assign('seo_title','首页');
        return $this->fetch();
    }

    public function reg(){
        $sor_code=Session::get('sor_code');// 获取APP端sor_code
        if($sor_code==''){
            if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){
                $sor_code='2219000001';//IOS浏览器
                if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ){
                    $sor_code='2211000001';//IOS微信
                }
            }
            else if(strpos($_SERVER['HTTP_USER_AGENT'], 'Android')){
                $sor_code='2119000001';//Android浏览器
                if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ){
                    $sor_code='2111000001';//Android微信
                }
            }
        }
        $u = input('u','');
        $golds_source = input('golds_source');
        $this->assign('isshow',$golds_source);
        $this->assign('u',$u);
        $this->assign('sor_code',$sor_code);
        $sid = input('sid/s','');
        if($sid){
            cookie('invite',$sid);
        }else{
            $sid = cookie('invite');
        }
        $this->assign('sid',$sid);

        $key = md5(\think\Request::instance()->url(true));
        Session::set('key',$key);

        $this->assign('key',$key);
        $this->assign('seo_title','注册');
        return $this->fetch();
    }


    public function find(){
        $key = md5(\think\Request::instance()->url(true));
        Session::set('key',$key);
        $golds_source = input('golds_source');
        $this->assign('isshow',$golds_source);
        $this->assign('key',$key);
        $this->assign('seo_title','找回密码');
        return $this->fetch();
    }

    //检测是否已经登陆
    public function isLogin(){
        $uid = $this->_islogin(true);
        $row = Member::where('id',$uid)->field('id,nickname,mobile')->find();
        finish(0,'',[
            'sid'=>$uid,
            'name'=>$row->nickname,
            "mobile"=>$row->mobile
        ]);
    }

    //退出
    public function logout(){
        Session::delete('uid');
        Session::delete('uname');
        finish(0);
    }

}
