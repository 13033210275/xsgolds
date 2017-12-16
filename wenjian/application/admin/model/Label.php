<?php

namespace app\admin\model;


class Label extends \think\Model{
    
    protected $autoWriteTimestamp = false; 
    
    public function getTypeTextAttr($value,$data){
        $arr = [
            1=>'标签',
            2=>'材料',
            3=>'借款用途'
        ];
        return $arr[$data['type']];
    }
    
    
    public function getAllType(){
       $data = [];
       $rs = $this->select();
       if(!$rs){
           return ;
       }
       
       foreach ($rs as $vo) {
           $data[$vo['type']][$vo['id']] = $vo['name']; 
       }
       return $data;
    }
}