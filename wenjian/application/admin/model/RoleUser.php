<?php

namespace app\admin\model;

 
class RoleUser extends \think\Model{
    
    protected $autoWriteTimestamp = false; 
    
    
    public function member(){
        return $this->belongsTo('Member','user_id')->field('id,nickname,last_login_time,last_login_ip,status');
    }
    
    
    
     
}