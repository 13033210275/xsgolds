<?php

namespace app\admin\controller;

use app\admin\model\TongjiTotal;
use app\admin\model\TjMert;
use app\admin\model\TongjiCash;
use app\admin\model\TongjiDate;
use app\admin\model\TongjiTag;
use app\admin\model\TongjiRec;
use app\admin\model\TongjiLoan;
use app\admin\model\Member;
use app\admin\model\Merchant;
use think\Db;

class Tongji extends Admin {



    //ok
    public function index() {


        $date = input('d',null);
        $sdate = input('sdate',  date('Y-m-d',strtotime('-8 days')));
        $edate = input('edate',date('Y-m-d'));

        if(!is_null($date)){
            if(is_numeric($date)){
                $sdate = date('Y-m-d',  strtotime('-'.$date.' days'));
            }elseif($date=='today'){
                $sdate = $edate;
            }elseif($date=='yestoday'){
                $sdate = $edate = date("Y-m-d",strtotime('-1 days'));
            }
        }

        $list = TongjiTotal::order('id', 'desc')->where('date','between',[$sdate,$edate])->select();
        $this->assign('_list',$list);


        $allView = $totalReg= $totalApply= $totalMertApply= $totalIp = 0;
        foreach ($list as $vo) {
            $allView+=$vo->view;
            $totalReg+=$vo->reg;
            $totalApply+=$vo->apply;
            $totalMertApply+=$vo->mert_apply;
            $totalIp+=$vo->ip;

        }
        $this->assign('allView',$allView);
        $this->assign('totalReg',$totalReg);
        $this->assign('totalApply',$totalApply);
        $this->assign('totalMertApply',$totalMertApply);
        $this->assign('totalIp',$totalIp);

        //---------
        $this->assign('d',$date);
        $this->assign('sdate',$sdate);
        $this->assign('edate',$edate);
        $this->assign('meta_title', '总统计');


        $exportUrl = url('exportTotal',[
            'sdate'=>$sdate,
            'edate'=>$edate

        ]);
        $this->assign('exportUrl',$exportUrl);


        return $this->fetch();
    }

    //导出 总统计
    public function exportTotal(){
        $sdate = input('sdate/s','');
        $edate = input('edate/s','');


            $list = TongjiTotal::order('id', 'desc')->where('date','between',[$sdate,$edate])->select();
            $xlsName  = "Tongji-Total-";
            $xlsCell  = array(
                array('date','时间'),
                array('view','访问量'),
                array('reg','注册量'),
                array('apply','申请量'),
                array('mert_apply','商家申请家数'),
                array('ip','访问IP量')
            );
        if(!$list){
            $this->error('没有数据可导出');
        }
        $this->exportExcel($xlsName,$xlsCell,$list);
    }

    //导出机构统计
    public function exportMert(){
        $sdate = input('sdate/s','');
        $edate = input('edate/s','');

            $list = TjMert::order('id', 'desc')->where('date','between',[$sdate,$edate])->select();
            if($list){
                foreach ($list as &$vo) {
                    $vo->mert_name = $vo->mert->name;
                }
                $xlsName  = "Tongji-Mert-";
                $xlsCell  = array(
                    array('date','时间'),
                    array('mert_name','机构名称'),
                    array('view','页面访问量'),
                    array('link','商家链接访问量'),
                    array('apply','申请量'),
                    array('ip','访问IP量')
                );
            }

        if(!$list){
            $this->error('没有数据可导出');
        }
        $this->exportExcel($xlsName,$xlsCell,$list);
    }



