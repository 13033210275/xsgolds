<?php


namespace app\home\controller;


/**
 * 前台用户后台公共控制器
 */
class Base extends Home {
    protected function _initialize(){
        /* 用户登录检测 */
        $k= 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        is_login() ||  $this->error('请先登录', url('login/index')."?u=".$k);
        parent::_initialize();
    }

}
