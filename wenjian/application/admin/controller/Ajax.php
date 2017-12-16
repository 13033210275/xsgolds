<?php

namespace app\home\controller;
use app\home\model\Member;
use com\Sms;
use think\Session;
use app\home\model\Adv;
use app\home\model\Lunbo;
use app\home\model\Region;
use app\home\model\Bjx;
use app\home\model\Document;
use app\home\model\Comment;
use app\home\model\Merchant;
use app\home\model\Zuanti;
use app\home\model\Action;
use app\home\model\ActionLog;
use app\home\model\Apply;
use app\home\model\MemberRec;
use app\home\model\CommentFav;
use app\admin\model\RecommendEnter;

class Ajax extends Home{



    //口子大全列表(ok)
    public function getProductList(){
        $size = input('size/d',10);
        $page = input('page/d',1);
        $start = ($page-1)*$size;

        $user_type = input('user_type/d',0);
        $cash = input('cash/s','');
        $loan_type = input('loan_type/d',0);
        $tag_id = input('tag_id/d',0);  // 成功率高=1 速度快=2 额度高=3 利率低=4
        $day = input('day');
        $unit = input('unit');
        $map = [
            'status'=>1,
            'type'=>1
        ];

        if($day && $unit){
            $map['min_date'] = ['<=',$day];
            $map['max_date'] = ['>=',$day];
        }

        if($user_type){
            $job = get_jobs($user_type);
            if($job){
                $map['to_people']=['like','%'.$job.'%'];
            }
        }
        if($cash!='null' && $cash){
            if(strpos($cash, '-')!==false){
                list($min_cash,$max_cash) = explode('-', $cash);
                $map['min_cash'] = ['between',[$min_cash*1000,$max_cash*1000]];
                $map['max_cash'] = ['>=',$max_cash*1000];
            }elseif(strpos($cash, '+')!==false){
                $cash = str_replace('+', '', $cash);
                $map['min_cash'] = ['<=',$cash];
                $map['max_cash'] = ['>=',$cash];
            }else{
                $map['min_cash'] = ['<=',$cash];
                $map['max_cash'] = ['>=',$cash];
            }
        }
        if($loan_type){
            $map['cate_id'] = $loan_type;
        }
        if($tag_id){
            $tag = get_tags($tag_id);
            $map['tag']=['like','%'.$tag.'%'];
        }

        $list = Merchant::field('id,name,logo as img,tag,adv_txt,comment_num')->where($map)->order('rise asc')->limit($start,$size)->select();
        if(!$list){
            finish(2,'数据为空');
        }
        foreach ($list as &$vo) {
            $vo['img'] = $vo->picture->path;
            $vo['text'] = $vo->adv_txt?$vo->adv_txt:'快来看看';
            $vo['tags'] = $vo->tag?explode(',', $vo->tag):[];
            $vo['star'] = floatval($vo->ext->use_sys?$vo->ext->sys_score:$vo->ext->user_score);
            $vo['number'] = $vo->ext->apply_num?$vo->ext->apply_num:0;
            unset($vo->ext,$vo->adv_txt,$vo->tag);
        }

        finish(0,'',$list);
    }

    //分享链接（ok）
    public function getInviteUrl(){
        $uid = Session::get('uid');
        $url = 'http://www.xsgolds.com/';
        if($uid){
           $url= config('web_url').'?fm=share&sid='.think_encrypt($uid);
        }
        finish(0,'',$url);
    }


    //ok
    public function getProductDetail(){
        $id = input('id',0);
        if(!$id){
            finish(1,'参数错误');
        }
        $row = Merchant::field('id,name,logo,img,adv_txt as shortdesc,tag,status,min_cash,max_cash,min_date,max_date,min_day_unit,max_day_unit,min_rate,max_rate'
                . ',apply_tj,cailiao,use_area,to_people,pass_type,get_type,back_type,'
                . 'platform,youshi,tips,comment_num')->order('zt_rise asc,rise asc,id desc')->find($id);

        if(!$row || $row['status']==0){
            finish(1,'商家已经不存在');
        }
        if($row->logo){
            $row->logo = get_cover($row->logo,'path');
        }
        if($row->img){
            $row->img = get_cover($row->img,'path');
        }else{
            $row->img = '/static/home/images/dt.png';
        }
        if($row->tag){
            $row->tag = explode(',', $row->tag);
        }
        if($row->cailiao){
            $row->cailiao = explode(',', $row->cailiao);
        }
        if($row->use_area){
            $row->use_area = explode(',', $row->use_area);
        }
        if($row->to_people){
            $row->to_people = explode(',', $row->to_people);
        }
        $tmp = [];
        if($row->min_rate>0){
            $tmp[] = $row->min_rate.'%';
        }
        if($row->max_rate>0){
            $tmp[] = $row->max_rate.'%';
        }
        $row->rate = implode(' - ', $tmp);

        $row->apply_tj = nl2br($row->apply_tj);

        $tag = array(
            [
               'status'=>'已申请',
		'number'=>1
            ]
        );
        $row['commentTag'] = [];
        finish(0,'',$row);
    }



