<?php

namespace app\admin\model;

/**
 * 配置模型
 */
class Apply extends \think\Model{
    
    protected $autoWriteTimestamp = false; 
    
    
    public function mert(){
        return $this->belongsTo('Merchant', 'mert_id')->field('name');
    }
    
    public function user(){
        return $this->belongsTo('Member', 'user_id')->field('nickname,truename');
    }

    
    public function updateApplyStatus($user_id,$mert_id){
        return $this->update([
            'loan_status'=>2
        ],[
            'user_id'=>$user_id,
            'mert_id'=>$mert_id,
        ]);
    }

    public function getLoanStatusTextAttr($value,$data){
        $arr = [ 
            1=>'已申请',
            2=>'已放款' 
        ];
        return $arr[$data['loan_status']];
    }
    

    public function getStatusTextAttr($value,$data){
        $arr = [
            0=>'待审核', 
            1=>'已申请',
            2=>'出额度',
            3=>'已放款',
            4=>'被拒绝',
            5=>'其他',
        ];
        return $arr[$data['status']];
    }
}