<?php

namespace app\admin\controller;
use app\admin\model\Member as User;
use app\admin\model\Partment;
use app\admin\model\SorCode;

/**
 * 后台用户控制器
 */
class Member extends Admin {


    private $search_type =  [
        'id'=>'会员ID',
        'nickname'=>'会员名称',
        'mobile'=>'手机号',
        'rec_truename'=>'推荐人',
        'rec_mobile'=>'推荐人手机号'
    ];


    /**
     * 用户管理首页(OK)
     */
    public function index() {
        $wd = input('wd');
        $sdate = input('sdate/s','');
        $edate = input('edate/s','');
        $map=[];
        $type = input('type/s','id');
        $map['is_reg'] = 1;
        $map['golds_black']=0;
        $map['wd_black']=0;
        $code = input('code/d',0);//注册来源
        if($wd){
            if($type=='id'){
                $map[$type] = intval($wd);

            }else if($type=='rec_truename'){
                $rec_truename=array('like', '%' . (string) $wd . '%');

                $rowe=User::field('nickname')->where('truename',$rec_truename)->find();
                $map['rec_uname'] = $rowe->nickname;
            }else if($type=='rec_mobile'){
                $rec_mobile=array('like', '%' . (string) $wd . '%');

                $rowe=User::field('nickname')->where('mobile',$rec_mobile)->find();
                $map['rec_uname'] = $rowe->nickname;

            }
            else{
                $map[$type] = array('like', '%' . (string) $wd . '%');
            }
        }
        if($sdate && $edate){
            $stime = strtotime($sdate." 00:00:00");
            $etime = strtotime($edate." 23:59:59");
            $map['reg_time'] = ['between',[$stime,$etime]];
        }elseif($sdate){
            $stime = strtotime($sdate." 00:00:00");
            $etime = strtotime($sdate." 23:59:59");
            $map['reg_time'] = ['between',[$stime,$etime]];
        }elseif($edate){
            $stime = strtotime($edate." 00:00:00");
            $etime = strtotime($edate." 23:59:59");
            $map['reg_time'] = ['between',[$stime,$etime]];
        }

        if($code!=0){
            $map['sor_code'] = $code;
        }

        $list = User::where($map)->order('id desc')->paginate(20,false,[
            'query' => request()->param()
        ]);
        $num=User::order('id', 'desc')->where($map)->count('id');
        $this->assign('_num',$num);
        foreach($list as $wo){
            $wo->code_name='';
            $row=SorCode::field('code_name')->where('sor_code',$wo->sor_code)->find();
            $wo->code_name=$row->code_name;
            $wo->true_rec_name='';
            $row=User::field('truename,mobile')->where('nickname',$wo->rec_uname)->find();
            if($row->truename){
                $wo->true_rec_name=$row->truename."(".$row->mobile.")";
            }
        }
        $codeSelect = bulid_form('select', [
            0=>'全部',
            '2111000001'=>'Android微信',
            '2119000001'=>'Android浏览器',
            '3110000001'=>'Android端APP',
            '2211000001'=>'IOS微信',
            '2219000001'=>'IOS浏览器',
            '3210000001'=>'IOS端APP',
            '2111000002'=>'Android小树时代微信',
            '2119000002'=>'Android小树时代浏览器',
            '3110000002'=>'Android小树时代APP',
            '2211000002'=>'IOS小树时代微信',
            '2219000002'=>'IOS小树时代浏览器',
            '3210000002'=>'IOS小树时代APP',
        ], 'code',$code);
        $this->assign('codeSelect',$codeSelect);
        $page = $list->render();
        $this->assign('_list',$list);
        $this->assign('_page',$page);
        $this->assign('meta_title', '普通会员');
        $typeSelect = bulid_form('select',  $this->search_type, 'type',$type);
        $this->assign('typeSelect',$typeSelect);
        $this->assign('sdate',$sdate);
        $this->assign('edate',$edate);

        $url = url('export',[
            'wd'=>$wd,
            'type'=>$type,
            'sdate'=>$sdate,
            'edate'=>$edate,
        ]);

        $this->assign('exportUrl',$url);
        return $this->fetch();
    }