    public function getZtProduct(){
        $id = input('post.id/d',0);
        if(!$id){
            finish(1,'参数错误');
        }
        $size = input('size/d',10);
        $page = input('page/d',1);
        $start = ($page-1)*$size;


        $row = Zuanti::field('id,zt_name,shortdesc')->find($id);
        if(!$row){
            finish(1,'专题已经不存在');
        }

        $list = Merchant::field('id,name,logo as img,tag,adv_txt')->where([
            'zt_id'=>$id,
            'status'=>1
        ])->order('rise asc')->limit($start,$size)->select();
        if(!$list){
            finish(2,'数据为空');
        }
        foreach ($list as &$vo) {
            $vo['img'] = $vo->picture->path;
            $vo['text'] = $vo->adv_txt?$vo->adv_txt:'快来看看';
            $vo['tags'] = $vo->tag?explode(',', $vo->tag):[];
            $vo['star'] = floatval($vo->ext->use_sys?$vo->ext->sys_score:$vo->ext->user_score);
            $vo['number'] = $vo->ext->apply_num;
            unset($vo->ext,$vo->adv_txt,$vo->tag);
        }
        if($page>1){
            finish(0,'',$list);
        }else{
            $row['product'] = $list;
            finish(0,'',$row);
        }
    }

    ////////////////////index////////////////////

    //专题ok
    public function zhuanti(){
       $list = Zuanti::where('is_credit',0)->field('id,zt_name')->order('rise asc')->select();
       finish(0,'',$list);
    }

    //精品推荐(ok)
    public function indexHot(){
        $list = Merchant::field('id,zt_id,name as title,logo as img,txt as text')->where([
            'status'=>1,
            'is_index'=>1,
            'type'=>1
        ])->order('sub_rise asc,rise asc')->select();
        if($list){
            foreach ($list as &$vo) {
                $vo->img = $vo->picture->path;
                unset($vo->picture);
            }
        }
        finish(0,'',$list);
    }

    //信用卡(OK)
    public function indexCredit(){
        $list = Merchant::field('id,name as title,logo as img,url')->where([
            'status'=>1,
            'is_index'=>1,
            'type'=>2
        ])->order('sub_rise asc,rise asc')->select();
        if($list){
            foreach ($list as &$vo) {
                $vo->img = $vo->picture->path;
                unset($vo->picture);
            }
        }
        finish(0,'',$list);
    }

    //首页banner广告（OK）
    public function indexAdv(){
        $adv = new Adv();
        $banner = $adv->lists('index_banner');
        finish(0,'',$banner);
    }

    //首页口子资讯(OK)
    public function indexNews(){
        $data =  [];
        $list = Document::order('rise asc,id desc')->field('id,title,img,create_time')->limit(5)->select();
        foreach ($list as $vo) {
            $data[]=[
                'id'=>$vo->id,
                'img'=>$vo->picture->path,
                'text'=>$vo->title,
                'time'=>  fdate(strtotime($vo->create_time))
            ];
             $vo->img = $vo->picture->path;
        }
        finish(0,'',$data);
    }

    ////////////////////////////////////////


    //评论点赞(OK)
    public function goods(){
        $uid = $this->_islogin(true);
        $comment_id = input('comment_id/d',0);
        if(!$comment_id){
            finish(1,'请选择要点赞的评论');
        }
        $fav = new CommentFav();
        if($fav->where([
            'user_id'=>$uid,
            'comment_id'=>$comment_id
        ])->find()){
            finish(1,'你已经点过赞啦');
        }
        $obj = new Comment();
        $flag = $obj->where('id',$comment_id)->setInc('goods_num',1);
        if($flag){
            $fav->save([
                'user_id'=>$uid,
                'comment_id'=>$comment_id
            ]);
            finish(0);
        }
        finish(1,'点赞失败');
    }

