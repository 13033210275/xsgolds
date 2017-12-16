<?php

namespace app\admin\controller;
use app\admin\model\Adv;
use app\admin\model\MerchantScore;
use app\admin\model\Merchant;
use app\admin\model\Comment;
use app\admin\model\Lunbo;
use app\admin\model\RecommendMerchant;
use app\admin\model\RecommendEnter;
use app\admin\model\Member;
use app\admin\model\RecommendSet;

class Web extends Admin {
    private $data = [
        0=>'全部',
        1=>'0~1分',
        2=>'1~2分',
        3=>'2~3分',
        4=>'3~4分',
        5=>'4~5分'
    ];

    public function index() {

        $wd = input('wd');
        $u_score = input('uscore/d');
        $s_score = input('sscore/d');
        $map = [];
        if (is_numeric($wd)) {
            $map['mert_id'] = $wd;
        } elseif($wd) {
            $row = Merchant::where('name',$wd)->field('id')->find();
            if($row){
                $map['mert_id'] = $row->id;
            }
        }
        if($u_score){
            $map['user_score'] = ['between',[$u_score-1,$u_score]];
        }
        if($s_score){
            $map['sys_score'] = ['between',[$s_score-1,$s_score]];
        }
        $list = MerchantScore::order('id', 'desc')->where($map)->paginate(10);
        $page = $list->render();
        $this->assign('list',$list);
        $this->assign('page', $page);
        $this->assign('meta_title', '机构评分');
        $uscoreSelect = bulid_form('select',  $this->data , 'uscore',$u_score);
        $this->assign('uscoreSelect',$uscoreSelect);

        $sscoreSelect = bulid_form('select', $this->data , 'sscore',$s_score);
        $this->assign('sscoreSelect',$sscoreSelect);
        return $this->fetch();
    }


    //ok 添加机构评分
    public function add() {
        if (request()->isPost()) {
            $result = $this->validate($_POST,[
                'mert_id'=>'require',
                'mert_name'=>'require',
            ],[
                'mert_id.require'=>'请选择商家',
                'mert_name.unique'=>'请选择商家'
            ]);
            if(true !== $result){
               $this->error($result);
            }
            $mode = new MerchantScore();
            $flag = $mode->allowField(true)->save($_POST);
            if($flag){
                $this->success('添加成功！', url('index'));
            }
            $this->error('添加失败');
        } else {
            $rs = Merchant::field('id,name')->select();
            $list = [
                0=>'--请选择商家--'
            ];
            if($rs){
                foreach ($rs as $vo) {
                     $list[$vo->id] = $vo->name;
                }
            }
            $mertSelect = bulid_form('select', $list, 'mert_id',0);
            $this->assign('mertSelect',$mertSelect);
            $statusRadio = bulid_form('radio', ['禁用','启用'], 'use_sys',0);
            $this->assign('statusRadio',$statusRadio);
            $this->assign('meta_title', '新增机构评分');
            return $this->fetch();
        }
    }


    //ok 编辑机构评分
    public function edit() {
        $id = input('id', '');
        if (empty($id)) {
            $this->error('参数不能为空！');
        }
        if (request()->isPost()) {
            $mode = new MerchantScore();
            $flag = $mode->isUpdate(true)->allowField(true)->save($_POST,['id'=>$id]);
            if($flag){
                $this->success('更新成功！', url('index'));
            }
            $this->error('更新失败');
        } else {
            $row = MerchantScore::find($id);
            if(empty($row)){
                $this->error('记录已经不存在');
            }



            $statusRadio = bulid_form('radio', ['禁用','启用'], 'use_sys',$row->use_sys);
            $this->assign('statusRadio',$statusRadio);
            $this->assign('meta_title', '编辑机构评分');
            $this->assign('row',$row);
            return $this->fetch();
        }
    }

