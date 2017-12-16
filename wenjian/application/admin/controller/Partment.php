<?php
//ok
namespace app\admin\controller;
use app\admin\model\Partment as Pment;
use app\admin\model\Member;
use app\admin\model\RoleUser;
use app\admin\model\Role;



class Partment extends Admin {

    /**
     * 部门管理首页（OK）
     */
    public function index() {
        $list = Pment::order('id desc')->paginate(20);
        $page = $list->render(); 
        $this->assign('_list',$list);
        $this->assign('_page',$page);
        $this->assign('meta_title', '部门列表'); 
        return $this->fetch();
    }

     
    //编辑部门（OK）
    public function edit($id=0) { 
         if (request()->isPost()) {
            $id = input('post.id/d',0);
            if(!$id){
                finish(1,'参数错误');
            }
            $parname = input('post.partname/s','');
            if(!$parname){
                finish(1,'请输入部门名称');
            }
            $obj = new Pment();
            $row = $obj->get($id); 
            if(!$row){
                finish(1,'部门不存在');
            } 
            $flag = $obj->allowField(true)->save($_POST,[
                'id'=>$id
            ]);
            //更新部门主管
            Member::where('id',$obj->user_id)->setField('part_id', $id);
            if($flag){
                //updatePartmentMemberRate($id, $obj->user_id, $obj->firper, $obj->secper);
                //更新本部门下所有人的提成系数
                $list = Member::field('id,part_id')->where('part_id',$id)->select();
                if(!empty($list)){
                    foreach ($list as $vo) {
                        if($vo->id == $obj->user_id){
                            Member::where('id',$vo->id)->setField('rate', $obj->firper);
                        }else{
                            Member::where('id',$vo->id)->setField('rate', $obj->secper);
                        }
                    }
                }

                finish(0,'',url('Partment/index'));
            }
            finish(1,'更新失败');
         }else{ 
            if(!$id){
                $this->error('参数错误');
            }
            $row = Pment::get($id); 
            if(!$row){
                $this->error('部门不存在');
            } 
            $user = ['请选择用户'];
            $res = Member::field('id,nickname')->where('is_reg',0)->select();
            if($res){
                foreach ($res as $vo) {
                    $user[$vo->id] = $vo->nickname;
                }
            }
            $userSelect = bulid_form('select', $user, 'user_id',$row->user_id);
            $this->assign('userSelect',$userSelect);

            $statusRadio = bulid_form('radio', ['否','是'], 'status', $row->status);
            $this->assign('statusRadio',$statusRadio);
            $mediRadio = bulid_form('radio', ['否','是'], 'is_medi',$row->is_medi);
            $this->assign('mediRadio',$mediRadio);

            $this->assign('row',$row);
            $this->assign('meta_title', '编辑部门');
            return $this->fetch();
         }
    }
    
    public function updatePartmentMemberRate($id, $user_id, $firper, $secper){
        //更新本部门下所有人的提成系数  $user_id主管id
        $list = Member::field('id,part_id')->where('part_id',$id)->select();
        if(!empty($list)){
            foreach ($list as $vo) {
                if($vo->id == $user_id){
                    Member::where('id',$vo->id)->setField('rate', $firper);
                }else{
                    Member::where('id',$vo->id)->setField('rate', $secper);
                }
            }
        }
    }
     

