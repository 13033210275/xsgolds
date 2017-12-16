<?php
namespace app\home\validate;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/16 0016
 * Time: 10:31
 */
use think\Validate;
use think\Request;

class BaseValidate extends Validate
{
    public function goCheck(){

        $request = Request::instance();
        $param = $request->param();
        if($this->check($param)){

            return true;
        }else{

            return false;
        }
    }

    public function getData($array)
    {
        $data = [];
        foreach ($this->rule as $key => $val){

            $data[$key] = $array[$key];
        }

        return $data;
    }

    protected function isNotEmpty($value){

        if(empty($value)){

            return false;
        }else{

            return true;
        }
    }
}