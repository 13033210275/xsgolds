<?php
// +----------------------------------------------------------------------
// | xs
// +----------------------------------------------------------------------
// | Copyright (c) 2013 All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <code-tech.diandian.com>
// +----------------------------------------------------------------------
 
namespace addons\socialcomment;
use app\common\controller\Addon;
/**
 * 通用社交化评论插件
 * @author thinkphp
 */

    class Socialcomment extends Addon{

        public $info = array(
            'name'=>'socialcomment',
            'title'=>'通用社交化评论',
            'description'=>'集成了各种社交化评论插件，轻松集成到系统中。',
            'status'=>1,
            'author'=>'thinkphp',
            'version'=>'0.1'
        );

        public function install(){
            return true;
        }

        public function uninstall(){
            return true;
        }

        //实现的pageFooter钩子方法
        public function documentDetailAfter($param){ 
            $this->assign('addons_config', $this->getConfig());
            return $this->fetch('comment');
        }
    }