    //添加部门（OK）
    public function add() { 
        if (request()->isPost()) {
            $parname = input('post.partname/s','');
            if(!$parname){
                finish(1,'请输入部门名称');
            }
            $obj = new Pment($_POST);
            if($obj->where('partname',$parname)->field('id')->find()){
                finish(1,'部门名称已经存在');
            }
            $flag = $obj->save();
            if($flag){
                finish(0,'',url('index'));
            }else{
                finish(1,'添加失败');
            }
        }else{
            
            $user = ['请选择用户'];
            $res = Member::field('id,nickname')->where('is_reg',0)->select();
            if($res){
                foreach ($res as $vo) {
                    $user[$vo->id] = $vo->nickname;
                }
            }
            $userSelect = bulid_form('select', $user, 'user_id',0);
            $this->assign('userSelect',$userSelect);
            
            $statusRadio = bulid_form('radio', ['否','是'], 'status', 1);
            $this->assign('statusRadio',$statusRadio);
            $mediRadio = bulid_form('radio', ['否','是'], 'is_medi', 0);
            $this->assign('mediRadio',$mediRadio);
            $this->assign('meta_title', '新增部门');
            return $this->fetch();
        }
    }
    
            
    /**
     * 会员状态修改(OK)
     */
    public function changeStatus() {
        $id = input('id/d');
        $cmd = input('cmd/s'); 
        $obj = input('obj/s'); 
        if (empty($id) || empty($cmd) || empty($obj)) {
            finish(1,'参数错误'); 
        }
        if($obj=='pment'){
            $object = new Pment;  
        }elseif($obj=='user'){
            $object = new Member;  
        }
        if($this->handle($object, $id, $cmd)){ 
            finish(0);
        } 
        finish(1,'操作失败');
    }


//成员列表 状态修改
    public function changeStatusOpen() {
        $id = input('id/d');
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }
        $map['id']=$id;
        $this->resume('member', $map);



    }

    public function changeStatusClose() {
        $id = input('id/d');
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }
        $map['id']=$id;
        $this->forbid('member', $map);

    }

    public function changeStatusDel() {
        $id = input('id/d');
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }
        $map['id']=$id;
        $this->_delete('member', $map);
    }

    //成员列表（OK）
    public function user(){
        $part_id = input('part_id/d',0);
        $wd = trim(input('wd/s',''));
        $map = [
            'is_reg'=>0,
            'id'=>['neq',config('user_administrator')]
        ];
        if($part_id>0){
            $map['part_id'] = $part_id;
        }
        if($wd){
            $map['mobile|truename|nickname'] = ['like','%'.$wd.'%'];
        }
        
        $list = Member::where($map)->field('id,mobile,truename,part_id,rate,status,last_login_ip,last_login_time')->order('id desc')->paginate(20);
        $page = $list->render(); 
        $this->assign('_list',$list);
        $this->assign('_page',$page); 
        $this->assign('meta_title', '成员列表');
        $this->_getPartmentSelect($part_id);
        return $this->fetch();
    }
    
    //添加成员（OK）
    public function useradd(){
        if (request()->isPost()) {
            $result = $this->validate($_POST,[
                'mobile'=>'number|length:11',
                'password'=>'length:6,13',
                'part_id'=>'require',
                'truename'=>'require',
            ],[
                'mobile.number'=>'手机必须',
                'mobile.length'=>'手机不正确',
                'password.length'=>'密码长度为6-13位之间',
                'part_id.require'=>'部门必须选择',
                'truename.require'=>'请填写真实姓名',
            ]);
            if(true!==$result){
                finish(1,$result);
            }   
            $obj = new Member($_POST);
            $obj->nickname = 'golds_'.$obj->mobile;
            if($obj->where('mobile',input('mobile'))->field('id')->find()){
                finish(1,'该手机号已经存在');
            }
            $flag = $obj->allowField(true)->save();
            $res = Pment::field('id,user_id,firper,secper')->where('id', $_POST['part_id'])->select();
            if(!empty($res)){
                if($res[0]->user_id == $id){
                    Member::where('id',$id)->setField('rate', $res[0]->firper);
                }else{
                    Member::where('id',$id)->setField('rate', $res[0]->secper);
                }
            }else{
                Member::where('id',$id)->setField('rate', 0);
            }
            if($flag){
                finish(0,'',url('user'));
            }
            finish(1,'添加失败');
        }else{
            $this->_getPartmentSelect();
            $statusRadio = bulid_form('radio', ['否','是'], 'status', 1);
            $this->assign('statusRadio',$statusRadio);
            $this->assign('meta_title', '新增成员');
            return $this->fetch();
        }
    }
    
    
    //修改成员
    public function useredit($id){
        $row = Member::get($id); 
        if (request()->isPost()) {
            if(!$row){
                finish(1,'成员已经不存在');
            }
            $mobile = input('mobile');
            $passwd = input('passwd');
            $result = $this->validate($_POST,[
                'mobile'=>'number|length:11', 
                'part_id'=>'require',
                'truename'=>'require'
            ],[
                'mobile.number'=>'手机必须',
                'mobile.length'=>'手机不正确',
                'part_id.require'=>'部门必须选择',
                'truename.require'=>'请填写真实姓名',
            ]);
            if(true!==$result){
                finish(1,$result);
            }   
            if($row->mobile!=$mobile && Member::where('mobile',$mobile)->field('id')->find()){
                finish(1,'该手机号已经存在');
            }
            
            $isupdate = false;
            $field = ['mobile','truename','status','part_id'];
            foreach ($field as $vo) {
                if($_POST[$vo]!=$row->$vo){
                    $isupdate = true;
                    $row->$vo = $_POST[$vo];
                    if($field=='mobile'){
                        $row->nickname = 'golds_'.$_POST[$vo];
                    }
                }
            }
            if($passwd){
                $row->passwd = $passwd;
                $isupdate = true;
            }
            if(!$isupdate){
                finish(1,'未修改任何数据');
            } 
            $flag = $row->save(); 
            $res = Pment::field('id,user_id,firper,secper')->where('id', $_POST['part_id'])->select();
            if(!empty($res)){
                if($res[0]->user_id == $id){
                    Member::where('id',$id)->setField('rate', $res[0]->firper);
                }else{
                    Member::where('id',$id)->setField('rate', $res[0]->secper);
                }
            }else{
                Member::where('id',$id)->setField('rate', 0);
            }
            if($flag){
                finish(0,'',url('user'));
            }
            finish(1,'更新失败');
        }else{
            if(!$row){
                $this->error('成员已经不存在');
            }
            $this->_getPartmentSelect($row->part_id);
            $res = Pment::field('id,user_id,firper,secper')->where('id',$row->part_id)->select();
            if($res){
                $row->firper = $res[0]->firper;
                $row->secper = $res[0]->secper;
            }else{
                $row->firper = 0;
                $row->secper = 0;
            }


            $statusRadio = bulid_form('radio', ['否','是'], 'status', $row->status);
            $this->assign('statusRadio',$statusRadio);
            $this->assign('row',$row);
            $this->assign('meta_title', '编辑成员');
            return $this->fetch();
        }
    }
    
    
    private function _getPartmentSelect($curr=0){
        $data = ['请选择部门'];
        $res = Pment::field('id,partname,is_medi,firper,secper')->where('status',1)->order('is_medi asc')->select();
        if($res){
            foreach ($res as $vo){
                $name = $vo->partname;
                if($vo->is_medi){
                    $name = '[中介]'.$name;
                }
                $data[$vo->id] = $name;
            }
        }
        $partSelect = bulid_form('select', $data, 'part_id',$curr);
        $this->assign('partSelect',$partSelect);
    }
    
    
}