    //导出条件筛选统计
    public function exportTiaojian(){
        $sdate = input('sdate/s','');
        $edate = input('edate/s','');
        $type = input('type/s','');
        if(empty($type)){
            $this->error('参数错误');
        }

        if($type=="cash"){
            $list = TongjiCash::order('id', 'desc')->where('date','between',[$sdate,$edate])->select();
            $xlsName  = "Tongji-Cash-";
            $xlsCell  = array(
                array('date','时间'),
                array('cash1','0~1000元'),
                array('cash2','1000~5000元'),
                array('cash3','5000~10000元'),
                array('cash4','10000~20000元'),
                array('cash5','20000元以上')
            );
        }elseif($type=='date'){
            $list = TongjiDate::order('id', 'desc')->where('date','between',[$sdate,$edate])->select();
            $xlsName  = "Tongji-Date-";
            $xlsCell  = array(
                array('date','时间'),
                array('m1','0~1个月'),
                array('m2','1~3个月'),
                array('m3','3~6个月'),
                array('m4','6~9个月'),
                array('m5','9~12个月'),
                array('m6','12~15个月'),
                array('m7','15~18个月'),
                array('m8','18~21个月'),
                array('m9','21~24个月')
            );
        }elseif($type=='tag'){
            $list = TongjiTag::order('id', 'desc')->where('date','between',[$sdate,$edate])->select();
            $xlsName  = "Tongji-Tag-";
            $xlsCell  = array(
                array('date','时间'),
                array('t1','成功率高'),
                array('t2','速度快'),
                array('t3','利率低'),
                array('t4','额度高')
            );
        }
        if(!$list){
            $this->error('没有数据可导出');
        }
        $this->exportExcel($xlsName,$xlsCell,$list);
    }


    //分销统计导出
    public function exportLoan(){
        $sdate = input('sdate/s','');
        $edate = input('edate/s','');

            $list = TongjiLoan::order('id', 'desc')->where('date','between',[$sdate,$edate])->select();
            if($list){
                foreach ($list as &$wo) {
                    $wo->user_name = $wo->user->nickname;
                    $wo->mert_name = $wo->mert->name;
                }
            }
            $xlsName  = "Tongji-Loan-";
            $xlsCell  = array(
                array('date','时间'),
                array('user_name','会员'),
                array('mert_name','机构'),
                array('cash','放款金额'),
                array('get','提成点'),
                array('notes','备注')
            );

        if(!$list){
            $this->error('没有数据可导出');
        }
        $this->exportExcel($xlsName,$xlsCell,$list);
    }

    //ok
    public function mert() {
        $date = input('d',null);
        $mert_name=input('mert_name');
        $sdate = input('sdate',  date('Y-m-d',strtotime('-8 days')));
        $edate = input('edate',date('Y-m-d'));
        $map = [];
        if($mert_name){
            $row=Merchant::where('name',$mert_name)->field('id')->find();
            $map['mert_id']=$row->id;
        }


        if(!is_null($date)){
            if(is_numeric($date)){
                $sdate = date('Y-m-d',  strtotime('-'.$date.' days'));
            }elseif($date=='today'){
                $sdate = $edate;
            }elseif($date=='yestoday'){
                $sdate = $edate = date("Y-m-d",strtotime('-1 days'));
            }
        }
        $map['date'] = ['between',[$sdate,$edate]];
        $list = TjMert::order('id', 'desc')->where($map)->paginate(20,false,[
            'query' => request()->param()
        ]);
        $AllList = TjMert::order('id', 'desc')->where($map)->select();
        $allView = $totalLink= $totalApply= $totalIp = 0;
        foreach ($AllList as $vo) {
            $allView+=$vo->view;
            $totalLink+=$vo->link;
            $totalApply+=$vo->apply;
            $totalIp+=$vo->ip;

        }
        $this->assign('allView',$allView);
        $this->assign('totalLink',$totalLink);
        $this->assign('totalApply',$totalApply);
        $this->assign('totalIp',$totalIp);

        $num=TjMert::order('id', 'desc')->where($map)->count('id');
        $page = $list->render();
        $this->assign('_page',$page);
        $this->assign('_list',$list);
        $this->assign('_num',$num);


        $this->assign('d',$date);
        $this->assign('sdate',$sdate);
        $this->assign('edate',$edate);
        $this->assign('meta_title', '机构统计');


        $exportUrl = url('exportMert',[
            'sdate'=>$sdate,
            'edate'=>$edate,
        ]);
        $this->assign('exportUrl',$exportUrl);

        return $this->fetch();
    }