    //评论列表OK
    public function commentList(){
        $uid = $this->_islogin(true);
        $mert_id = input('post.mert_id/d',0);
        $page = input('post.page/d',1);
        $size = input('post.size/d',10);
        if(!$mert_id){
            finish(1,'无评论机构');
        }
        $start = ($page-1)*$size;
        $list = Comment::where([
            'mert_id'=>$mert_id,
            'status'=>1
        ])->limit($start,$size)->select();
        if(!$list){
            finish(2,'暂无数据了');
        }
        $data =  [];
        $fav = new CommentFav();
        foreach ($list as $vo) {
            $isfav = 0;
            if($fav->where([
                'user_id'=>$uid,
                'comment_id'=>$vo->id
            ])->find()){
                $isfav = 1;
            }
            $data[] = [
                'id'=>$vo->id,
                'name'=>$vo->user_name,
                'date'=>$vo->create_time,
                'star'=>$vo->score,
                'status'=>$vo->type_text,
                'text'=>$vo->contents,
                'isgood'=>$isfav,
                'goods_num'=>$vo->goods_num
            ];
        }
        finish(0,'',$data);
    }

    //发表评论（OK）
    public function postComment(){
        $uid = $this->_islogin(true);
        $mert_id = input('post.mert_id/d',0);
        $score = input('post.score/d',0);
        $contents = input('post.contents/s','');
        $cash = input('post.cash',0);
        $type = input('post.type');
        $types = getCommentType();
        unset($types[0]);
        if(!$mert_id){
            finish(1,'无评论机构');
        }
        if(!$score){
            finish(1,'请打分');
        }
        if(!$contents){
            finish(1,'请输入评论内容');
        }
        if(!$type || !isset($types[$type])){
            finish(1,'无效借款状态');
        }


        $mert_name = Merchant::field('name')->find($mert_id);

        if(!$mert_name){
            finish(1,'评论不存在的机构');
        }

        $status = 0;
        $words = file_get_contents(BADWORD_FILE);
        $wordArr = explode(',', str_replace('，', ',', $words));
        if(!empty($wordArr)){
            foreach ($wordArr as $vo) {
                if(strpos($contents, $vo)!==false){
                    $status = -1;
                    break;
                }
            }
        }


        $obj = new Comment();
        $obj->mert_name = $mert_name->name;
        $obj->user_id = $uid;
        $obj->user_name = Session::get('uname');
        $obj->status = 0;
        $obj->mert_id = $mert_id;
        $obj->contents = $contents;
        $obj->score = $score;
        $obj->type = $type;
        $obj->cash = $cash;
        $obj->status = $status;

        $res = $obj->allowField(true)->save();
        if($res){
            finish(0);
        }
        finish(1,'评论失败');

    }

    //首页轮播(ok)
    public function indexLunbo(){
        $obj = new Lunbo();
        $list = $obj->where('status',1)->order('rise asc,id desc')->select();
        $data = [];
        if($list){
            $region = new Region();
            $oname = new Bjx();
            $sex = ['先生','女士'];
            foreach ($list as $vo) {
                if(!$vo->mert){
                    continue;
                }
                $suffix = $sex[array_rand($sex)];
                $city = $region->getRandCity();
                $uname = $oname->getRandName();
                $job = get_rand_jobs();
                $cash = rand($vo->mert->min_cash, $vo->mert->max_cash);
                $data[]=[
                    'id'=>$vo['merchant_id'],
                    'link'=>url('product/detail',['id'=>$vo['merchant_id']]),
                    'title'=>$vo->mert->name,
                    'img'=>get_cover_path($vo->mert->logo),
                    'text'=>$city->name.$job.'<span class=\'color\'>'.$uname->name.$suffix.'</span>今日借款<span class=\'color\'>'.$cash.'</span>元已经到账'
                ];
            }
        }
        finish(0,'',$data);
    }



    //手机验证码登录(OK)
    public function loginByPhone(){
         $phone = input('post.phone/s','');
         $code = input('post.code/s','');
         if(empty($phone) || strlen($phone)!=11){
            finish(1,'请输入正确手机号码');
         }
         if(!$code || strlen($code)!=6 || Session::get($phone)!=$code){
            finish(1,'请输入正确的验证码');
         }
         $user = new Member;
         $row = $user->where('mobile',$phone)->field('id,nickname,mobile')->find();
         if(!$row){
             finish(1,'手机号码不存在');
         }
         //登录成功
        Session::set('uid',$row->id);
        Session::set('uname',$row->nickname);
        finish(0);

    }


