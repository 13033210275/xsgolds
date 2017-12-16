<?php

namespace app\admin\model;

use think\Model;
use app\admin\model\Merchant;
use app\admin\model\Apply;


class MemberRec extends Model {

    protected $updateTime = false;

 
    
    public function user(){
        return $this->belongsTo('Member','rec_id')->field('truename,nickname,mobile');
    }

    
    public function member(){
        return $this->belongsTo('Member','user_id')->field('nickname,mobile,sor_code');
    }

    public static function applyed($id)
    {
        $appList = Apply::where("user_id", $id)->select();
        if (!$appList) {
            return "";
        }
        $sql = "id in (";
        foreach ($appList as $vo) {
            $sql = $sql.$vo->mert_id.",";
        }
        $sql = substr($sql, 0, -1);
        $sql = $sql.")";
        // 去重复
        $list = Merchant::field("name")->where($sql)->order('rise', 'asc')->distinct(true)->select();
        $names = "";
        foreach ($list as $vo) {
            $names = $names.$vo["name"].",";
        }
        $names = substr($names, 0, -1);
        return $names;
        // $list =  Merchant::alias('a')->field('name')->join(' apply'.' b','a.id = b.mert_id and b.user_id ='.$id)->select();
    }

}
