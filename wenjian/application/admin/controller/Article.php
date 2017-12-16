<?php
 
namespace app\admin\controller;
use app\admin\model\Document;

class Article extends Admin {
 
  
    public function index() {
        $list = Document::order('rise', 'asc')->field('id,title,rise,create_time')->paginate(20);  
        $page = $list->render();   
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('meta_title', '口子资讯');
        return $this->fetch();
    }
 

    /**
     * 文档新增页面初始化
     * @author xsgolds
     */
    public function add() {
        if (request()->isPost()) {
            $result = $this->validate($_POST,[
                'title'=>'require',
            ],[
                'title.require'=>'请填写标题',
            ]);
            if(true !== $result){
               $this->error($result);
            } 
            $mode = new Document();   
            $flag = $mode->allowField(true)->save($_POST);  
            if($flag){
                $this->success('添加成功！', url('index'));
            }
            $this->error('添加失败');
        } else { 
            $this->assign('meta_title', '新增口子资讯');
            return $this->fetch();
        }
    }

    
    
     //ok 编辑
    public function edit() {
        $id = input('id', '');
        if (empty($id)) {
            $this->error('参数不能为空！');
        } 
        $mode = new Document();    
        if (request()->isPost()) {  
            $flag = $mode->isUpdate(true)->allowField(true)->save($_POST,['id'=>$id]);
            if($flag){
                $this->success('更新成功！', url('index'));
            }
            $this->error('更新失败');
        } else { 
            $row = $mode->find($id);
            if(empty($row)){
                $this->error('记录已经不存在');
            } 
            $this->assign('meta_title', '编辑口子资讯');
            $this->assign('row',$row);
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
        if($obj=='document'){
            $object = new Document;  
        }
        if($this->handle($object, $id, $cmd)){ 
            finish(0);
        } 
        finish(1,'操作失败');
    }
            
    
     

}
