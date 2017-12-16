<?php
 
namespace app\home\controller;
use app\home\model\Member;
 
class Comment extends Home{
   
    public function index($id){
        $this->assign('nav',1);
        $this->assign('id',$id);
        $this->assign('seo_title','用户评论列表');
        return $this->fetch();
    }
    
    public function post($id){
        $uid = $this->_islogin();
        if(!$uid){

            $k= 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->error('请先登录', url('login/index')."?u=".$k);
        }
        
        $row = Member::where('id',$uid)->field('id,nickname,mobile')->find();
        $this->assign('row',$row);
        
        $this->assign('nav',1);
        $this->assign('id',$id);
        $this->assign('seo_title','发表评论');
        return $this->fetch();
    }
}
