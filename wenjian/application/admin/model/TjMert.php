<?php
 
namespace app\admin\model;
use think\Model;
 

class TjMert extends Model { 
    protected $autoWriteTimestamp = false;
    
    
    public function mert(){
        return $this->belongsTo('Merchant', 'mert_id')->field('name');
    }
}