    //通过账号登录（OK）
    public function loginByAccount(){
        $phone = input('post.phone/s','');
        $passwd = input('post.passwd/s','');
        if(empty($phone) || strlen($phone)!=11){
            finish(1,'请输入正确手机号码');
        }
        if(strlen($passwd)<6 || strlen($passwd)>13){
            finish(1,'请输入6-13位的密码');
        }
        $user = new Member;
        $row = $user->where([
            'mobile'=>$phone
        ])->field('id,nickname,passwd,status')->find();
        if(!$row){
            finish(1,'账号或者密码错误');
        }
        if($row['status']==0){
            finish(1,'账号已经被禁止登录');
        }
        if($row->passwd!=think_ucenter_md5($passwd)){
            finish(1,'账号或者密码错误');
        }
        //登录成功
        Session::set('uid',$row->id);
        Session::set('uname',$row->nickname);
        finish(0);
    }


    //注册(OK)
    public function reg(){
        $phone = input('post.phone/s','');
        $code = input('post.code/d',0);
        $passwd = input('post.passwd/s','');
        $recom = input('post.recom/s','');
        $sid = input('post.sid','');
        $fm = input('post.fm','share');
        if(empty($phone) || strlen($phone)!=11){
            finish(1,'请输入正确手机号码');
        }
        if(!$code || strlen($code)!=6 || Session::get($phone)!=$code){
            finish(1,'请输入正确的验证码');
        }
        if(strlen($passwd)<6 || strlen($passwd)>13){
            finish(1,'请输入6-13位的密码');
        }
        $user = new Member;
        $uid = 0;
        if($sid){
            $uid = think_decrypt($sid);
        }
        $data = [];
        if($uid){ //存在系统推荐
            $res = Member::field('id,nickname')->find($uid);
            if($res){
                $data['rec_id'] = $uid;
                $user->rec_uid = $uid;
                $user->rec_uname = $res->nickname;
            }
        }elseif($recom && strlen($recom)==11){ //手工推荐
            $res = $user->where('mobile',$recom)->field('id,nickname')->find();
            $uid = $res->id;
            if($res){
                $data['rec_id'] = $uid;
                $user->rec_uid = $uid;
                $user->rec_uname = $res->nickname;
            }
        }else{
            $user->rec_uid = 0;;
            $user->rec_uname = '';
        }
        $user->nickname = $phone;
        $user->passwd = $passwd;
        $user->mobile = $phone;
        $flag = $user->save();
        if($flag){
            if(!empty($data)){
                $obj = new MemberRec;
                $data['user_id'] = $user->id;
                $obj->save($data);
            }
            if($uid){
                Member::where('id',$uid)->setInc('rec_num',1);
            }

            Session::delete($phone);
            Session::set('uid',$user->id);
            Session::set('uname',$phone);
            finish(0);
        }
        finish(1,'注册失败');
    }


     //重置密码(OK)
    public function reset(){
        $phone = input('post.phone/s','');
        $code = input('post.code/d',0);
        $passwd = input('post.passwd/s','');
        if(empty($phone) || strlen($phone)!=11){
            finish(1,'请输入正确手机号码');
        }
        if(!$code || strlen($code)!=6 || Session::get($phone)!=$code){
            finish(1,'请输入正确的验证码');
        }
        if(strlen($passwd)<6 || strlen($passwd)>13){
            finish(1,'请输入6-13位的密码');
        }
        $user = new Member;
        $res = $user->where('mobile',$phone)->find();
        $uid = $res->id;
        if(!$res){
            finish(1,'该手机号未注册');
        }
        $flag = $user->isUpdate(true)->save([
            'passwd'=>$passwd
        ],[
            'id'=>$uid
        ]);
        if($flag){
            Session::delete($phone);
            finish(0);
        }
        finish(1,'重置密码失败');
    }

    //检测手机(OK)
    public function checkPhone(){
        $phone = input('post.phone/s','');
        if(!$phone || strlen($phone)!=11){
            finish(1,'请输入正确的手机号码');
        }
        $user = new Member;
        $res = $user->checkMobile($phone);
        if($res){
            finish(0);
        }
        finish(2,'不存在手机号码');
    }

