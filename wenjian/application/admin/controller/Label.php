<?php

namespace app\admin\controller;

use app\admin\model;

class Label extends Admin {

    
    private $data = [
        1=>'标签',
        2=>'材料',
        3=>'借款用途'
    ];


    //ok
    public function index() {
        $list = model\Label::order('id', 'desc')->paginate(20);
        $page = $list->render(); 
        $this->assign('_list',$list);
        $this->assign('_page',$page);
        $this->assign('meta_title', '属性管理');
        return $this->fetch();
    }

    
    public function del(){
        $id = input('id/d');
        $cmd = input('cmd/s'); 
        $obj = input('obj/s'); 
        if (empty($id) || empty($cmd) || empty($obj)) {
            finish(1,'参数错误'); 
        }
        if($obj=='label'){
            $object = new model\Label;  
        }
        if($this->handle($object, $id, $cmd)){ 
            finish(0);
        } 
        finish(1,'操作失败');
    }

    

    //ok
    public function add() {
        if (request()->isPost()) {
            $result = $this->validate($_POST, [
                'name' => 'require'
                    ], [
                'name.require' => '请填写名称'
            ]);
            if (true !== $result) {
                finish(1,$result);
            }
            $mode = new model\Label();
            $flag = $mode->allowField(true)->save($_POST);
            if ($flag) {
                finish(0,'',url('index'));
            }
            finish(1,'添加失败');
        } else {
            
            $typeRadio = bulid_form('radio', $this->data, 'type',1);
            $this->assign('typeRadio',$typeRadio);
            
            $this->assign('meta_title', '新增属性');
            return $this->fetch();
        }
    }

    //ok
    public function edit() {
        $id = input('id', '');
        if (request()->isPost()) {
            if(!$id){
                finish(1,'参数错误');
            }
            $result = $this->validate($_POST, [
                'name' => 'require',
                    ], [
                'name.require' => '请填名称',
            ]);
            if (true !== $result) {
                finish(1,$result); 
            }
            $mode = new model\Label();
            $flag = $mode->isUpdate(true)->allowField(true)->save($_POST, ['id' => $id]);
            if ($flag) {
                finish(0,'',url('index'));
            }
            finish(1,'更新失败');
        } else {
            if (empty($id)) {
                $this->error('参数不能为空！');
            }
            $row = model\Label::where('id', $id)->find();
            if (empty($row)) {
                $this->error('记录已经不存在');
            }
            
            $typeRadio = bulid_form('radio', $this->data, 'type',$row->type);
            $this->assign('typeRadio',$typeRadio);
            
            $this->assign('meta_title', '编辑属性');
            $this->assign('row', $row);
            return $this->fetch();
        }
    }

}
