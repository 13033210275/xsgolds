<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/8 0008
 * Time: 10:18
 */

namespace app\home\controller;



class Information extends Home
{

    public function index(){
        $golds_source = input('golds_source');
        $this->assign('isshow',$golds_source);
        $this->assign('curr','information');
        $this->assign('seo_title','口子资讯');
        return $this->fetch();
    }
}