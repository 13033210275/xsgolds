<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/24
 * Time: 19:51
 */
namespace app\kashu\controller;
use think\Controller;


/**
 * 后台首页控制器
 */
class Index extends Controller  {

    /**
     * 后台首页
     * @author xsgolds
     */

    public function index(){

        return $this->fetch();
    }

    public function letter(){

        return $this->fetch();
    }
    public function home(){

        $this->redirect('/');
    }

}