    //ok 评论列表
    public function comment() {


        $wd = input('wd');
        $sdate = input('sdate/s','');
        $edate = input('edate/s','');
        $score = input('score/d',0);
        $status = input('status/d',0);
        $type_id = input('type/d',0);
        $map = [];
        if($wd){
           if (is_numeric($wd)) {
                $map['mert_id|user_id'] = $wd;
            } else {
                $map['mert_name|user_name'] = array('like', '%' . (string) $wd . '%');
            }
        }
        $type = getCommentType();
        if($score>0){
            $map['score'] = ['between',[$score-1,$score]];
        }
        if($status>0){
            $map['status'] = $status-1;
        }else{
            $map['status'] = ['>=',0];
        }
        if($type_id>0){
            $map['type'] = $type[$type_id];
        }

        if($sdate && $edate){
            $map['create_time'] = ['between',  [strtotime($sdate),strtotime($edate)]];
        }elseif($sdate){
            $stime = strtotime($sdate.' 00:00:00');
            $etime = strtotime($sdate.' 23:59:59');
            $map['create_time'] = ['between',[$stime,$etime]];
        }elseif($edate){
            $stime = strtotime($edate.' 00:00:00');
            $etime = strtotime($edate.' 23:59:59');
            $map['create_time'] = ['between',[$stime,$etime]];
        }
        $list = Comment::order('id', 'desc')->where($map)->paginate(10);
        $page = $list->render();
        $this->assign('list',$list);
        $this->assign('page', $page);
        $this->assign('meta_title', '评论列表');
        $typeSelect = bulid_form('select', $type , 'type',$type);
        $scoreSelect =  bulid_form('select', $this->data, 'score',$score);

        $statusSelect = bulid_form('select', [
            0=>'全部',
            1=>'否',
            2=>'是',
        ], 'status',$status);
        $this->assign('statusSelect',$statusSelect);

        $this->assign('scoreSelect',$scoreSelect);
        $this->assign('typeSelect',$typeSelect);
        return $this->fetch();
    }

    //ok 未发布评论列表
    public function unpost() {
        $wd = input('wd');
        $sdate = input('sdate/s','');
        $edate = input('edate/s','');
        $score = input('score/d',0);
        $status = input('status/d',1);
        $type_id = input('type/d',0);
        $map = [];
        if($wd){
           if (is_numeric($wd)) {
                $map['mert_id|user_id'] = $wd;
            } else {
                $map['mert_name|user_name'] = array('like', '%' . (string) $wd . '%');
            }
        }
        $type = getCommentType();
        if($score>0){
            $map['score'] = ['between',[$score-1,$score]];
        }
        if($status>0){
            $map['status'] = $status-1;
        }else{
            $map['status'] = ['>=',0];
        }
        if($type_id>0){
            $map['type'] = $type[$type_id];
        }

        if($sdate && $edate){
            $map['create_time'] = ['between',  [strtotime($sdate),strtotime($edate)]];
        }elseif($sdate){
            $stime = strtotime($sdate.' 00:00:00');
            $etime = strtotime($sdate.' 23:59:59');
            $map['create_time'] = ['between',[$stime,$etime]];
        }elseif($edate){
            $stime = strtotime($edate.' 00:00:00');
            $etime = strtotime($edate.' 23:59:59');
            $map['create_time'] = ['between',[$stime,$etime]];
        }
        $list = Comment::order('id', 'desc')->where($map)->paginate(10);
        $page = $list->render();
        $this->assign('list',$list);
        $this->assign('page', $page);
        $this->assign('meta_title', '未发布评论列表');
        $typeSelect = bulid_form('select', $type , 'type',$type);
        $scoreSelect =  bulid_form('select', $this->data, 'score',$score);

        $statusSelect = bulid_form('select', [
            0=>'全部',
            1=>'否',
            2=>'是',
        ], 'status',$status);
        $this->assign('statusSelect',$statusSelect);

        $this->assign('scoreSelect',$scoreSelect);
        $this->assign('typeSelect',$typeSelect);
        return $this->fetch();
    }

    //ok 回收站
    public function recycle() {
        $wd = input('wd');
        $sdate = input('sdate/s','');
        $edate = input('edate/s','');
        $score = input('score/d',0);
        $type_id = input('type/d',0);
        $map = [];
        if($wd){
           if (is_numeric($wd)) {
                $map['mert_id|user_id'] = $wd;
            } else {
                $map['mert_name|user_name'] = array('like', '%' . (string) $wd . '%');
            }
        }
        $type = getCommentType();
        if($score>0){
            $map['score'] = ['between',[$score-1,$score]];
        }
        $map['status'] = -1;
        if($type_id>0){
            $map['type'] = $type[$type_id];
        }

        if($sdate && $edate){
            $map['create_time'] = ['between',  [strtotime($sdate),strtotime($edate)]];
        }elseif($sdate){
            $stime = strtotime($sdate.' 00:00:00');
            $etime = strtotime($sdate.' 23:59:59');
            $map['create_time'] = ['between',[$stime,$etime]];
        }elseif($edate){
            $stime = strtotime($edate.' 00:00:00');
            $etime = strtotime($edate.' 23:59:59');
            $map['create_time'] = ['between',[$stime,$etime]];
        }
        $list = Comment::order('id', 'desc')->where($map)->paginate(10);
        $page = $list->render();
        $this->assign('list',$list);
        $this->assign('page', $page);
        $this->assign('meta_title', '评论回收站');
        $typeSelect = bulid_form('select', $type , 'type',$type);
        $scoreSelect =  bulid_form('select', $this->data, 'score',$score);

        $statusSelect = bulid_form('select', [
            0=>'全部',
            1=>'否',
            2=>'是',
        ], 'status',0);
        $this->assign('statusSelect',$statusSelect);

        $this->assign('scoreSelect',$scoreSelect);
        $this->assign('typeSelect',$typeSelect);
        return $this->fetch();
    }

