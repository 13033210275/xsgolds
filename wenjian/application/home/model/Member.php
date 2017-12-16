<?php

namespace app\home\model;

use think\Model;

class Member extends Model {

    protected $updateTime = false;
    protected $insert = ['reg_ip','passwd','is_reg' =>1,'status'=>1];
    protected $createTime = 'reg_time';

    protected static function init() {
        Member::afterInsert(function ($obj) {
            
            //统计
            $date = date("Y-m-d");
            $tongji = new TongjiTotal;
            $row = $tongji->where('date',$date)->field('id')->find();
            if($row){
                $tongji->where('id',$row->id)->setInc('reg',1);
            }else{
                $tongji->save([
                    'date'=>$date,
                    'reg'=>1
                ]);
            } 
             
            //推荐
            if($obj->rec_uid){ //查找推荐用户是否存在推荐
                
                $tongji = new TongjiRec();
                $tongji->isUpdate(false)->save([
                    'user_id'=>$obj->id,
                    'user_name'=>$obj->nickname,
                    'rec_id'=>$obj->rec_uid,
                    'rec_name'=>$obj->rec_uname,
                ]);

            }
            
        });
    }
    
    
    private function findRec($user_id,$i=1){
        global $data; 
        if($i<=2){
            $row = Member::where('id',$user_id)->field('rec_uid,rec_uname')->find();
            if($row && $row->rec_uid){
                $data[$i]['id']=$row->rec_uid;
                $data[$i]['name']=$row->rec_uname;
                $i++;
                $this->findRec($row->rec_uid,$i);
            }
        }
        return $data;
    }

    

    public function checkMobile($phone){
        return $this->where('mobile',$phone)->field('id')->find();
    }

    
    public function setNicknameAttr($name){
        return 'golds_'.$name;
    }
    
     
    protected function setRegIpAttr() {
        return request()->ip(1);
    }
    
    protected function setPasswdAttr($value){
         return think_ucenter_md5($value);
    }
    
   
    
    

}