    //导出
    public function export(){
        $map = [];
        $wd = input('wd');
        $type = input('type/s','id');
        $xsblack = input('xsblack',0);
        $wdblack = input('wdblack',0);
        $sdate = input('sdate/s','');
        $edate = input('edate/s','');
        $map['is_reg'] = 1;
        $map['golds_black']=$xsblack;
        $map['wd_black']=$wdblack;
        $code = input('code/d',0);//注册来源
        if($code!=0){
            $map['sor_code'] = $code;
        }
        if($wd){
            if($type=='id'){
                $map[$type] = intval($wd);

            }else if($type=='rec_truename'){
                $rec_truename=array('like', '%' . (string) $wd . '%');

                $rowe=User::field('nickname')->where('truename',$rec_truename)->find();
                $map['rec_uname'] = $rowe->nickname;
            }else if($type=='rec_mobile'){
                $rec_mobile=array('like', '%' . (string) $wd . '%');

                $rowe=User::field('nickname')->where('mobile',$rec_mobile)->find();
                $map['rec_uname'] = $rowe->nickname;

            }
            else{
                $map[$type] = array('like', '%' . (string) $wd . '%');
            }
        }
        if($sdate && $edate){
            $stime = strtotime($sdate." 00:00:00");
            $etime = strtotime($edate." 23:59:59");
            $map['reg_time'] = ['between',[$stime,$etime]];
        }elseif($sdate){
            $stime = strtotime($sdate." 00:00:00");
            $etime = strtotime($sdate." 23:59:59");
            $map['reg_time'] = ['between',[$stime,$etime]];
        }elseif($edate){
            $stime = strtotime($edate." 00:00:00");
            $etime = strtotime($edate." 23:59:59");
            $map['reg_time'] = ['between',[$stime,$etime]];
        }
        $list = User::where($map)->field('id,nickname,mobile,truename,reg_time,rec_uname,id_card,sor_code')->order('id desc')->select();
        foreach($list as $wo){
            $wo->true_rec_name=$wo->rec_mobile='';
            $row=User::field('truename,mobile')->where('nickname',$wo->rec_uname)->find();
            if($row->truename){
                $wo->true_rec_name=$row->truename;
                $wo->rec_mobile=$row->mobile;
            }
            $wo->code_name='';
            if($wo->id_card!=''){
                $wo->id_card="'".$wo->id_card;
            }
            $row=SorCode::field('code_name')->where('sor_code',$wo->sor_code)->find();
            $wo->code_name=$row->code_name;
        }
        if(!$list){
            $this->error('没有数据可导出');
        }
        $xlsName  = "User";
        $xlsCell  = array(
            array('id','用户ID'),
            array('nickname','会员名称'),
            array('mobile','手机号'),
            array('truename','真实姓名'),
            array('id_card','身份证'),
            array('reg_time','注册时间'),
            array('code_name','注册来源'),
            array('true_rec_name','推荐人'),
            array('rec_mobile','推荐人手机号')
        );
        $this->exportExcel($xlsName,$xlsCell,$list);
    }


    //编辑(ok)
    public function edit($id=0) {
        $row = User::get($id);
        if (request()->isPost()) {
            if(!$row){
                finish(1,'用户已经不存在');
            }
            $passwd = input('passwd/s');
            $exchange = input('exchange/d',0);
            $part_id = input('part_id/d',0);
            $isupdate = false;
            $field = ['nickname','truename','status'];
            foreach ($field as $vo) {
                if($_POST[$vo]!=$row->$vo){
                    $isupdate = true;
                    $row->$vo = $_POST[$vo];
                }
            }
            if($passwd){
                if(strlen($passwd)<6){
                    finish(1,'密码必须大于6位');
                }
                $row->passwd = $passwd;
                $isupdate = true;
            }
            if($exchange && !$part_id){
                finish(1,'请选择部门');
            }
            if($exchange && $part_id){
                $row->is_reg = 0;
                $row->part_id = $part_id;
                $isupdate = true;
            }

            if(!$isupdate){
                finish(1,'未修改任何数据');
            }
            $flag = $row->save();
            if($flag){
                finish(0,'',url('Member/index'));
            }
            finish(1,'更新失败');

        }else{
            if(!$row){
                $this->error('用户不存在');
            }
            $statusRadio = bulid_form('radio', ['否','是'], 'status', $row->status);
            $this->assign('statusRadio',$statusRadio);

            $midRadio = bulid_form('radio', ['否','是'], 'exchange');
            $this->assign('midRadio',$midRadio);

            $pmt = new Partment;
            $partSelect = $pmt->getPartmentSelect($row->part_id);
            $this->assign('partSelect',$partSelect);

            $this->assign('row',$row);
            $this->assign('meta_title', '编辑会员');
            return $this->fetch();
        }
    }



    //ok
    public function xsblack(){

        $wd = input('wd');
        $sdate = input('sdate/s','');
        $edate = input('edate/s','');
        $map=[];
        $type = input('type/s','id');
        $map['is_reg'] = 1;
        $map['golds_black']=1;

        if($wd){
            if($type=='id'){
                $map[$type] = intval($wd);

            }else{
                $map[$type] = array('like', '%' . (string) $wd . '%');
            }
        }
        if($sdate && $edate){
            $stime = strtotime($sdate." 00:00:00");
            $etime = strtotime($edate." 23:59:59");
            $map['reg_time'] = ['between',[$stime,$etime]];
        }elseif($sdate){
            $stime = strtotime($sdate." 00:00:00");
            $etime = strtotime($sdate." 23:59:59");
            $map['reg_time'] = ['between',[$stime,$etime]];
        }elseif($edate){
            $stime = strtotime($edate." 00:00:00");
            $etime = strtotime($edate." 23:59:59");
            $map['reg_time'] = ['between',[$stime,$etime]];
        }

        $list = User::where($map)->order('xs_black_time desc')->paginate(20);
        $page = $list->render();
        $num=User::order('xs_black_time', 'desc')->where($map)->count('id');
        $this->assign('_num',$num);
        $this->assign('_list',$list);
        $this->assign('_page',$page);

        $search_type =  [
            'id'=>'会员ID',
            'nickname'=>'会员名称',
            'mobile'=>'手机号',
        ];

        $typeSelect = bulid_form('select',  $search_type, 'type',$type);
        $this->assign('typeSelect',$typeSelect);


        $this->assign('meta_title', '小树黑名单');
        $url = url('export',[
            'wd'=>$wd,
            'type'=>$type,
            'xsblack'=>1,
            'sdate'=>$sdate,
            'edate'=>$edate,
        ]);

        $this->assign('exportUrl',$url);

        return $this->fetch();

    }

