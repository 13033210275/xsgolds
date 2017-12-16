<?php

namespace app\admin\model;

/**
 * 配置模型
 */
class Merchant extends \think\Model{
    
    protected $autoWriteTimestamp = true;
    protected $updateTime = false;
    
   
    
    public function zt()
    {
        return $this->belongsTo('zuanti','zt_id');
    }
    
 
    
//    public function getTagAttr($value){
//        return explode(',', $value);
//    }
//
//    
    
//    public function getCailiaoAttr($value){
//        return explode(',', $value);
//    }
//
//    public function setCailiaoAttr($value){
//        return implode(',', $value);
//    }
//    
    
//    public function getUseAreaAttr($value){
//        return explode(',', $value);
//    }
//
//    public function setUseAreaAttr($value){
//        return implode(',', $value);
//    }
//    
    
//    public function getToPeopleAttr($value){
//        return explode(',', $value);
//    }
//
//    public function setToPeopleAttr($value){
//        return implode(',', $value);
//    }
    

    public function getTypeTextAttr($value,$data){
        $arr = [    
            1=>'借款',
            2=>'信用卡'
        ];
        return $arr[$data['type']];
    }
    
    
    public function getCateTextAttr($value,$data){
        $arr = [ 
            1=>'现金贷',
            2=>'空放',
            3=>'车辆抵押',
            4=>'信用卡'
        ];
        return $arr[$data['cate_id']];
    }
    
    
    public function getIndexTextAttr($value,$data){
        $arr = [    
            0=>'否',
            1=>'是'
        ];
        return $arr[$data['is_index']];
    }
    
    public function getStatusTextAttr($value,$data){
        $arr = [    
            0=>'否',
            1=>'是'
        ];
        return $arr[$data['status']];
    }
    
    
    public function getAdvTextAttr($value,$data){
        $arr = [    
            0=>'否',
            1=>'是'
        ];
        return $arr[$data['use_adv']];
    }
    
}