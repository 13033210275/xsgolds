<?php

namespace app\home\controller;



class User extends Home{


    public function index(){

        $golds_source = input('golds_source');
        $this->assign('isshow',$golds_source);
        $this->assign('curr','user');
        $this->assign('seo_title','用户中心');
        return $this->fetch();
    }


    public function qrcode(){
        $golds_source = input('golds_source');
        $this->assign('isshow',$golds_source);
        $this->assign('seo_title','关注公众号');
        return $this->fetch();
    }

    public function cooperate(){
        $golds_source = input('golds_source');
        $this->assign('isshow',$golds_source);
        $this->assign('seo_title','商务合作');
        return $this->fetch();
    }


    public function help(){
        $golds_source = input('golds_source');
        $this->assign('isshow',$golds_source);
        $this->assign('seo_title','帮助中心');
        return $this->fetch();
    }
}