    //ok 敏感词管理
    public function badword() {
        if (request()->isPost()) {
            $contents = input('contents');
            $flag = file_put_contents(BADWORD_FILE, $contents);
            if($flag){
                $this->success('更新成功！');
            }
            $this->error('更新失败');
        } else {
            $contents = file_get_contents(BADWORD_FILE);
            $this->assign('contents',$contents);
            $this->assign('meta_title', '评论自动过滤配置');
            return $this->fetch();
        }
    }

    //ok
    public function adv($adv_type = 0) {
        $list = Adv::where('adv_type='.$adv_type)->order('id', 'desc')->paginate(10);
        $page = $list->render();
        $this->assign('list',$list);
        $this->assign('page', $page);
        $this->assign('adv_type', $adv_type);
        $this->assign('meta_title', '广告管理');
        return $this->fetch();
    }

    //ok
    public function advAdd() {
        if (request()->isPost()) {
            $result = $this->validate($_POST,[
                'title'=>'require',
                'flag'=>'require',
            ],[
                'title.require'=>'请填写广告名称',
                'flag.unique'=>'请填写广告位标示'
            ]);
            if(true !== $result){
               $this->error($result);
            }
            $mode = new Adv();
            $flag = $mode->allowField(true)->save($_POST);
            if($flag){
                $this->success('添加成功！', url('adv'));
            }
            $this->error('添加失败');
        } else {
            $typeSelect = bulid_form('select', [
                1=>'url链接',
                2=>'文章ID'
            ], 'type', 1);

            $statusRadio = bulid_form('radio', ['禁用','启用'], 'status',1);
            $this->assign('statusRadio',$statusRadio);

            $adv_typeRadio = bulid_form('radio', ['H5首页','推荐页'], 'adv_type',0);
            $this->assign('adv_typeRadio',$adv_typeRadio);

            $this->assign('typeSelect',$typeSelect);
            $this->assign('meta_title', '新增广告');
            return $this->fetch();
        }
    }


