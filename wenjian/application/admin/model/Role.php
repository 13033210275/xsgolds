<?php

namespace app\admin\model;

use think\Model;
 
class Role extends Model {

    protected $autoWriteTimestamp = false;
    protected $insert = ['type'=>1,'module' => 'admin'];

    
    public function part(){
        return $this->belongsTo('Partment','part_id')->field('partname');
    }
    
    
    public function user(){
        return $this->hasMany('RoleUser','role_id')->field('user_id');
    }
 
    
    public function setRulesAttr($value){
        return implode(',', $value);
    }

    public function getStatusTextAttr($value,$data){
        $arr = [0=>'禁用',1=>'开启'];
        return $arr[$data['status']];
    }

    
    public function getRulesTextAttr($value,$data){
        if($data['rules']){
            $arr = explode(',', $data['rules']);
            foreach ($arr as &$vo) {
                $vo = intval($vo);
            }
            return json_encode($arr);
        }
        return json_encode([]);
    }

    public function getRoleSelect($curr=0){
        $data = ['请选择角色'];
        $list = $this->field('id,title,part_id')->select();
        if($list){
            foreach ($list as $vo) {
                $data[$vo->id] = '['.$vo->part->partname.'] - '.$vo->title;
            }
        }
        return bulid_form('select', $data, 'role_id',$curr);
    }
}
