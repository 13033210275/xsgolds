<?php
//ok
namespace app\admin\controller;
use app\admin\model\Partment;
use app\admin\model\Role as mRole;
use app\admin\model\Menu;
use app\admin\model\Member;
use app\admin\model\RoleUser;
/**
 * 权限管理控制器
 * Class AuthManagerController

 */
class Role extends Admin{

    
    

    //角色权限管理首页
    public function index(){
        $part_id = input('part_id/d',0);
        $part = new Partment;
        $partSelect = $part->getPartmentSelect($part_id);
        $this->assign('partSelect',$partSelect);
        $map = [];
        if($part_id>0){
            $map['part_id'] = $part_id;
        }
        $list = mRole::where($map)->order('id desc')->paginate(20);
        $page = $list->render(); 
        $this->assign('_list',$list);
        $this->assign('_page',$page);
        $this->assign('meta_title','角色管理');
        return $this->fetch();
    }

    //创建用户组(OK)
    public function add(){
        if (request()->isPost()) {
            $result = $this->validate($_POST,[
                'part_id'=>'gt:0',
                'title'=>'require',
            ],[
                'part_id.gt'=>'部门必须选择',
                'title.require'=>'请填写角色组名称',
            ]);
            if(true!==$result){
                finish(1,$result);
            }    
            $obj = new mRole($_POST);
            $flag = $obj->save();
            if($flag){
                finish(0,'',url('index'));
            }else{
                finish(1,'添加失败');
            }
        }else{ 
            $part = new Partment;
            $partSelect = $part->getPartmentSelect(0);
            $this->assign('partSelect',$partSelect);
            
            $statusRadio = bulid_form('radio', ['否','是'], 'status', 1);
            $this->assign('statusRadio',$statusRadio);
        
            $this->assign('meta_title','新增角色组');
            return $this->fetch();
        }
    }
         
    //会员状态修改(OK)
    public function changeStatus() {
        $id = input('id/d');
        $cmd = input('cmd/s'); 
        $obj = input('obj/s'); 
        if (empty($id) || empty($cmd) || empty($obj)) {
            finish(1,'参数错误'); 
        }
        if($obj=='role'){
            $object = new mRole;  
        }
        if($this->handle($object, $id, $cmd)){ 
            finish(0);
        } 
        finish(1,'操作失败');
    }
    
    //编辑(OK)
    public function edit($id){
        $row = mRole::get($id); 
        if (request()->isPost()) {
            if(!$row){
                finish(1,'角色已经不存在');
            }
            $result = $this->validate($_POST,[
                'part_id'=>'gt:0',
                'title'=>'require',
            ],[
                'part_id.gt'=>'部门必须选择',
                'title.require'=>'请填写角色组名称',
            ]);
            if(true!==$result){
                finish(1,$result);
            }    
            $isupdate = false;
            $field = ['title','part_id','description','status'];
            foreach ($field as $vo) {
                $val = input($vo);
                if($val!=$row->$vo){
                    $isupdate = true;
                    $row->$vo = $val; 
                }
            } 
            if(!$isupdate){
                finish(1,'未修改任何数据');
            } 
            $flag = $row->save(); 
            if($flag){
                finish(0,'',url('index'));
            }
            finish(1,'更新失败');
        }else{
            if(!$row){
                $this->error('角色已经不存在');
            }
            $part = new Partment;
            $partSelect = $part->getPartmentSelect($row->part_id);
            $this->assign('partSelect',$partSelect);
            
            $statusRadio = bulid_form('radio', ['否','是'], 'status', $row->status);
            $this->assign('statusRadio',$statusRadio);
        
        
            $this->assign('row',$row);
            $this->assign('meta_title','编辑角色组');
            return $this->fetch();
        }
        
    }
    
    //访问授权页面(OK)
    public function access($role_id=null){
        $obj = new mRole();
        $row = $obj->get($role_id);
        if (request()->isPost()) {
            if(!$row){
                finish(1,'角色已经不存在');
            }
            $rules = input('rules/a');
            if(empty($rules)){
                finish(1,'请选择授权选项');
            }
            $row->rules = $rules;
            $flag = $row->save();
            if($flag){
                finish(0,'',url('Role/index'));
            }
            finish(1,'授权失败');
        }else{
            if(!$row){
                $this->error('角色已经不存在');
            }
            $roleSelect = $obj->getRoleSelect($role_id);
            $this->assign('roleSelect',$roleSelect);
            $res = Menu::where('status',1)->field('id,title,pid')->order('sort asc')->select()->toArray();
            $list = merge_node($res); 
            $this->assign('node_list',  $list); 

            $this->assign('exsit',$row->rules_text);
            $this->assign('row',$row);
            $this->assign('title','部门：'.$row->part->partname.' -> '.$row->title.' 权限分配');
            $this->assign('meta_title','角色权限分配');
            return $this->fetch();
        }
    }
    
    
    //用户组授权用户列表(OK)
    public function user($role_id=null){
        $obj = new mRole();
        $row = $obj->get($role_id);
        if(request()->isPost()){
            if(!$row){
                finish(1,'角色已经不存在');
            }
            $user = input('user/a');
            if(empty($user)){
                finish(1,'请选择授权的成员');
            }
            $data = [];
            foreach ($user as $vo) {
                $data[] = [
                    'user_id'=>$vo,
                    'role_id'=>$row->id
                ];
            }
            $objUser = new RoleUser;
            $objUser->where('role_id',$row->id)->delete();
            $flag = $objUser->saveAll($data);
            if($flag){
                finish(0,'',url('Role/index'));
            }
            finish(1,'授权失败');
        }else{
            if(!$row){
                $this->error('角色已经不存在');
            }
            $roleSelect = $obj->getRoleSelect($role_id);
            $this->assign('roleSelect',$roleSelect);
            
            $res = Member::where([
                'is_reg'=>0,
                'status'=>1,
            ])->field('id,mobile,truename,part_id')->select();
            $data = [];
            foreach ($res as $vo) {
                $data[$vo->part_id]['id'] = $vo->part_id;
                $data[$vo->part_id]['partname'] = $vo->part->partname;
                $data[$vo->part_id]['child'][]=[
                    'id'=>$vo->id,
                    'name'=>$vo->mobile."[".$vo->truename."]"
                ];
            }
            
            //获取已经关联的用户
            $exsit = [];
            if($row->user){
                foreach ($row->user as $wo) {
                     $exsit[] = $wo->user_id;
                }
            }
            $exsit = json_encode($exsit);
            
            $this->assign("row",$row);
            $this->assign('exsit',$exsit);
            $this->assign('list',$data);
            $this->assign('meta_title' , '成员授权');
            $this->assign('title','部门：'.$row->part->partname.' -> '.$row->title.' 成员授权');
            return $this->fetch();
        } 
        
    } 

}
