<?php
namespace app\home\controller;
use app\home\model\Document;
 
class Article extends Home {
 

    public function index(){
        
    }
    
    public function detail($id){
        if(!$id){
            $this->error('参数错误');
        }
        $row = Document::get($id);
        if(!$row){
            $this->error('资讯已经不存在');
        }
        $golds_source = input('golds_source');
        $this->assign('isshow',$golds_source);
        $this->assign('row',$row);
        $this->assign('seo_title','口子资讯详情');
        $this->assign('css','whiteBg');

        return $this->fetch();
    }
}
