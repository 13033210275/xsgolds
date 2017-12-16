<?php

namespace app\home\model;

use think\Model;

class Lunbo extends Model {

    
    public function mert(){
        return $this->belongsTo('Merchant','merchant_id')->field('name,logo,min_cash,max_cash,min_date,max_date');
    }
 
}
