<?php 

//ok
namespace app\admin\model;
use think\Model;
 

class Partment extends Model { 
    protected $updateTime = false;
    
    
    public function member(){
        return $this->belongsTo('Member','user_id')->field('mobile,rate,truename');
    }

    public function getStatusTextAttr($value,$data){
        $arr = [0=>'禁用',1=>'开启'];
        return $arr[$data['status']];
    }
    
    
    

    
    public function getPartmentSelect($curr=0){
        $data = ['请选择部门'];
        $res = $this->field('id,partname,is_medi')->where('status',1)->order('is_medi asc')->select();
        if($res){
            foreach ($res as $vo){
                $name = $vo->partname;
                if($vo->is_medi){
                    $name = '[中介]'.$name;
                }
                $data[$vo->id] = $name;
            }
        }
        $partSelect = bulid_form('select', $data, 'part_id',$curr);
        return $partSelect;
    }
}
