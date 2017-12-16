<?php

namespace app\admin\model;

 
class Lunbo extends \think\Model{
    
    protected $autoWriteTimestamp = false;
    
    
    public function mert(){
        return $this->belongsTo('Merchant')->field('name');
    }
}