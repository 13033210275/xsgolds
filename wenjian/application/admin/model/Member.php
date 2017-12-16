<?php

namespace app\admin\model;

use think\Model;
// use app\admin\model\Partment;
/**
 * 用户模型
 * @author xsgolds
 */
class Member extends Model {

    protected $updateTime = false;
    protected $insert = ['reg_ip','passwd','is_reg' => 0];
    protected $createTime = 'reg_time';

    protected static function init(){
        Member::afterInsert(function ($obj) {
            Partment::where('id',$obj->part_id)->setInc('num',1);
        });
        
        Member::afterDelete(function ($obj) {
            Partment::where('id',$obj->part_id)->setDec('num',1);
        });
    }
    
  

    public function part(){
        return $this->belongsTo('Partment','part_id')->field('partname');
    }


    protected function setRegIpAttr() {
        return request()->ip(1);
    }
    
    protected function setPasswdAttr($value){
         return think_ucenter_md5($value);
    }
    
    
   
    public function getLastLoginIpAttr($value){
        if($value>0){
            return long2ip($value);
        }
        return '-';
    }

    
    public function getXsMonthAttr($value,$data){
        return date('n月',$data['xs_black_time']);
    }
    
    public function getWdMonthAttr($value,$data){
        return date('n月',$data['wd_black_time']);
    }

    public function getStatusTextAttr($value,$data){
        $arr = [0=>'禁用',1=>'开启'];
        return $arr[$data['status']];
    }
    
    public function getLoginNameAttr($value,$data){
        return $data['truename']?$data['truename']:$data['nickname'];
    }
    public function SaveQrimage($id, $qrcodeImg)
    {
        $data = [];
        $data['qrcodeImg'] = $qrcodeImg;
        $result = $this->where("id = " . $id)->update($data);
        return $path;
    }

    public static function getManagerId($id)
    {
        $partmant = Partment::where("user_id =" . $id)->select();
        if (!$partmant) {
            return 0;
        }
        else
        {
            $datas = Member::where("id", $id)->select();
            if ($datas) {
                return $id;
            }
            else
            {
                return 0;
            }
        }
    }
}
