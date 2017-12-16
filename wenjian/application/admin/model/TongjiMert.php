<?php

namespace app\admin\model;

 
class TongjiMert extends \think\Model{
    
    protected $autoWriteTimestamp = false;
    
     public function mert(){
        return $this->belongsTo('Merchant','mert_id')->field('name');
    }
}