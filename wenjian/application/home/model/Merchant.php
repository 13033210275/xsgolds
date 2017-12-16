<?php

namespace app\home\model;

use think\Model;

class Merchant extends Model {

    
    public function picture() {
        return $this->hasOne('Picture','id','img')->field('path');
    }
    
    
    public function ext() {
        return $this->hasOne('MerchantScore','mert_id','id')->field('user_score,sys_score,use_sys,apply_num');
    }
    
    
    
     public function getPassTypeAttr($value){
        $arr = [1=>'人工审核',2=>'自动审核'];
        return $arr[$value];
    }
    
    
}
