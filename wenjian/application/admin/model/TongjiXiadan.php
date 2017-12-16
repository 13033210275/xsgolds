<?php

namespace app\admin\model;

 
class TongjiXiadan extends \think\Model{
    
    protected $autoWriteTimestamp = true;
    protected $updateTime = false;
    
    
    protected static function init() {
        
      
        
        TongjiXiadan::afterInsert(function ($obj) {
            
            //处理机构下单统计
            // $tMertObj = new TongjiMert();
            // $row = $tMertObj->where([
            //     'date'=>$obj->date,
            //     'mert_id'=>$obj->mert_id
            // ])->find();
            // if($row->id){ //存在
            //     $flag = $tMertObj->isUpdate(true)->save([
            //         'apply_cash'=>$row['apply_cash']+$obj->apply_cash,
            //         'loan_cash'=>$row['loan_cash']+$obj->loan_cash
            //     ],[
            //         'id'=>$row->id
            //     ]);
            // }else{ //不存在
            //     $flag = $tMertObj->save([
            //         'date'=>$obj->date,
            //         'mert_id'=>$obj->mert_id,
            //         'apply_cash'=>$obj->apply_cash,
            //         'loan_cash'=>$obj->loan_cash
            //     ]);
            // } 

            $tmchantObj = new Merchant();
            $mert = $tmchantObj->where([
                'id'=>$obj->mert_id
            ])->find();
            if(!empty($mert)){
                $tMertObj = new TongjiMert();
                $row = $tMertObj->where([
                    'date'=>$obj->date,
                    'mert_id'=>$obj->mert_id
                ])->find();
                if($row->id){ //存在
                    $flag = $tMertObj->isUpdate(true)->save([
                        'apply_cash'=>$row['apply_cash']+$obj->apply_cash,
                        'loan_cash'=>$row['loan_cash']+$obj->loan_cash,
                        'mert_name'=>$mert['name'],
                        'commission_cash'=>$row['commission_cash'] + $obj->regs * $mert->aper / 100 + $obj->loan_cash * $mert->sper / 100,
                    ],[
                        'id'=>$row->id
                    ]);
                }else{ //不存在
                    $flag = $tMertObj->save([
                        'date'=>$obj->date,
                        'mert_id'=>$obj->mert_id,
                        'apply_cash'=>$obj->apply_cash,
                        'loan_cash'=>$obj->loan_cash,
                        'mert_name'=>$mert['name'],
                        'commission_cash'=>$obj->regs * $mert->aper / 100 + $obj->loan_cash * $mert->sper / 100,
                    ]);
                } 
            } 
            
            
            //查找用户信息
            $user = Member::where('mobile',$obj->phone)->field('id,truename,part_id,rec_uid,rate,sec_rate,is_reg')->find();
            if(!$user){
                return;
            }
            
            //处理中介下单统计  处理一级提成  该业务员的提成
            $tPartObj = new TongjiPart();
            if($user->rec_uid>0){ //别人推荐
                $recUser = Member::where('id',$user->rec_uid)->field('id,truename,part_id,rec_uid,rate,sec_rate,is_reg')->find();
                if($recUser->part_id > 0){
                    $tPart = Partment::where('id',$recUser->part_id)->find();
                    if(!empty($tPart)){
                        //主管只有二级佣金
                        $tPartObj->initTongjiPart($obj->date,$recUser->id,$recUser->part_id,$tPart['partname'],0);
                        if($tPart->user_id == $recUser->id){
                            $tPartObj->updateLoanCash($obj->date,$recUser->part_id,$obj->loan_cash,$obj->apply_cash,$tPart->firper);
                        }else{
                            $tPartObj->updateLoanCash($obj->date,$recUser->part_id,$obj->loan_cash,$obj->apply_cash,$tPart->firper);
                            //主管提成
                            $tPartObj->updateLoanCashSec($obj->date,$recUser->part_id,$obj->loan_cash,$obj->apply_cash,$tPart->firper,$tPart->secper);
                        }
                    }
                    $tPartObj->updateLoanCash($obj->date,$recUser->part_id,$obj->loan_cash,$obj->apply_cash, $recUser->sec_rate);
                }
            }else if($user->is_reg==0 && $user->part_id>0){ //是中介成员
                $tPart = Partment::where('id',$user->part_id)->find();
                if(!empty($tPart)){
                    //主管只有二级佣金
                    $tPartObj->initTongjiPart($obj->date,$user->id,$user->part_id,$tPart['partname'],0);
                    if($tPart->user_id == $user->id){
                        $tPartObj->updateLoanCash($obj->date,$user->part_id,$obj->loan_cash,$obj->apply_cash,$tPart->firper);
                    }else{
                        $tPartObj->updateLoanCash($obj->date,$user->part_id,$obj->loan_cash,$obj->apply_cash,$tPart->firper);
                        //主管提成
                        $tPartObj->updateLoanCashSec($obj->date,$user->part_id,$obj->loan_cash,$obj->apply_cash,$tPart->firper,$tPart->secper);
                    }
                }
            } 

            // $tPartObj = new TongjiPart();
            // $tPart = Partment::where('id',$user->part_id)->find();
            // if(!empty($tPart)){
            //     //主管只有一级佣金
            //     $tPartObj->initTongjiPart($obj->date,$user->id,$user->truename,$user->part_id,$tPart['partname'],0);
            //     if($tPart->user_id == $user->id){
            //         $tPartObj->updateLoanCash($obj->date,$user->part_id,$obj->loan_cash,$obj->apply_cash,$tPart->secper);
            //     }else{
            //         $tPartObj->updateLoanCash($obj->date,$user->part_id,$obj->loan_cash,$obj->apply_cash,$tPart->secper);
            //         //主管提成
            //         $tPartObj->updateLoanCashSec($obj->date,$user->part_id,$obj->loan_cash,$obj->apply_cash,$tPart->firper,$tPart->secper);
            //     }
            // }
           
            //处理订单列表
            $orderObj= new Apply;
            $orderObj->updateApplyStatus($user->id,$obj->mert_id);
            
            // // //处理客户借款列表
            // $objUR = new TongjiUser();
            // $objUR->save([
            //     'date'=>$obj->date,
            //     'mert_id'=>$obj->mert_id,
            //     'user_id'=>$user->id,
            //     'loan_cash'=>$obj->loan_cash,
            //     'rec_id'=>$user->rec_uid
            // ]);
            
         
            // //今日统计 
            if($user->rec_uid>0){
                $objTC = new TongjiClient();
                $rowTC = $objTC->where([
                    'date'=>$obj->date,
                    'user_id'=>$user->rec_uid
                ])->find();
                if($rowTC){
                    $objTC->where('id',$rowTC->id)->update([
                        'add_loan_num'=>$rowTC->add_loan_num+1,
                        'add_loan_cash'=>$rowTC->add_loan_cash+$obj->loan_cash
                    ]);
                }else{
                    $objTC->isUpdate(false)->save([
                        'date'=>$obj->date,
                        'user_id'=>$user->rec_uid,
                        'add_loan_num'=>1,
                        'add_loan_cash'=>$obj->loan_cash
                    ]);
                }
            }
            
            
            
            //分销提成统计
            if($user->rec_uid>0){ //存在推荐人
                $recUser = Member::where('id',$user->rec_uid)->field('id,nickname,is_reg,part_id,rec_uid,rate,sec_rate')->find();
                if(!$recUser){  
                    return;
                } 
                
                //上级推荐人获取的提成
                //ps:TongjiLoan:  apply_num'=>$obj->apply_cash,
                $loanObj = new TongjiLoan();
                $loanObj->isUpdate(false)->save([
                    'date'=>$obj->date,
                    'user_id'=>$recUser->id,
                    'mert_id'=>$obj->mert_id, 
                    'loan_cash'=>$obj->loan_cash,
                    'apply_num'=>$obj->apply_cash, 
                    'commission_cash'=>$obj->loan_cash * $recUser->rate/100,
                ]);

                if($recUser->is_reg==0 && $recUser->part_id > 0){ //是中介获取部门主管
                    $part = Partment::get($recUser->part_id);
                    if(!$part || !$part->user_id){ //部门不存在，或者无主管
                        return;
                    } 
                    $admin =  Member::where('id',$part->user_id)->field('id,truename,rate,sec_rate')->find();
                    if(!$admin){
                        return;
                    } 
                    //主管获取的提成从成员获取的提成中抽取
                    $loanObj->isUpdate(false)->save([
                        'date'=>$obj->date,
                        'user_id'=>$admin->id,
                        'mert_id'=>$obj->mert_id, 
                        'sec_loan'=>$obj->loan_cash,
                        'sec_commission'=>$obj->loan_cash * $part->firper * $part->secper / 10000,
                    ]);  
                    
                }else if($recUser->rec_uid){ //存在推荐人
                    $recUser2 = Member::where('id',$recUser->rec_uid)->field('id,part_id,rec_uid,rate,sec_rate')->find();
                    if(!$recUser2){
                        return;
                    }
                    //上上级推荐人获取的提成
                    $loanObj->isUpdate(false)->save([
                        'date'=>$obj->date,
                        'user_id'=>$recUser2->id,
                        'mert_id'=>$obj->mert_id, 
                        'sec_loan'=>$obj->loan_cash,
                        'sec_commission'=>$obj->loan_cash * $recUser->rate * $recUser->sec_rate / 10000,
                    ]); 
                }
            }  
            
        });
    }
    
    
    
    public function mert(){
        return $this->belongsTo('Merchant','mert_id')->field('name');
    }
    
     
    
}