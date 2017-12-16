<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/15 0015
 * Time: 14:58
 */

namespace app\home\controller;


use app\home\model\Adv;
use app\home\model\Document;
use app\home\validate\Banner;
use think\Db;

class Api
{

    public function Banner(){

        $validate = new Banner();
        $state = $validate->goCheck();
        if($state){

            $info = (new Adv())->apiBanner('index_banner');
            if($info){

                $msg['code'] = 200;
                $msg['info'] = $info;
            }else{

                $msg['code'] = 201;
            }
        }else{

            $msg['code'] = 400;
        }

        return json($msg);
    }
}