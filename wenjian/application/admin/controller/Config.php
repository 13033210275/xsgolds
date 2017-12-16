<?php
// +----------------------------------------------------------------------
// | xs
// +----------------------------------------------------------------------
// | xs copy right
// +----------------------------------------------------------------------
// | * @author xsgolds
// +---------------------------------------------------------------------- 

namespace app\admin\controller;

/**
 * 后台配置控制器
 * @author xsgolds
 */
class Config extends Admin {

    /**
     * 配置管理
     * @author xsgolds
     */
    public function index(){
        /* 查询条件初始化 */
        $map = array();
        $map  = array('status' => 1);
        $group=input('group');
        $name=input('name');
        if(isset($group)){
            $map['group']   =   input('group',0);
        }
        if(isset($name)){
            $map['name']    =   array('like', '%'.(string)input('name').'%');
        } 
        $list = $this->lists('Config', $map,'sort,id');  
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);

        $this->assign('group',config('config_group_list'));
        $this->assign('group_id',input('get.group',0));
        $this->assign('list', $list);
        $this->assign('meta_title' , '配置管理');
        return $this->fetch();
    }

    /**
     * 新增配置
     * @author xsgolds
     */
    public function add(){
        if($this->request->isPost()){
            $Config = \think\Loader::model('Config');
            $data  = $this->request->Post();
            $validate = \think\Loader::validate('config');
            if(!$validate->check($data)){
                return $this->error($validate->getError());
            }
            $data = $Config->create($data);
            if($data){ 
                cache('db_config_data',null);
                $this->success('新增成功', url('index')); 
            } else {
                $this->error($Config->getError());
            }
        } else {
            $this->assign('meta_title','新增配置');
            $this->assign('info',null);
            return $this->fetch('edit');
        }
    }

    /**
     * 编辑配置
     * @author xsgolds
     */
    public function edit($id = 0){
        if(request()->isPost()){
            $Config = \think\Loader::model('Config');
            $data  = $this->request->Post();
            $validate = \think\Loader::validate('config');
            if(!$validate->check($data)){
                return $this->error($validate->getError());
            }
            $update = $Config->allowField(true)->update($data); 
            if($update){ 
                cache('db_config_data',null);
                    //记录行为
                action_log('update_config','config',$data['id'],UID);
                $this->success('更新成功', Cookie('__forward__')); 
            } else {
                $this->error($Config->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = \think\Db::name('Config')->field(true)->find($id);

            if(false === $info){
                $this->error('获取配置信息错误');
            }
            $this->assign('info', $info);
            $this->assign('meta_title', '编辑配置');
            return $this->fetch();
        }
    }

    /**
     * 批量保存配置
     * @author xsgolds
     */
    public function save($config){
        if($config && is_array($config)){
            $Config = \think\Db::name('Config');
            foreach ($config as $name => $value) {
                $map = array('name' => $name);
                $Config->where($map)->setField('value', $value);
            }
        }
        cache('db_config_data',null);
        $this->success('保存成功！');
    }

    /**
     * 删除配置
     * @author xsgolds
     */
    public function del(){
        $id = array_unique((array)input('id/a',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(\think\Db::name('Config')->where($map)->delete()){
            cache('db_config_data',null);
            //记录行为
            action_log('update_config','config',$id,UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    // 获取某个标签的配置参数
    public function group() {
        $id     =   input('id',1);
        $type   =   config('config_group_list'); 
        $list   =   \think\Db::name("Config")->where(array('status'=>1,'hide'=>0,'group'=>$id))->field('id,name,title,extra,value,remark,type')->order('sort')->select();
        if($list) {
            $this->assign('list',$list);
        }
        $this->assign('id',$id);
        $this->assign("meta_title", $type[$id].'设置');
        return $this->fetch();
    }

    /**
     * 配置排序
     * @author xsgolds
     */
    public function sort(){
    	if($this->request->isGet()){
            $ids = input('ids');

            //获取排序的数据
            $map = array('status'=>array('gt',-1));
            if(!empty($ids)){
                $map['id'] = array('in',$ids);
            }elseif(input('group')){
                $map['group']	=	input('group');
            }
            $list = \think\Db::name('Config')->where($map)->field('id,title')->order('sort asc,id asc')->select();

            $this->assign('list', $list);
            $this->assign('meta_title', '配置排序');
            return $this->fetch();
        }elseif (request()->isPost()){
            $ids = input('ids');
            $ids = explode(',', $ids);
            foreach ($ids as $key=>$value){
                $res = \think\Db::name('Config')->where(array('id'=>$value))->setField('sort', $key+1);
            }
            if($res !== false){
                $this->success('排序成功！',Cookie('__forward__'));
            }else{
                $this->error('排序失败！');
            }
        }else{
            $this->error('非法请求！');
        }
    }

    // 移动端配置
    public function status() {
        $android_status = input('android_status/s','');
        $ios_status = input('ios_status/s','');
         $android_type =  [
            '0'=>'正式环境',
            '1'=>'审核环境'
        ];
        $ios_type =  [
            '0'=>'正式环境',
            '1'=>'审核环境'
        ];
        if($android_status!=''){

            \think\Db::name("Config")->where(name,'android_status')->setField('status', $android_status);
            $AndroidSelect = bulid_form('select',$android_type, 'android_type',$android_status);
            $this->assign('AndrosidSelect',$AndroidSelect);
        }else{
            $android_status =  \think\Db::name("Config")->where(name,'android_status')->field('status')->find();
            $AndroidSelect = bulid_form('select',$android_type, 'android_type',$android_status['status']);
            $this->assign('AndrosidSelect',$AndroidSelect);
        }

        if($ios_status!=''){
            \think\Db::name("Config")->where(name,'IOS_status')->setField('status', $ios_status);
            $IosSelect = bulid_form('select',$ios_type, 'ios_type',$ios_status);
            $this->assign('IosSelect',$IosSelect);
        }else{
            $ios_status   =  \think\Db::name("Config")->where(name,'IOS_status')->field('status')->find();
            $IosSelect = bulid_form('select',$ios_type, 'ios_type',$ios_status['status']);
            $this->assign('IosSelect',$IosSelect);
        }

       /* $android_status   =  \think\Db::name("Config")->where(name,'android_status')->field('status')->find();
        $ios_status   =  \think\Db::name("Config")->where(name,'IOS_status')->field('status')->find();
        $this->assign('android_status',$android_status['status']);
        $this->assign('ios_status',$ios_status['status']);*/
        return $this->fetch();
    }

    /**
     * 状态修改
     */
    public function changeStatus() {
        $obj = input('type');
        $value = input('value');
        $Config = \think\Db::name('Config');
            $map = array('name' => $obj);
            $row=$Config->where($map)->setField('status', $value);

            if($row){
            finish(0);
        }
        finish(1,'操作失败');
    }

}