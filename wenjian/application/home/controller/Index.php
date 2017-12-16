<?php

namespace app\home\controller;
use app\home\model\Adv;
use think\Session;
use app\home\model\Document;
use OT\DataDictionary;
use think\Config;
 
class Index extends Home{

    //系统首页
    public function index(){
 
//        $category = model('Category')->getTree();
//        $document = new Document();
//        $lists    = $document->lists(null);
//        $this->assign('category',$category);//栏目
//        $this->assign('lists',$lists);//列表
//        $this->assign('page',model('Document')->page);//分页

        $sor_code = input('sor_code','');
        Session::set('sor_code',$sor_code);//获取客户端编码
        if(!isMobile()){
           /* if ($_SERVER['HTTP_HOST'] == 'xsgolds.cn' || $_SERVER['HTTP_HOST'] == 'www.xsgolds.cn'){
                $this->assign('curr','index');
                $this->assign('seo_title','首页');
                return $this->fetch();
            }*/
             $this->redirect(url('accurate'));
        }
        else{
            $golds_source = input('golds_source');
            $this->assign('isshow',$golds_source);
            $this->assign('curr','index');
            $this->assign('seo_title','首页');
            return $this->fetch();
        }

    }

    //PC首页
    public function land_page(){
            if(!isMobile()){
                return $this->fetch('land_page');
            } else {

                return $this->fetch('land_page');
            }
    }

    public function accurate(){
        if(!isMobile()){
            return $this->fetch('accurate');
        } else {
            $this->redirect(url('/'));
        }
    }

    public function company(){
            if(!isMobile()){
                return $this->fetch('company');
            } else {
                $this->redirect(url('/'));
            }
        }

    public function contact(){
            if(!isMobile()){
                return $this->fetch('contact');
            } else {
                $this->redirect(url('/'));
            }
    }

}