    //ok
    public function wdblack(){

        $wd = input('wd');
        $sdate = input('sdate/s','');
        $edate = input('edate/s','');
        $map=[];
        $type = input('type/s','id');
        $map['is_reg'] = 1;
        $map['wd_black']=1;

        if($wd){
            if($type=='id'){
                $map[$type] = intval($wd);

            }else{
                $map[$type] = array('like', '%' . (string) $wd . '%');
            }
        }
        if($sdate && $edate){
            $stime = strtotime($sdate." 00:00:00");
            $etime = strtotime($edate." 23:59:59");
            $map['reg_time'] = ['between',[$stime,$etime]];
        }elseif($sdate){
            $stime = strtotime($sdate." 00:00:00");
            $etime = strtotime($sdate." 23:59:59");
            $map['reg_time'] = ['between',[$stime,$etime]];
        }elseif($edate){
            $stime = strtotime($edate." 00:00:00");
            $etime = strtotime($edate." 23:59:59");
            $map['reg_time'] = ['between',[$stime,$etime]];
        }

        $list = User::where($map)->order('wd_black_time desc')->paginate(20);
        $page = $list->render();
        $num=User::order('wd_black_time', 'desc')->where($map)->count('id');
        $this->assign('_num',$num);
        $this->assign('_list',$list);
        $this->assign('_page',$page);

        $search_type =  [
            'id'=>'会员ID',
            'nickname'=>'会员名称',
            'mobile'=>'手机号',
        ];
        $typeSelect = bulid_form('select',  $search_type, 'type',$type);
        $this->assign('typeSelect',$typeSelect);

        $this->assign('meta_title', '网贷黑名单');

        $url = url('export',[
            'wd'=>$wd,
            'type'=>$type,
            'wdblack'=>1,
            'sdate'=>$sdate,
            'edate'=>$edate,
        ]);

        $this->assign('exportUrl',$url);
        return $this->fetch();
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
        if($obj=='user'){
            $object = new User;
        }
        if($this->handle($object, $id, $cmd)){
            finish(0);
        }
        finish(1,'操作失败');
    }



    /**
     * 获取用户注册错误信息
     * @param  integer $code 错误编码
     * @return string        错误信息
     */
    private function showRegError($code = 0) {
        switch ($code) {
            case -1: $error = '用户名长度必须在16个字符以内！';
                break;
            case -2: $error = '用户名被禁止注册！';
                break;
            case -3: $error = '用户名被占用！';
                break;
            case -4: $error = '密码长度必须在6-30个字符之间！';
                break;
            case -5: $error = '邮箱格式不正确！';
                break;
            case -6: $error = '邮箱长度必须在1-32个字符之间！';
                break;
            case -7: $error = '邮箱被禁止注册！';
                break;
            case -8: $error = '邮箱被占用！';
                break;
            case -9: $error = '手机格式不正确！';
                break;
            case -10: $error = '手机被禁止注册！';
                break;
            case -11: $error = '手机号被占用！';
                break;
            default: $error = '未知错误';
        }
        return $error;
    }



    //会员分销提成配置 ok
    public function distributionset(){
        if (request()->isPost()) {
            $firper = input('firper/d',0);
            $secper = input('secper/d',0);
            //firper -> rate secper-> sec_rate
            //更新给所有除职能部门和中介之外的所有人 即is_reg=1

            $list = User::field('id')->where('is_reg', 1)->select();
            if(!empty($list)){
                foreach($list as $vo){
                    User::where('id',$vo->id)->setField('rate', $firper);
                    User::where('id',$vo->id)->setField('sec_rate', $secper);
                }
            }
            finish(0,'',url('Member/index'));
        }else{
            $this->assign('meta_title', '会员分销提成配置');
            $list = User::field('id, rate, sec_rate')->where('is_reg', 1)->select();
            if(!empty($list)){
                $this->assign("data", $list[0]);
            }
            else
            {
                $list = [];
                $list["rate"] = 0;
                $list["sec_rate"] = 0;
                $this->assign("data", $list);
            }
            return $this->fetch();
        }
    }

}
