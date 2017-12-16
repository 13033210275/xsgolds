<?php

namespace app\home\model;

use think\Model;

class Comment extends Model {

    protected $updateTime = false;

//    protected static function init() {
//        Comment::afterInsert(function ($obj) {
//            
//              $totalScore = parent::where('mert_id',$obj->mert_id)->sum('score');
//              $num = parent::where('mert_id',$obj->mert_id)->count();
//              $average = round($totalScore/$num,1);
//              
//              $o = new MerchantScore();
//              if($o->where('mert_id',$obj->mert_id)->find()){
//                   $o->isUpdate(true)->save([
//                        'user_score'=>$average,
//                    ],[
//                        'mert_id'=>$obj->mert_id
//                    ]);
//              }else{
//                  $o->isUpdate(false)->save([
//                        'user_score'=>$average,
//                        'mert_id'=>$obj->mert_id
//                    ]);
//              }
//              
//              $b = new Merchant();
//              $b->where('id', $obj->mert_id)->setInc('comment_num', 1);
//        });
//    }
    
    
    
    public function getTypeTextAttr($value,$data){
        $arr = [
            1=>'已申请',
            2=>'出额度',
            3=>'已放款',
            4=>'被拒绝',
            5=>'其他'
        ];
        return $arr[$data['type']];
    }
    
    
    public function getCommentTag($mert_id){
        $arr = [
            1=>'已申请',
            2=>'出额度',
            3=>'已放款',
            4=>'被拒绝',
            5=>'其他'
        ];
        $res =  [];
        $data = [];
        $list = $this->where([
            'mert_id'=>$mert_id,
            'status'=>1
        ])->field('type')->select(); 
        if($list){
            foreach ($list as $vo) {
                $data[$vo['type']][]=1;
            }
            foreach ($data as $key=>$vo) {
                $res[] = [
                    'status'=> $arr[$key],
                    'number'=>count($vo)
                ];
            }
        }
        return $res;
    }
    
    
    
    

}