    //ok
    public function tiaojian() {
        $type = input('type',1);
        $date = input('d',null);
        $sdate = input('sdate',  date('Y-m-d',strtotime('-8 days')));
        $edate = input('edate',date('Y-m-d'));

        if(!is_null($date)){
            if(is_numeric($date)){
                $sdate = date('Y-m-d',  strtotime('-'.$date.' days'));
            }elseif($date=='today'){
                $sdate = $edate;
            }elseif($date=='yestoday'){
                $sdate = $edate = date("Y-m-d",strtotime('-1 days'));
            }
        }
        $typeRadio = bulid_form('radio', [
            1=>'金额',
            2=>'期限',
            3=>'标签'
        ], 'type',$type);


        $field = '';
        if($type==1){
            $list = TongjiCash::order('id', 'desc')->where('date','between',[$sdate,$edate])->select();
            $field = 'cash';
        }elseif($type==2){
            $list = TongjiDate::order('id', 'desc')->where('date','between',[$sdate,$edate])->select();
            $field = 'date';
        }elseif($type==3){
            $list = TongjiTag::order('id', 'desc')->where('date','between',[$sdate,$edate])->select();
            $field = 'tag';
        }

        $exportUrl = url('exportTiaojian',[
            'sdate'=>$sdate,
            'edate'=>$edate,
            'type'=>$field
        ]);
        $this->assign('exportUrl',$exportUrl);

        $this->assign('_list',$list);

        $this->assign('d',$date);
        $this->assign('sdate',$sdate);
        $this->assign('edate',$edate);
        $this->assign('type',$type);
        $this->assign('typeRadio',$typeRadio);
        $this->assign('meta_title', '条件筛选统计');
        return $this->fetch();
    }


    //ok
    public function recom() {
        $cmd = input('post.cmd/s','');
        if($cmd=='view'){
            $id = input('id/d',0);
            $lev = input('type');
            if(!$id || !$lev){
                finish(1,'参数不匹配');
            }

            //一级列表
            $list = TongjiRec::field('user_id,user_name')->order('id desc')->where('rec_id',$id)->select();
            $arr=[];
            $rec_ids=[];
            foreach ($list as $wo) {
                $rec_ids[]=$wo->user_id;
            }
            $row=Member::field('id,truename')->where('id','in',$rec_ids)->select();
            foreach($row as $vo){
                $arr[$vo->id]=$vo->truename;

            }

            foreach($list as $vo){
                $vo->tru_name='';
                $vo->tru_name= $arr[$vo->user_id];
            }

            if($lev==1){
                $title = '一级下线';
            }elseif($lev==2){
                $title = '二级下线';
                $rec_ids = [];

                //二级列表
                foreach ($list as $wo) {
                    $rec_ids[]=$wo->user_id;
                }

                $list = TongjiRec::field('user_id,user_name')->order('id desc')->where('rec_id','in',$rec_ids)->select();

                $arr=[];
                $rec_ids=[];
                foreach ($list as $wo) {
                    $rec_ids[]=$wo->user_id;
                }
                $row=Member::field('id,truename')->where('id','in',$rec_ids)->select();
                foreach($row as $vo){
                    $arr[$vo->id]=$vo->truename;

                }

               foreach($list as $vo){
                   // $row=Member::field('truename')->where('nickname',$vo->user_name)->find();
                    $vo->tru_name='';
                    $vo->tru_name=$arr[$vo->user_id];
                }

            }
            $num = count($list);
            $html='<div class="data-table table-striped">
		<table class="">
	    <thead>
	        <tr>
				<th class="txt-center">用户ID</th>
                <th class="txt-center">姓名</th>
				<th class="txt-center">用户名称</th> 
			</tr> 
	    </thead>
	    <tbody> ';
            if($list){
                foreach ($list as $vo) {
                    $html.='<tr>
                                    <td>'.$vo->user_id.'</td>
                                    <td>'.$vo->user_name.'</td>
                                    <td>'.$vo->tru_name.'</td>
                            </tr>';
                }
            }else{
                $html.='<td colspan="3" class="text-center"> aOh! 暂时还没有内容! </td>';
            }
            $html.='</tbody>
	    </table>
	</div>';

            finish(0,'',[
                'title'=>$title.' - 共推荐 '.$num.' 人',
                'content'=>$html
            ]);

        }


        $map = [];

        $uname = input('wd/s',null);
        if($uname){
            $rec_names=Member::where('truename',$uname)->field('nickname')->find();
            $map[]=$uname;
            if($rec_names['nickname']!=''){
                $map[]=$rec_names['nickname'];
            }
        }
//        $data = [];
        if($uname!=null) {
            $list = TongjiRec::field('rec_id,rec_name')->where('rec_name', 'in', $map)->order('id desc')->group('rec_id')->paginate(20);
        }else{
            $list = TongjiRec::field('rec_id,rec_name')->where($map)->order('id desc')->group('rec_id')->paginate(20);

        }

        /*$list =TongjiRec::alias('a')->field('rec_id,rec_name,truename')->join('member'.' g','a.rec_name=g.nickname')
            ->where($map)->order('a.id desc')->group('a.rec_id')->paginate(20);*/

        $arr=[];
        $rec_ids=[];
        foreach ($list as $wo) {
            $rec_ids[]=$wo->rec_id;
        }
        $row=Member::where('id','in',$rec_ids)->select();
        foreach($row as $vo){
           $arr[$vo->id]=$vo->truename;

        }


       foreach($list as $vo){
           $vo->tru_name='';
           $vo->tru_name=$arr[$vo->rec_id];
           $vo->num1='';$vo->num2='';
           $vo->num1=TongjiRec::where('rec_id',$vo->rec_id)->count();
           $list2 = TongjiRec::field('user_id')->order('id desc')->where('rec_id',$vo->rec_id)->select();
           $num2=[];
           foreach ($list2 as $wo) {
               $num2[]=$wo->user_id;
           }
           $vo->num2=TongjiRec::where('rec_id','in',$num2)->count();
        }
        $page = $list->render();


        //  $list = TongjiRec::order('id desc')->select();
//        if($list){
//            foreach ($list as $vo) {
//                $child = [];
//                foreach ($list as $wo) {
//                    if($vo->user_id==$wo->rec_id){
//                        $child[]=[
//                            'user_id'=>$wo->user_id,
//                            'user_name'=>$wo->user_name,
//                        ];
//                    }
//                }
//                $data[$vo->rec_id]['rec_name'] = $vo->rec_name;
//                $data[$vo->rec_id]['child'][] = [
//                    'user_id'=>$vo->user_id,
//                    'user_name'=>$vo->user_name,
//                    'child'=>$child
//                ];
//            }
//        }


        $this->assign('_list',$list);
        $this->assign('_page',$page);
        $this->assign('meta_title', '推荐关系统计');
        return $this->fetch();
    }


//    private function findRec($user_id,$i=1){
//        global $data;
//        $row = MemberRec::where('user_id',$user_id)->field('rec_id')->find();
//        if($row){
//            $data[$i][]=$row->rec_id;
//            if($row->rec_id>0){
//                $i++;
//                $this->findRec($row->rec_id,$i);
//            }
//        }
//        return $data;
//    }


