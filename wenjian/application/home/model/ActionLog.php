<?php

namespace app\home\model;

use think\Model;

class ActionLog extends Model {

    protected $autoWriteTimestamp = true;
    protected $updateTime = false;


    protected static function init() {
        ActionLog::afterInsert(function ($obj) {
            $date = date("Y-m-d");

            if($obj->action_id==6){ //全站统计
                $num = $obj->where('action_id',6)->whereTime('create_time','today')->count('distinct(action_ip)');
                $tongji = new TongjiTotal;
                $row = $tongji->where('date',$date)->field('id,view')->find();
                if($row){
                    $tongji->isUpdate(true)->update([
                        'view'=>$row->view+1,
                        'ip'=>$num
                    ],[
                        'id'=>$row->id
                    ]);
                }else{
                    $tongji->save([
                        'date'=>$date,
                        'view'=>1,
                        'ip'=>1
                    ]);
                }
            }elseif($obj->action_id==7){ //商家访问页统计
                $mert_id = $obj->record_id;
                $arr=[];
                $arr['action_id']=7;
                $arr['record_id']=$mert_id;
                $num = $obj->where($arr)->whereTime('create_time','today')->count('distinct(action_ip)');
                $tongji = new TjMert;
                $row = $tongji->where([
                    'date'=>$date,
                    'mert_id'=>$mert_id
                ])->find();
                if($row){ //存在访问记录
                    $tongji->isUpdate(true)->save([
                        'view'=>$row->view+1,
                        'ip'=>$num
                    ],[
                        'id'=>$row->id
                    ]);
                }else{
                    $tongji->save([
                        'date'=>$date,
                        'mert_id'=>$mert_id,
                        'view'=>1,
                        'ip'=>1
                    ]);
                }
            }elseif($obj->action_id==10){ //金额点击统计
                $tongji = new TongjiCash;
                $cash = trim($obj->record_id);
                if($cash==0){
                    return;
                }

                $field = '';
                if(strpos($cash, '-')!==false){
                    list($min,$max) = explode('-', $cash);
                    if($min==0 && $max==1){
                        $field = 'cash1';
                    }elseif($min==1 && $max==5){
                        $field = 'cash2';
                    }elseif($min==5 && $max==10){
                        $field = 'cash3';
                    }elseif($min==10 && $max==20){
                        $field = 'cash4';
                    }
                }elseif($cash=='20+'){
                    $field = 'cash5';
                }else{
                    $cash = intval($cash);
                    if(!$cash){
                        return ;
                    }
                    if($cash>0 && $cash<=1000){
                        $field = 'cash1';
                    }elseif($cash>1000 && $cash<=5000){
                        $field = 'cash2';
                    }elseif($cash>5000 && $cash<=10000){
                        $field = 'cash3';
                    }elseif($cash>10000 && $cash<=20000){
                        $field = 'cash4';
                    }elseif($cash>20000){
                        $field = 'cash5';
                    }
                }
                if(empty($field)){
                    return;
                }
                $row = $tongji->where('date',$date)->find();
                if($row){
                    $tongji->where('id',$row->id)->setInc($field,1);
                }else{
                    $tongji->save([
                        'date'=>$date,
                        $field=>1
                    ]);
                }
            }elseif($obj->action_id==13 || $obj->action_id==14){ //天数点击
                $field = '';
                $val = intval($obj->record_id);
                if(!$val) return;
                $num = $obj->action_id;
                if($num==13){
                    if($val>0 && $val<=30){  //0~1个月
                        $field = 'm1';
                    }elseif($val>30 && $val<=90){ //1~3个月
                        $field = 'm2';
                    }elseif($val>90 && $val<=180){ //3~6个月
                        $field = 'm3';
                    }elseif($val>180 && $val<=270){ //	6~9个月
                        $field = 'm4';
                    }elseif($val>270 && $val<=360){ //	9~12个月
                        $field = 'm5';
                    }elseif($val>360 && $val<=450){ //12~15个月
                        $field = 'm6';
                    }elseif($val>450 && $val<=540){ //15~18个月
                        $field = 'm7';
                    }elseif($val>540 && $val<=610){ //18~21个月
                        $field = 'm8';
                    }elseif($val>610 && $val<=720){ //	21~24个月
                        $field = 'm9';
                    }
                }elseif($num==14){
                    if($val>0 && $val<=1){  //0~1个月
                        $field = 'm1';
                    }elseif($val>1 && $val<=3){ //1~3个月
                        $field = 'm2';
                    }elseif($val>3 && $val<=6){ //3~6个月
                        $field = 'm3';
                    }elseif($val>6 && $val<=9){ //	6~9个月
                        $field = 'm4';
                    }elseif($val>9 && $val<=12){ //	9~12个月
                        $field = 'm5';
                    }elseif($val>12 && $val<=15){ //12~15个月
                        $field = 'm6';
                    }elseif($val>15 && $val<=18){ //15~18个月
                        $field = 'm7';
                    }elseif($val>18 && $val<=21){ //18~21个月
                        $field = 'm8';
                    }elseif($val>21 && $val<=24){ //	21~24个月
                        $field = 'm9';
                    }
                }
                if(empty($field)){
                    return;
                }
                $tongji = new TongjiDate;
                $row = $tongji->where('date',$date)->find();
                if($row){
                    $tongji->where('id',$row->id)->setInc($field,1);
                }else{
                    $tongji->save([
                        'date'=>$date,
                        $field=>1
                    ]);
                }
            }elseif($obj->action_id==11){
                $field = '';
                $val = intval($obj->record_id);
                if(!$val) return;
                $num = $obj->action_id;
                // // 成功率高=1 速度快=2 额度高=3 利率低=4
                if($val==1){
                    $field='t1';
                }elseif($val==2){
                    $field='t2';
                }elseif($val==3){
                    $field='t3';
                }elseif($val==4){
                    $field='t4';
                }
                $tongji = new TongjiTag;
                $row = $tongji->where('date',$date)->find();
                if($row){
                    $tongji->where('id',$row->id)->setInc($field,1);
                }else{
                    $tongji->save([
                        'date'=>$date,
                        $field=>1
                    ]);
                }
            }


        });
    }

}
