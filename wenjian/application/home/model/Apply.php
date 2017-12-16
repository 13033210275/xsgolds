<?php

namespace app\home\model;

use think\Model;

class Apply extends Model {

    protected $autoWriteTimestamp = true;
    protected $updateTime = false;

    
    protected static function init() {
        Apply::afterInsert(function ($obj) {
            $mert_id = $obj->mert_id;
            
            
            //更新商家申请数
            $o = new MerchantScore;
            $rest = $o->where('mert_id',$mert_id)->find();
            if($rest){
                $o->where('id',$rest->id)->setInc('apply_num',1);
            }else{
                $o->save([
                    'mert_id'=>$mert_id,
                    'apply_num'=>1
                ]);
            }
            
            
            //总统计
            $num = $obj->whereTime('create_time','today')->distinct(true)->field('mert_id')->count();
            $date = date("Y-m-d");
            $tongji = new TongjiTotal;
            $row = $tongji->where('date',$date)->field('id,apply')->find();
            if($row){
                $tongji->isUpdate(true)->save([
                    'apply'=>$row->apply+1,
                    'mert_apply'=>$num
                ],[
                    'id'=>$row->id
                ]);
            }else{
                $tongji->save([
                    'date'=>$date,
                    'apply'=>1,
                    'mert_apply'=>$num
                ]);
            } 
            
            //机构申请统计
            $o = new TjMert;
            $res = $o->where([
                'mert_id'=>$mert_id,
                'date'=>$date
            ])->find();
            if($res){
                $o->isUpdate(true)->save([
                    'apply'=>$res->apply+1,
                    'link'=>$res->link+1,
                ],[
                    'id'=>$res->id
                ]);
            }else{
                $tongji->save([
                    'date'=>$date,
                    'apply'=>1,
                    'link'=>1,
                    'mert_id'=>$mert_id
                ]);
            } 
            
            
        });
    }

}