    //分销
    public function feixiao() {
        $sdate = input('sdate',  date('Y-m-d',strtotime('-8 days')));
        $edate = input('edate',date('Y-m-d'));
        $user_name = input('user_name');
        $mert_name = input('mert_name');
        $map = [];
        if($user_name){
            $row = Member::where('nickname',$user_name)->field('id')->find();
            if($row){
                $map['user_id'] = $row->id;
            }
        }

        if($mert_name){
            $row = Merchant::where('name',$mert_name)->field('id')->find();
            if($row){
                $map['mert_id'] = $row->id;
            }
        }
        $exportUrl = url('exportLoan',[
            'sdate'=>$sdate,
            'edate'=>$edate,
        ]);
        $this->assign('exportUrl',$exportUrl);



        $list = TongjiLoan::order('id desc')->where($map)->where('date','between',[$sdate,$edate])->select();
        if(!empty($list)){
            foreach($list as $vo){
                $vo['rec_uname'] = "";
                $row = Member::where('id',$vo->user_id)->field('nickname')->find();
                if($row){
                    $vo['rec_uname'] = $row->nickname;
                    //$row2 = Member::where('id',$row->rec_uid)->field('nickname')->find();
                    // if($row2){
                    //     $vo['rec_uname'] = $row2->nickname;
                    // }
                }
            }
        }
        $this->assign('_list',$list);
        $this->assign('meta_title', '分销提成统计');
        return $this->fetch();
    }
}