    /**
     * 状态修改
     */
    public function changeStatus($method = null) {
        $data = input('id/a');
        $id = array_unique($data);
        $id = is_array($id) ? implode(',', $id) : $id;
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }
        $map['id'] = array('in', $id);
        switch (strtolower($method)) {
            case 'forbidrecommend':
                // $this->forbid('recommend_set', $map);
                $this->forbid('recommend_set', $map);
                break;
            case 'resumerecommend':
                // $this->resume('recommend_set', $map);
                $this->resume('recommend_set', $map);
                break;
            case 'deleterecommend':
                // $this->_delete('recommend_set', $map);
                $this->_delete('recommend_set', $map);
                break;
            case 'forbidadv':
                $this->forbid('adv', $map);
                break;
            case 'resumeadv':
                $this->resume('adv', $map);
                break;
            case 'deleteadv':
                $this->_delete('adv', $map);
                break;
            case 'deletecomment':
                $this->handeComment('deletecomment', $map);
                break;
            case 'delcomment':
                $this->_delete('comment', $map);
                break;
            case 'postcomment':
                $this->handeComment('postcomment', $map);
                break;
            case 'forbidlunbo':
                $this->forbid('lunbo', $map);
                break;
            case 'resumelunbo':
                $this->resume('lunbo', $map);
                break;
            case 'deletelunbo':
                $this->_delete('lunbo', $map);
                break;
            case 'forbidmerchant':
                $this->forbid('recommend_merchant', $map);
                break;
            case 'resumemerchant':
                $this->resume('recommend_merchant', $map);
                break;
            case 'deletemerchant':
                $this->_delete('recommend_merchant', $map);
                break;
            default:
                $this->error('参数非法');
        }
    }

    //未发布评论状态
    public function changeStatusPostComment() {
        $data = input('id/a');
        $id = array_unique($data);
        $id = is_array($id) ? implode(',', $id) : $id;
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }
        $map['id'] = array('in', $id);
        $this->handeComment('postcomment', $map);

    }

    public function changeStatusDeleteComment() {
        $data = input('id/a');
        $id = array_unique($data);
        $id = is_array($id) ? implode(',', $id) : $id;
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }
        $map['id'] = array('in', $id);
        $this->handeComment('deletecomment', $map);

    }

    private function changeRecommendenter($id, $status)
    {
        $recommend = new RecommendEnter();
        $recommend->checkCheckStatus($id, $status);
        $this->success('操作成功');
    }
    private function deleteRecommendenter($id)
    {
        $recommend = new RecommendEnter();
        $recommend->deleteById($id);
        $this->success('操作成功');
    }
    private function handeComment($type,$map){
        $o = new Comment;
        $list = $o->field('id,mert_id')->where($map)->select();
        if(!$list){
            $this->error('评论已经不存在');
        }
        foreach ($list as $vo) {
            $mert_id= $vo->mert_id;
            if($type=='postcomment'){//发布
                $f1 = $o->where('id',$vo->id)->update([
                    'status'=>1
                ]);
            }elseif($type=='deletecomment'){// 删除
                $f1 = $o->where('id',$vo->id)->update([
                    'status'=>-1
                ]);
            }
            $b = new Merchant();
            $c = new MerchantScore();
            $score = $o->getAvarageScore($mert_id);

            if($type=='postcomment'){
                $f2 = $b->where('id',$mert_id)->setInc('comment_num', 1);
            }elseif($type=='deletecomment'){
                $f2 = $b->where('id',$mert_id)->setDec('comment_num', 1);
            }
            $f3  = $c->updateMertScore($mert_id, $score);


        }
        $this->success('操作成功');
    }

    public function advEdit() {
        $id = input('id', '');
        if (empty($id)) {
            $this->error('参数不能为空！');
        }
        if (request()->isPost()) {
            $result = $this->validate($_POST,[
                'title'=>'require',
                'flag'=>'require',
            ],[
                'title.require'=>'请填写广告名称',
                'flag.unique'=>'请填写广告位标示'
            ]);
            if(true !== $result){
               $this->error($result);
            }
            $mode = new Adv();
            $flag = $mode->isUpdate(true)->allowField(true)->save($_POST,['id'=>$id]);
            if($flag){
                $this->success('更新成功！', url('adv'));
            }
            $this->error('更新失败');
        } else {
            $row = Adv::where('id',$id)->find();
            if(empty($row)){
                $this->error('广告已经不存在');
            }
            $typeSelect = bulid_form('select', [
                1=>'url链接',
                2=>'文章ID'
            ], 'type',$row->type);

            $statusRadio = bulid_form('radio', ['禁用','启用'], 'status',$row->status);
            $this->assign('statusRadio',$statusRadio);

            $adv_typeRadio = bulid_form('radio', ['H5首页','推荐页'], 'adv_type',0);
            $this->assign('adv_typeRadio',$adv_typeRadio);

            $this->assign('typeSelect',$typeSelect);
            $this->assign('meta_title', '编辑广告');
            $this->assign('row',$row);
            return $this->fetch();
        }
    }


    //ok
    public function lunbo() {
        $list = Lunbo::order('rise', 'asc')->select();
        $this->assign('list',$list);
        $this->assign('meta_title', '权重管理列表');
        return $this->fetch();
    }


     //ok
    public function lbAdd() {
        if (request()->isPost()) {
            $result = $this->validate($_POST,[
                'merchant_id'=>'require',
            ],[
                'merchant_id.require'=>'请选择机构',
            ]);
            if(true !== $result){
               $this->error($result);
            }


            $mode = new Lunbo();
            $flag = $mode->allowField(true)->save($_POST);
            if($flag){
                $this->success('添加成功！', url('lunbo'));
            }
            $this->error('添加失败');
        } else {
            $mert=['--选择机构--'];
            $res = Merchant::field('id,name')->select();
            if($res){
                foreach ($res as $vo) {
                    $mert[$vo->id] = $vo->name;
                }
            }
            $mertSelect = bulid_form('select', $mert, 'merchant_id', 0);
            $this->assign('mertSelect',$mertSelect);
            $statusRadio = bulid_form('radio', ['禁用','启用'], 'status',1);
            $this->assign('statusRadio',$statusRadio);
            $this->assign('meta_title', '新增轮播权重');
            return $this->fetch();
        }
    }

    public function lbEdit() {
        $id = input('id', '');
        if (empty($id)) {
            $this->error('参数不能为空！');
        }
        if (request()->isPost()) {
            $result = $this->validate($_POST,[
                'merchant_id'=>'require',
            ],[
                'merchant_id.require'=>'请选择机构',
            ]);
            if(true !== $result){
               $this->error($result);
            }
            $mode = new Lunbo();
            $flag = $mode->isUpdate(true)->allowField(true)->save($_POST,['id'=>$id]);
            if($flag){
                $this->success('更新成功！', url('lunbo'));
            }
            $this->error('更新失败');
        } else {
            $row = Lunbo::where('id',$id)->find();
            if(empty($row)){
                $this->error('记录已经不存在');
            }
             $mert=['--选择机构--'];
            $res = Merchant::field('id,name')->select();
            if($res){
                foreach ($res as $vo) {
                    $mert[$vo->id] = $vo->name;
                }
            }
            $mertSelect = bulid_form('select', $mert, 'merchant_id',$row->merchant_id);
            $this->assign('mertSelect',$mertSelect);

            $statusRadio = bulid_form('radio', ['禁用','启用'], 'status',$row->status);
            $this->assign('statusRadio',$statusRadio);

            $this->assign('meta_title', '编辑权重');
            $this->assign('row',$row);
            return $this->fetch();
        }
    }

    //ok
    public function merchant() {
        $list = RecommendMerchant::order('rise', 'asc')->select();
        foreach ($list as $vo) {
            $merchant = Merchant::where('id='.$vo->id)->select();
            if (!empty($merchant)){
                $vo->name = $merchant[0]->name;
            }else{
                RecommendMerchant::where('id='.$vo->id)->delete();
            }
        }
        $this->assign('list',$list);
        $this->assign('meta_title', '机构配置');
        return $this->fetch();
    }

    //ok
    public function merchantAdd() {
        if (request()->isPost()) {
            // $result = $this->validate($_POST,[
            //     'id'=>'require'
            // ],[
            //     'id.require'=>'请输入机构编号'
            // ]);
            // if(true !== $result){
            //     $this->error($result);
            // }

            // $merchant = RecommendMerchant::where('id='.$_POST['id'])->select();
            // if (!empty($merchant)) {
            //     $this->error('已经存在');
            // }
            $mode = new RecommendMerchant();
            $flag = $mode->allowField(true)->save($_POST);
            if($flag){
                $this->success('添加成功！', url('merchant'));
            }
            $this->error('添加失败');
        } else {
            $statusRadio = bulid_form('radio', ['禁用','启用'], 'status',1);
            $this->assign('statusRadio',$statusRadio);

            $this->assign('meta_title', '新增机构');
            return $this->fetch();
        }
    }

    public function checkMerchantId() {
        $id = input('mid', '');
        $RecommendMerchant = RecommendMerchant::where('id='.$id)->select();
        if (!empty($RecommendMerchant)) {
            return '{code:1, error:"已经存在"}';
        }
        $merchant = Merchant::where('id='.$id)->select();
        if (empty($merchant)) {
            return '{code:1, error:"机构不存在"}';
        }

        return  "{code:0, id:'".$merchant[0]->id."',name:'".$merchant[0]->name."'}";
    }

    public function merchantEdit() {
        $id = input('id', '');
        if (empty($id)) {
            $this->error('参数不能为空！');
        }
        if (request()->isPost()) {
            $mode = new RecommendMerchant();
            $flag = $mode->isUpdate(true)->allowField(true)->save($_POST,['id'=>$id]);
            if($flag){
                $this->success('更新成功！', url('merchant'));
            }
            $this->error('更新失败');
        } else {
            $row = RecommendMerchant::where('id',$id)->find();
            if(empty($row)){
                $this->error('机构已经不存在');
            }
            $merchant = Merchant::where('id='.$id)->select();
            if(empty($merchant)){
                RecommendMerchant::where('id='.$vo->id)->delete();
                $this->error('机构已经不存在');
            }
            $row->name = $merchant[0]->name;

            $statusRadio = bulid_form('radio', ['禁用','启用'], 'status',$row->status);
            $this->assign('statusRadio',$statusRadio);

            $this->assign('meta_title', '编辑机构');
            $this->assign('row',$row);
            return $this->fetch();
        }
    }
    public function recommend_enter()
    {
        $uid = 1;
        $recommend_enter = new RecommendEnter();
        if (request()->isPost()) {
            $leftName = $_POST['leftName'];
            $leftUrl = $_POST['leftUrl'];
            $rightName = $_POST['rightName'];
            $rightUrl = $_POST['rightUrl'];
            $code = $recommend_enter->updateDatas($uid, $leftName, $leftUrl, $rightName, $rightUrl);
            if ($code)
            {
                $datas = [];
                $datas['leftName']=$leftName;
                $datas['leftUrl']=$leftUrl;
                $datas['rightName']=$rightName;
                $datas['rightUrl']=$rightUrl;
                $this->assign('row', $datas);
            }
            else
            {
                $this->error('更新失败请检查是不是删除超级管理员配置的数据');
                $datas = $recommend_enter->getDatas($uid);
                $this->assign('row', $datas);
            }
        }
        else
        {
            $datas = $recommend_enter->getDatas($uid);
            $this->assign('row', $datas);
        }
        $this->assign('meta_title', '入口配置');
        return $this->fetch();
    }
    private function _post_credit_link($user_id, $link, $status)
    {
        $row = [];
        $row['userId'] = $user_id;
        $row['link'] = $link;
        $statusRadio = bulid_form('radio', ['禁用','启用'], 'status',$status);
        $this->assign('statusRadio',$statusRadio);
        $this->assign("row", $row);
        $this->assign('meta_title', '信用卡链接配置');
        return $this->fetch();
    }
    public function credit_link(){
        $status = 1;
        if (request()->isPost()) {
            $link = $_POST['link'];
            $user_id = $_POST['userId'];
            $status = $_POST['status'];
            if (!$user_id){
                return $this->_post_credit_link($user_id, $link, $status);
            }
            if ($link) {
                $list = Member::where('id',$user_id)->field('status')->find();
                if (!$list) {
                    // echo "<script type='text/javascript'>alert('您添加的用户不存在。');</script>";
                    // return $this->_post_credit_link($user_id, $link, $status);
                    return $this->error("您添加的用户不存在。");
                }
                $recommend_set = new RecommendSet();
                $datas = $recommend_set->getDatasNoStatus($user_id);
                if ($datas) {
                    $recommend_set->updateDatas($user_id, $link, $status);
                }else{
                    $datas = $recommend_set->getDatasNoStatus();
                    $recommend_set->insertData($user_id, $link, $status);
                }
                // return $this->_post_credit_link($user_id, $link, $status);
                $this->success('操作成功', url('recommend_set'));
            }else{
                $recommend_set = new RecommendSet();
                $datas = $recommend_set->getDatasNoStatus($user_id);
                if ($datas) {
                    $status = $datas["status"];
                    $link = $datas["leftUrl"];
                }
                return $this->_post_credit_link($user_id, $link, $status);
            }
        }else{
            $user_id = input('id', "");
            if(!$user_id)
            {
                return $this->_post_credit_link("", "", 1);
            }
            $recommend_set = new RecommendSet();
            $datas = $recommend_set->getDatasNoStatus($user_id);
            return $this->_post_credit_link($user_id, $datas["leftUrl"], $status);
        }
        return $this->_post_credit_link($user_id, $link, $status);
    }
    public function check_credit_link(){
        if (request()->isPost()) {
            $user_id = $_POST['userId'];
            $status = $_POST['status'];
            if ($user_id)
            {
                $recommend_set = new RecommendSet();
                $datas = $recommend_set->getDatasNoStatus($user_id);
                $list = [];
                if ($datas) {
                    $list['link'] = $datas['leftUrl'];
                    $list['status'] = $datas['status'];
                }
                else
                {
                    $list['status'] = $status;
                }
                finish(0,'',$list);
            }
            else
            {
                $list = [];
                $list['status'] = $status;
                finish(0,'',$list);
            }
        }
    }
    public function recommend_set()
    {
        $recommend_set = new RecommendSet();
        $list = $recommend_set->getAllData();
        $this->assign('list',$list);
        $this->assign('meta_title', '信用卡链接配置列表');
        return $this->fetch();
    }
}
