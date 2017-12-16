<?php

namespace app\admin\controller;
use app\admin\model\Partment as Pment;
use app\admin\model\Member;
use app\admin\model\MemberRec;
use app\admin\model\Apply;
use app\admin\model\TongjiClient;
use app\admin\model\TongjiPart;
use app\admin\model\TongjiUser;
use app\admin\model\Merchant;


class Partner extends Admin {


    /**
     * 中介列表首页（OK）
     */
    public function index() {
        $wd = input('wd/s','');
        $map = ['is_medi'=>1];
        if($wd){
            $map['partname'] = $wd;
        }
        $list = Pment::where($map)->order('id desc')->paginate(20);
        //by xssd csy 0905小树Golds临时需求
        foreach ($list as &$vo){
            $vo->num = '';
            $vo->num = Member::where('part_id',$vo->id)->count('id');

        }
        //by xssd csy 0905小树Golds临时需求 End
        $page = $list->render();
        $this->assign('_list',$list);
        $this->assign('_page',$page);
        $this->assign('meta_title', '中介列表');
        $this->assign('wd',$wd);

        return $this->fetch();
    }

    //状态修改
    //成员列表 状态修改
    public function changeStatusOpen() {
        $id = input('id/d');
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }
        $map['id']=$id;
        $this->resume('partment', $map);
    }

    public function changeStatusClose() {
        $id = input('id/d');
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }
        $map['id']=$id;
        $this->forbid('partment', $map);

    }


    //导出
    public function export(){
        $map = [];
        $name = input('name/s','');
        $sdate = input('sdate/s','');
        $edate = input('edate/s','');
        if($name){
            $map['part_name'] = ['like','%'.$name.'%'];
        }
        if($sdate && $edate){
            $map['date'] = ['between',[$sdate,$edate]];
        }elseif($sdate){
            $map['date'] = $sdate;
        }elseif($edate){
            $map['date'] = $edate;
        }
        $list = TongjiPart::order('date desc')->where($map)->select();
        if(!$list){
            $this->error('没有数据可导出');
        }
        $xlsName  = "Xiadan";
        $xlsCell  = array(
            array('date','日期'),
            array('part_name','中介'),
            array('apply_num','申请量'),
            array('loan_cash','放款量'),
        );
        $this->exportExcel($xlsName,$xlsCell,$list);
    }


    //ok
    public function order() {
        $name = input('name/s','');
        $sdate = input('sdate/s','');
        $edate = input('edate/s','');
        $map = [];
        if($name){
            $map['part_name'] = ['like','%'.$name.'%'];
        }
        if($sdate && $edate){
            $map['date'] = ['between',[$sdate,$edate]];
        }elseif($sdate){
            $map['date'] = $sdate;
        }elseif($edate){
            $map['date'] = $edate;
        }


        $list = TongjiPart::order('date desc')->where($map)->paginate(20);
        if(!empty($list)){
            foreach ($list as $vo) {
                $user = Member::where('id',$vo->user_id)->find();
                $vo['user_name'] = $user->truename;
            }
        }

        $page = $list->render();
        $this->assign('_list',$list);
        $this->assign('_page',$page);
        $this->assign('meta_title', '下单统计');

        $export = url('export',[
            'name'=>$name,
            'sdate'=>$sdate,
            'edate'=>$edate
        ]);
        $this->assign('export',$export);

        return $this->fetch();
    }

    //ok
    public function account() {
        $id = session('user_auth.id');
        $row = Member::get($id);
        $row->share = config('web_url').'?fm=share&sid='.think_encrypt($row->id);
        // $row->qrcodeImg = $this->createQRcode($row->share);
        if (!$row->qrcodeImg) {
            $row->qrcodeImg = $this->createQRcode($row->share);
            $member = new Member();
            $member->SaveQrimage($id, $row->qrcodeImg);
        }
        $this->assign('row',$row);
        $this->assign('meta_title', '我的帐户');
        return $this->fetch();
    }
    /**
     * 调用phpqrcode生成二维码
     * @param string $url 二维码解析的地址
     * @param int $level 二维码容错级别
     * @param int $size 需要生成的图片大小
     */
    public function createQRcode($url, $level = 3, $size = 4){
        Vendor('phpqrcode.phpqrcode');
        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        //生成二维码图片
        $object = new \QRcode();
        // $object->png($url, $path, $errorCorrectionLevel, $matrixPointSize, 2);
        ob_start();
        $object->png($url, false , $errorCorrectionLevel, $matrixPointSize , $margin = 2, $saveandprint=true);
        $imageString = "data:image/png;base64,".base64_encode(ob_get_contents());
        ob_end_clean();
        return $imageString;
    }

    //ok
    public function client() {
        $id = session('user_auth.id');
        $list = MemberRec::where('rec_id',$id)->order('id desc')->paginate(20);
        foreach ($list as $vo) {
            $vo["applyed"] = MemberRec::applyed($vo->user_id);
            // echo $vo["applyed"];
            // $list = $merchant->join('right join apply on (a.uid = b.uid)' );
        }
        $page = $list->render();
        $this->assign("hasMember", (Member::getManagerId($id) == $id ? 1 : 0));
        $this->assign('_list',$list);
        $this->assign('_page',$page);
        $this->assign('meta_title', '我的客户列表');
        return $this->fetch();
    }

    //ok
    public function loan() {
        $id = session('user_auth.id');
        //获取我的客户列表
        $list = TongjiUser::where('rec_id',$id)->order('id desc')->paginate(20);
        $page = $list->render();
        $this->assign("hasMember", (Member::getManagerId($id) == $id ? 1 : 0));
        $this->assign('_list',$list);
        $this->assign('_page',$page);
        $this->assign('meta_title', '借款用户列表');
        return $this->fetch();
    }

    //ok
    public function day() {
        $id = session('user_auth.id');
        $edate= date('Y-m-d');
        $sdate = date("Y-m-d",  strtotime('-30 days'));
        $obj = new TongjiClient;
        $row = $obj->where([
            'date'=>$edate,
            'user_id'=>$id
        ])->find();
        $list = $obj->where([
            'user_id'=>$id,
            'date'=>['between',[$sdate,$edate]]
        ])->select();
        $this->assign("hasMember", (Member::getManagerId($id) == $id ? 1 : 0));
        $this->assign('_list',$list);
        $this->assign('sdate',$sdate);
        $this->assign('edate',$edate);
        $this->assign('row',$row);
        $this->assign('meta_title', '每日统计');
        return $this->fetch();
    }

    //ok
    public function month() {
        $id = session('user_auth.id');
        $obj = new TongjiClient;

        //本月数据
        $row = [
            'add_client_num'=>0,
            'add_apply_num'=>0,
            'add_loan_num'=>0,
            'add_loan_cash'=>0,
        ];
        $today = date('Y-m-d');
        $rs0 = $this->getMonthDate($today);
        $res = $obj->where([
            'date'=>['between',[$rs0[0],$rs0[1]]],
            'user_id'=>$id
        ])->select();
        if($res){
            foreach ($res as $wo) {
                $row['add_client_num']+=$wo->add_client_num;
                $row['add_apply_num']+=$wo->add_apply_num;
                $row['add_loan_num']+=$wo->add_loan_num;
                $row['add_loan_cash']+=$wo->add_loan_cash;
            }
        }


        //过去12个月时间列表
        $data = [];
        for($i=0;$i<=11;$i++){
            $time = strtotime('-'.$i.' month');
            $month = date("Y-m",$time);
            $rs = $this->getMonthDate(date("Y-m-d",$time));
            $list = $obj->where([
                'user_id'=>$id,
                'date'=>['between',[$rs[0],$rs[1]]]
            ])->select();
            if($list){
                $s1 = $s2 = $s3 = $s4 = 0;
                foreach ($list as $vo) {
                    $s1+=$vo->add_client_num;
                    $s2+=$vo->add_apply_num;
                    $s3+=$vo->add_loan_num;
                    $s4+=$vo->add_loan_cash;
                }
                $data[$month] = [
                    'month'=>$month,
                    'add_client_num'=>$s1,
                    'add_apply_num'=>$s2,
                    'add_loan_num'=>$s3,
                    'add_loan_cash'=>$s4,
                ];
            }else{
                $data[$month] = [
                    'month'=>$month,
                    'add_client_num'=>0,
                    'add_apply_num'=>0,
                    'add_loan_num'=>0,
                    'add_loan_cash'=>0,
                ];
            }
        }
        $this->assign("hasMember", (Member::getManagerId($id) == $id ? 1 : 0));
        $this->assign('_list',$data);
        $this->assign('row',$row);
        $this->assign('meta_title', '每月统计');
        return $this->fetch();
    }


    //获取月的
    private function getMonthDate($date){
        $firstday = date('Y-m-01', strtotime($date));
        $lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
        return [$firstday,$lastday];
    }
    public function member()
    {
        $id = session('user_auth.id');
        $pmentData = Pment::where("user_id",$id)->select();
        $list = [];
        if($pmentData)
        {
            $sql = "part_id in(";
            foreach ($pmentData as $vo) {
                $sql = $sql . $vo->id .",";
            }
            $sql = substr($sql, 0, -1);
            $sql = $sql .")";
            $list = Member::where($sql)->select();
        }
        $this->assign("hasMember", (Member::getManagerId($id) == $id ? 1 : 0));
        $this->assign('_list',$list);
        $this->assign('_page',$page);
        $this->assign('meta_title', '二级代理列表');
        return $this->fetch();
    }
}
