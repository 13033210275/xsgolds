<?php

namespace app\admin\model;

/**
 * 配置模型
 */
class MerchantScore extends \think\Model{
    protected $autoWriteTimestamp = true;
    protected $updateTime = false;
    
    
    public function mert(){
        return $this->belongsTo('Merchant','mert_id')->field('name');
    }

    public function setUserScoreAttr($value)
    {
        if($value<0){
            $value = 0;
        }
        if($value>5){
            $value = 5;
        }
        return $value;
    }
    
     public function setSysScoreAttr($value)
    {
        if($value<0){
            $value = 0;
        }
        if($value>5){
            $value = 5;
        }
        return $value;
    }
    
    
    public function updateMertScore($mert_id,$score){
        $row = $this->where('mert_id',$mert_id)->find();
        if($row){
            $f1 = $this->isUpdate(true)->save([
                 'user_score'=>$score,
             ],[
                 'mert_id'=>$mert_id
             ]);
        }else{
           $f1 = $this->isUpdate(false)->save([
                 'user_score'=>$score,
                 'mert_id'=>$mert_id
             ]);
        }
        return $f1;
    }
}