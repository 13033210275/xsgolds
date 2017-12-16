<?php
 
namespace app\admin\model;
use think\Model;
 

class TongjiUser extends Model { 
    protected $autoWriteTimestamp = false;
    
    public function user(){
        return $this->belongsTo('Member','user_id')->field('nickname');
    }
    
    
     public function mert(){
        return $this->belongsTo('Merchant','mert_id')->field('name');
    }
}
