<?php
namespace app\home\validate;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/16 0016
 * Time: 10:53
 */
class Banner extends BaseValidate
{

    protected $rule = [
        'id'=>'require|isNotEmpty'
    ];
}