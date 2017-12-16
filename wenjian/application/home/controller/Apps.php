<?php
/**
 * Created by PhpStorm.
 * User: SiYuChen
 * Date: 2017/10/9
 * Time: 11:12
 */
namespace app\home\controller;
class Apps extends Home{
    public function index(){
        $sor_code = input('sor_code','');
        $version_name=input('version_name','');

        //安卓端 :微信，浏览器，APP
        if(in_array($sor_code,array('2111000001','2119000001','3110000001'))){
            $android_status   =  \think\Db::name("Config")->where(name,'android_status')->field('status')->find();
            if($android_status['status']==1){
                header("Location: http://fr.xsgolds.com");
            } else {
                header("Location: http://www.xsgolds.com");
            }
        } //IOS端:微信，浏览器，APP
       else if(in_array($sor_code,array('2211000001','2219000001','3210000001'))){
            $ios_status   =  \think\Db::name("Config")->where(name,'IOS_status')->field('status')->find();
            if($ios_status['status']==1){
                header("Location: http://fr.xsgolds.com");
            }else {
                header("Location: http://www.xsgolds.com");
            }
        }else {
           header("Location: http://www.xsgolds.com");
       }
    }
}