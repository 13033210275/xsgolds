<?php

namespace app\admin\model;

 
class TongjiPart extends \think\Model{
    
    protected $autoWriteTimestamp = false;
    
   
    public function part(){
        return $this->belongsTo('Partment','part_id')->field('partname');
    }

    public function initTongjiPart($date,$user_id=0,$part_id=0,$part_name="",$is_role=0){
        if(!$user_id){
            return ;
        }
        $data = [
            'date'=>$date,
            'user_id'=>$user_id
        ];
        $row = $this->where($data)->find();
        if($row){
            $res = $this->isUpdate(true)->save([
                'user_id'=>$user_id,
                'part_id'=>$part_id,
                'part_name'=>$part_name,
                'is_role'=>$is_role,
            ],[
                'id'=>$row->id
            ]);
        }else{
            $data['user_id'] = $user_id;
            $data['part_id'] = $part_id;
            $data['part_name'] = $part_name;
            $data['is_role'] = $is_role;
            $res = $this->save($data);
        }
        return $res;
    }
    
    
    public function updateLoanCash($date,$part_id=0,$loan_cash=0,$apply_cash=0,$rate=0){
        if(!$part_id || !$loan_cash){
            return ;
        }
        $data = [
            'date'=>$date,
            'part_id'=>$part_id
        ];
        $row = $this->where($data)->find();
        if($row){
            $res = $this->isUpdate(true)->save([
                'loan_cash'=>$row->loan_cash+$loan_cash,
                'apply_num'=>$row->apply_num+$apply_cash,
                'commission_cash'=>$row->commission_cash + $rate * $loan_cash / 100,
            ],[
                'id'=>$row->id
            ]);
        }else{
            $data['loan_cash'] = $loan_cash;
            $data['apply_num'] = $apply_cash;
            $data['commission_cash'] = $rate * $loan_cash / 100;
            $res = $this->save($data);
        }
        return $res;
    }

    public function updateLoanCashSec($date,$part_id=0,$loan_cash=0,$apply_cash=0,$rate=0, $sec_rate=0){
        if(!$part_id || !$loan_cash){
            return ;
        }
        $data = [
            'date'=>$date,
            'part_id'=>$part_id
        ];
        $row = $this->where($data)->find();
        if($row){
            $res = $this->isUpdate(true)->save([
                'sec_loan'=>$row->sec_loan+$loan_cash,
                'sec_commission'=>$row->sec_commission + $rate * $sec_rate * $loan_cash / 10000,
            ],[
                'id'=>$row->id
            ]);
        }else{
            $data['sec_loan'] = $loan_cash;
            $data['sec_commission'] = $rate * $sec_rate * $loan_cash / 10000;
            $res = $this->save($data);
        }
        return $res;
    }
}