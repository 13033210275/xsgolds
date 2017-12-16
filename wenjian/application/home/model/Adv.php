<?php

namespace app\home\model;

use think\Db;
use think\Model;

class Adv extends Model {



    public function picture() {
        return $this->hasOne('Picture','id','img')->field('id,path');
    }

    public  function lists($flag) {
        $map = [
            'status'=>1,
            'flag'=>$flag
        ];
        $data = [];
        $list =  $this->field('id,alt,img,type,val')->with('picture')->where($map)->order('rise asc')->select();
        if($list){
            foreach ($list as $vo) {
                $link = $vo->val;
                if($vo->type==2){
                    $link = '/a/'.$vo->val;
                }
                $data[] = [
                    'id'=>$vo->id,
                    'link'=>$link,
                    'img'=>$vo->picture->path,
                    'alt'=>$vo->alt
                ];
            }
        }
        return $data;
    }

    public function bannerList($adv_type, $status)
    {
        $map = [
            'status'=>$status,
            'adv_type'=>$adv_type,
        ];
        $data = [];
        $list =  $this->field('id,alt,img,type,val')->with('picture')->where($map)->order('rise asc')->select();
        if($list){
            foreach ($list as $vo) {
                $link = $vo->val;
                if($vo->type==2){
                    $link = '/a/'.$vo->val;
                }
                $data[] = [
                    'id'=>$vo->id,
                    'link'=>$link,
                    'img'=>$vo->picture->path,
                    'alt'=>$vo->alt
                ];
            }
        }
        return $data;
    }

    public function apiBanner($flag=''){

        $map = [
            'status'=>1,
            'flag'=>$flag
        ];
        $list =  $this->field('id,alt,img,type,val')->with('picture')->where($map)->select();

        return $list;
    }
}
