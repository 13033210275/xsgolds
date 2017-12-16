<?php

namespace app\admin\controller;

use app\admin\model\Apply;
use app\admin\model\MemberRec;
use app\admin\model\Merchant;
use app\admin\model\Member;
use app\admin\model\SorCode;

class Order extends Admin {




    public function index() {

        $loan_status = input('loan_status/d');
        $field = input('field/s');

        $rec_mobile = input('rec_mobile');
        $rec_name = input('rec_name');
        $loan_id = input('loan_id/d',0);
        $mert_name = input('mert_name/s');
        $loan_uid = input('loan_uid/d',0);
        $loan_uname = input('loan_uname');
        $loan_phone = input('loan_phone');
        $recom = input('recom/s');
        $sdate = input('sdate');
        $edate = input('edate');
        $code = input('code/d',0);//来源
        $map = [];
        if($code!=0){
            $map['sor_code'] = $code;
        }
        if($rec_mobile){
            $row1 = Member::where('mobile',$rec_mobile)->field('id')->find();
            $row = MemberRec::where('rec_id',$row1->id)->select();
            $row2=[];
            foreach($row as $vo){
                $row2[]=$vo->user_id;
            }

            $map['user_id'] = ['in',$row2];
        }
        if($rec_name){
            $row1 = Member::where('truename',$rec_name)->field('id')->find();
            $row = MemberRec::where('rec_id',$row1->id)->select();
            $row2=[];
            foreach($row as $vo){
                $row2[]=$vo->user_id;
            }

            $map['user_id'] = ['in',$row2];
        }
        if($loan_id){
            $map['id'] = $loan_id;
        }
        if($mert_name){
            $row = Merchant::where('name',$mert_name)->field('id')->find();
            if($row){
                $map['mert_id'] = $row->id;
            }
        }
        if($loan_uid){
            $map['user_id'] = $loan_uid;
        }
        if($loan_uname){
            $row = Member::where('nickname',$loan_uname)->field('id')->find();
            if($row){
                $map['user_id'] = $row->id;
            }
        }
        if($loan_phone){
            $row = Member::where('mobile',$loan_phone)->field('id')->find();
            if($row){
                $map['user_id'] = $row->id;
            }
        }

        if($sdate && $edate){
            $stime = strtotime($sdate." 00:00:00");
            $etime = strtotime($edate." 23:59:59");
            $map['create_time'] = ['between',[$stime,$etime]];
        }elseif($sdate){
            $stime = strtotime($sdate." 00:00:00");
            $etime = strtotime($sdate." 23:59:59");
            $map['create_time'] = ['between',[$stime,$etime]];
        }elseif($edate){
            $stime = strtotime($edate." 00:00:00");
            $etime = strtotime($edate." 23:59:59");
            $map['create_time'] = ['between',[$stime,$etime]];
        }
        $list = Apply::order('id', 'desc')->where($map)->paginate(100,false,[
            'query' => request()->param()
        ]);
        $num=Apply::order('id', 'desc')->where($map)->count('id');
        $page = $list->render();
        $this->assign('_list',$list);
        $this->assign('_num',$num);
        $this->assign('_page', $page);

        foreach ($list as $vo){
            $vo->rec_uname = '';
            $vo->code_name ='';
            $row = MemberRec::where('user_id',$vo->user_id)->find();
            if($row) {
                if($row->user->truename){
                    $vo->rec_uname = $row->user->truename."(".$row->user->mobile.")";
                }
            }
            $rows = SorCode::where('sor_code',$vo->sor_code)->find();
            if($rows) {
                $vo->code_name = $rows->code_name;
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
        $fieldSelect = bulid_form('select', [
            'id'=>'申请ID',
            'mert_name'=>'机构名称',
            'user_id'=>'贷款人ID',
            'moblie'=>'贷款人手机号',
        ] , 'field',$field);
        $this->assign('fieldSelect',$fieldSelect);


        $typeSelect = bulid_form('select', [
            0=>'全部',
            1=>'待审核',
            2=>'已申请',
            3=>'出额度',
            4=>'已放款',
            5=>'被拒绝',
            6=>'其他',
        ] , 'loan_status',$loan_status);
        $this->assign('typeSelect',$typeSelect);

        $this->assign('page', input('page',1));
        $this->assign('meta_title', '订单列表');
        $url = url('export',[
            'rec_mobile'=>$rec_mobile,
            'rec_name'=>$rec_name,
            'code'=>$code,
            'loan_id'=>$loan_id,
            'mert_name'=>$mert_name,
            'loan_uid'=>$loan_uid,
            'sdate'=>$sdate,
            'edate'=>$edate,
            'loan_uname'=>$loan_uname,
            'loan_phone'=>$loan_phone,
        ]);
        $this->assign('exportUrl',$url);
        return $this->fetch();
    }

//导出
    public function export(){
        $rec_mobile=input('rec_mobile');
        $rec_name = input('rec_name');
        $loan_id = input('loan_id/d',0);
        $mert_name = input('mert_name/s');
        $loan_uid = input('loan_uid/d',0);
        $loan_uname = input('loan_uname');
        $loan_phone = input('loan_phone');
        $sdate = input('sdate');
        $edate = input('edate');
        $code = input('code/d',0);//来源
        $map = [];
        if($rec_mobile){
            $row1 = Member::where('mobile',$rec_mobile)->field('id')->find();
            $row = MemberRec::where('rec_id',$row1->id)->select();
            $row2=[];
            foreach($row as $vo){
                $row2[]=$vo->user_id;
            }

            $map['user_id'] = ['in',$row2];
        }
        if($rec_name){
            $row1 = Member::where('truename',$rec_name)->field('id')->find();
            $row = MemberRec::where('rec_id',$row1->id)->select();
            $row2=[];
            foreach($row as $vo){
                $row2[]=$vo->user_id;
            }

            $map['user_id'] = ['in',$row2];
        }
        if($code!=0){
            $map['sor_code'] = $code;
        }
        if($loan_id){
            $map['id'] = $loan_id;
        }
        if($mert_name){
            $row = Merchant::where('name',$mert_name)->field('id')->find();
            if($row){
                $map['mert_id'] = $row->id;
            }
        }
        if($loan_uid){
            $map['user_id'] = $loan_uid;
        }
        if($loan_uname){
            $row = Member::where('nickname',$loan_uname)->field('id')->find();
            if($row){
                $map['user_id'] = $row->id;
            }
        }
        if($loan_phone){
            $row = Member::where('mobile',$loan_phone)->field('id')->find();
            if($row){
                $map['user_id'] = $row->id;
            }
        }

        if($sdate && $edate){
            $stime = strtotime($sdate." 00:00:00");
            $etime = strtotime($edate." 23:59:59");
            $map['create_time'] = ['between',[$stime,$etime]];
        }elseif($sdate){
            $stime = strtotime($sdate." 00:00:00");
            $etime = strtotime($sdate." 23:59:59");
            $map['create_time'] = ['between',[$stime,$etime]];
        }elseif($edate){
            $stime = strtotime($edate." 00:00:00");
            $etime = strtotime($edate." 23:59:59");
            $map['create_time'] = ['between',[$stime,$etime]];
        }
        $list = Apply::order('id', 'desc')->where($map)->select();
        if(!$list){
            $this->error('没有数据可导出');
        }
        $arr = [
            1=>'已申请',
            2=>'已放款'
        ];
        foreach ($list as $vo){

            $vo->rec_uname = $vo->code_name=$vo->mert_name=$vo->nickname=$vo->truename=$vo->time=$vo->status=$vo->cashs=$vo->rec_mobile='';
            $rows = SorCode::where('sor_code',$vo->sor_code)->find();
            if($rows){
                $vo->code_name = $rows->code_name;
            }
            if($vo->cash!=''){
                $vo->cashs=$vo->cash."元";
            }
            if($vo->mert->name!=''){
                $vo->mert_name =$vo->mert->name;
            };
            if($vo->user->nickname!=''){
                $vo->nickname =$vo->user->nickname;
            };
            if($vo->user->truename!=''){
                $vo->truename =$vo->user->truename;
            };
            if($vo->day.$vo->unit!=''){
                $vo->time=$vo->day.$vo->unit;
            };
            if($vo->loan_status!=''){
                $vo->status =$arr[$vo->loan_status];
            };

            $vo->status =$arr[$vo->loan_status];
            $row = MemberRec::where('user_id',$vo->user_id)->find();
            if($row){
                if($row->user->nickname!='') {
                    $vo->rec_uname = $row->user->truename;
                    $vo->rec_mobile=$row->user->mobile;
                }
            }
        }

        $xlsName  = "Order";
        $xlsCell  = array(
            array('id','编号'),
            array('mert_name','商家名称'),
            array('nickname','借款人'),
            array('truename','姓名'),
            array('cashs','借款金额'),
            array('time','借款时长'),
            array('rec_uname','推荐人'),
            array('rec_mobile','推荐人手机号'),
            array('create_time','申请时间'),
            array('code_name','来源'),
            array('status','状态')
        );
        $this->exportExcel($xlsName,$xlsCell,$list);
    }




}