    //获取验证码(OK)
    public function getCode(){
        if (!request()->isPost()) {
            finish(1,'非法操作');
        }
        $key = input('post.key/s','');
        $phone = input('post.phone/s','');
        $fm = input('post.fm/s','');
        if(!$key){
            finish(1,'非法操作');
        }
        if(!$phone || strlen($phone)!=11){
            finish(1,'手机格式不正确');
        }
        if(Session::get('key')!=$key){
            finish(1,'非法来源');
        }


        $user = new Member;

        if($fm=='forget' || $fm=='login'){
            if(!$user->checkMobile($phone)){
                finish(1,'该手机号未注册');
            }
        }elseif($fm=='reg'){
            if($user->checkMobile($phone)){
                finish(1,'该手机号已经注册');
            }
        }else{
            finish(1,'参数不匹配');
        }


        $obj = new \app\admin\model\Code();
        $day = date('Y-m-d');
        $condition = [
            'day'=>$day,
            'phone'=>$phone
        ];
        $num = $obj->where($condition)->count();
        if($num>2){
           // finish(1,'今日发送次数已经用完');
        }

        $time = time();
        $row = $obj->where($condition)->field('send_time')->order('id desc')->find();
        if($row && $time-$row['send_time']<60){
            finish(1,'发送太频繁了，稍后再试');
        }
        $code = rand(100000,999999);

        Session::set($phone,$code);
        //finish(0,'',$code);


        $demo = new Sms(
            "LTAIiSFPppx8ZQLn",
            "6DPqCnMgo19jrTp4kIHmChbSYjFm40"
        );
        $response = $demo->sendSms(
            "小树Golds", // 短信签名
            "SMS_78450027", // 短信模板编号
            $phone, // 短信接收者
            Array(  // 短信模板中字段的值
                "code"=>$code,
            )
        );
        if(!$response){
            finish(1,'发送失败');
        }
        if($response->Code=='OK'){
            Session::delete('key');
            $flag = $obj->insert([
                'phone'=>$phone,
                'code'=>$code,
                'ip'=> ip2long(\think\Request::instance()->ip()),
                'send_time'=>$time,
                'day'=>$day
            ]);
            Session::set($phone,$code);
            finish(0,'验证码发送成功');
        }else{
            finish(1,$response->Message);
        }
    }

    //ok
    public function tongji(){
        $action_id = input('action_id/d',0);
        $record_id = input('record_id/s','');
        if(!$action_id){
            finish(1,'参数错误');
        }
        $row = Action::field('title,model')->find($action_id);
        if(!$row){
            finish(1,'非法统计项目');
        }
        $user_id = Session::get('uid');
        $obj           = new ActionLog;
        $obj->action_id     = $action_id;
        $obj->record_id    = $record_id;
        if($user_id){
            $obj->user_id = $user_id;
        }
        $obj->remark = $row->title;
        $obj->model = $row->model;
        $obj->action_ip  = \think\Request::instance()->ip();
        $flag= $obj->save();
        if($flag){
            finish(0);
        }
        finish(1,'统计失败');
    }


    //申请(OK)
    public function apply(){
        $user_id = $this->_islogin(true);
        $mert_id = input('post.mert_id/d',0);
        $cash = input('post.cash/d',0);
        $day = input('post.day/d',0);
        if(!$mert_id || !$cash || !$day){
            finish(1,'参数错误');
        }
        $row = Merchant::field('id,status,min_cash,max_cash,min_date,max_date,min_day_unit,max_day_unit')->find($mert_id);
        if(!$row || $row['status']!=1){
            finish(1,'机构已经不存在');
        }
        if($cash>$row->max_cash || $cash<$row->min_cash){
            finish(1,'借款金额不符合该机构提供金额范围');
        }
        if($day<$row->min_date || $day>$row->max_date){
            finish(1,'借款时间不符合该机构提供时长');
        }

        //检测是否黑名单
        $user = Member::where('id',$user_id)->field('status,golds_black,wd_black')->find();
        if(!$user){
            finish(1,'用户不存在');
        }
        if($user['status']!=1){
            finish(1,'用户已经被禁用');
        }
        if($user['golds_black'] || $user['wd_black']){
            finish(1,'黑名单用户无法申请贷款产品');
        }

        $obj = new Apply;
        $obj->user_id = $user_id;
        $obj->mert_id = $mert_id;
        $obj->cash = $cash;
        $obj->day = $day;
        $obj->unit = $row->min_day_unit;
        $flag = $obj->save();
        if($flag){
            finish(0);
        }
        finish(1,'申请失败');

    }
}
