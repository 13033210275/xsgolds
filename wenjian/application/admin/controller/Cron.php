<?php

namespace app\admin\controller;


use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\admin\model\TongjiXiadan;
use app\admin\model\TongjiMert;
use app\admin\model\ActionLog;
use app\admin\model\TongjiTotal;
use app\admin\model\Member;
use app\admin\model\Apply;
use app\admin\model\TjMert;


class Cron extends Command {
   


    protected function configure() { 
        $this->setName('handle')->setDescription('Command Printer'); 
    }
 

    protected function execute(Input $input, Output $output) {
        ini_set('max_execution_time', 0);  
        ini_set('date.timezone','Asia/Shanghai');
        $this->dataAnalysis();
        exit;
        while (true) {
            if(date("His")=='000000'){
                $this->dataAnalysis();
            }
        }
      
    }
    

    //每日数据统计
    public function dataAnalysis(){
        $totalObj = new TongjiTotal();
        $today = date('Y-m-d',  strtotime('-1 days'));
        
        //今日注册用户
        $objMember = new Member;
        $todayRegNum = $objMember->whereTime('reg_time', 'yestoday')->count();
      
        
        //总申请数
        $objApply = new Apply;
        $totalApplyNum = $objApply->whereTime('create_time', 'yestoday')->count();
        
        
        $tmp = $totalObj->where('date',$today)->field('id')->find();
        if($tmp){
            $totalObj->where('id',$tmp->id)->setInc('reg',$todayRegNum);
            $totalObj->where('id',$tmp->id)->setInc('apply',$totalApplyNum);
            $totalObj->where('id',$tmp->id)->setInc('mert_apply',$totalApplyNum);
        }else{
            $totalObj->save([
                'date'=>$today,
                'reg'=>$todayRegNum,
                'apply'=>$totalApplyNum,
                'mert_apply'=>$totalApplyNum       
            ]);
        } 
        
        $obj = new ActionLog;  
        $res = $obj->select();
        
        if($res){
            $data_6 = [];
            $data_7 = [];
            $ips = [];
            foreach ($res as $vo) { 
                $date = date('Y-m-d',  strtotime($vo->create_time)); 
                $ip = ip2long($vo->action_ip);
                if($vo->action_id==6){ //全站统计
                    $ips[$date][$ip]=1;
                    $data_6[$date][]=1;
                    $obj->where('id',$vo->id)->delete();
                }elseif($vo->action_id==7){ //商家详情页显示统计
                    $data_7[$date][$vo->record_id][]=1;
                    $ips[$date][$vo->record_id][$ip]=1;
                    $obj->where('id',$vo->id)->delete();
                }
            }
            if(!empty($data_6)){
                foreach ($data_6 as $key=>$vo) {
                    $num = count($data_6[$key]);
                    $ipnum = count($ips[$key]);
                    $row = $totalObj->where('date',$key)->field('id')->find();
                    if($row){
                        $totalObj->where('id',$row->id)->setInc('view',$num);
                        $totalObj->where('id',$row->id)->setInc('ip',$ipnum);
                    }else{
                        $totalObj->save([
                            'date'=>$key,
                            'view'=>$num,
                            'ip'=>$ipnum
                        ]);
                    }
                }
            }
            
             
            //商家详情处理
            $tjMertObj = new TjMert();
            if(!empty($data_7)){
                foreach ($data_7 as $day=>$vo) { 
                    foreach ($vo as $mert_id=>$wo) {
                        $num = count($wo);
                        $ipnum = count($ips[$day][$mert_id]);
                        $row = $tjMertObj->where([
                            'date'=>$day,
                            'mert_id'=>$mert_id
                        ])->field('id')->find();
                        if($row){
                            $tjMertObj->where('id',$row->id)->setInc('view',$num);
                            $tjMertObj->where('id',$row->id)->setInc('ip',$ipnum);
                        }else{
                            $tjMertObj->save([
                                'date'=>$day,
                                'mert_id'=>$mert_id,
                                'view'=>$num,
                                'ip'=>$ipnum
                            ]);
                        }
                    }  
                }
            }
            
        } 
    }
    
    
  
    //处理下单
    public function start(){
        $o = new TongjiXiadan;
        $list = $o->where('handle',0)->select();
        if(empty($list)){
            exit();
        }
        
        foreach ($list as $vo) {
            $obj = new TongjiMert;
            $row = $obj->where([
                'date'=>$vo->date,
                'mert_id'=>$vo->mert_id
            ])->find();
            if($row->id){ //存在
                $flag = $obj->save([
                    'apply_cash'=>$row['apply_cash']+$vo->apply_cash,
                    'loan_cash'=>$row['loan_cash']+$vo->loan_cash
                ],[
                    'id'=>$row->id
                ]);
            }else{ //不存在
                $flag = $obj->save([
                    'date'=>$vo->date,
                    'mert_id'=>$vo->mert_id,
                    'mert_name'=>$vo->mert->name,
                    'apply_cash'=>$vo->apply_cash,
                    'loan_cash'=>$vo->loan_cash
                ]);
            }
            if($flag){
                $o->save([
                    'handle'=>1
                ],[
                    'id'=>$vo->id
                ]);
            }
        }
        exit;
        
    }
}
 
    