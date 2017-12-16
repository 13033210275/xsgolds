<?php

namespace app\home\controller;
use app\home\model\Member;
use app\home\model\Merchant;
use think;
use think\Session;


class Product extends Home{


    public function index(){

        $golds_source = input('golds_source');
        $this->assign('isshow',$golds_source);
        $this->assign('curr','product');
        $this->assign('seo_title','口子大全');
        return $this->fetch();
    }


    public function detail($id){
        $golds_source = input('golds_source');
        $this->assign('isshow',$golds_source);
        $this->assign('id',$id);
        $this->assign('nav',1);
        $this->assign('seo_title','商家详情');
        return $this->fetch();
    }

    public function zhuanti($id){
        $golds_source = input('golds_source');
        $this->assign('isshow',$golds_source);
        $this->assign('id',$id);
        $this->assign('seo_title','专题产品');
        return $this->fetch();
    }

    public function match(){
        $golds_source = input('golds_source');
        $this->assign('isshow',$golds_source);
        $this->assign('curr','match');
        $this->assign('seo_title','智能匹配');
        return $this->fetch();
    }

    public function credit(){

        $golds_source = input('golds_source');
        $this->assign('isshow',$golds_source);
        return $this->fetch();
     }

    //csy golds350 关于小树Golds推荐页在小树时代中的自动执行登录注册动作
    public function curl_http($url, $data=array(), $header=array(), $timeout=30){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        $response = curl_exec($ch);

        if($error=curl_error($ch)){
            die($error);
        }

        curl_close($ch);

        return $response;

    }

    public function recommend(){
        $mobile = input('mobile','');
        $sor_code = input('sor_code','');
        $user_id=input('user_id','');
        if(($sor_code!=''&&$mobile!='')||($sor_code!=''&&$user_id!='')) {
            if($mobile!=''){
                $requestData = '{"act":"golds_zd_reg","mobile":"'.$mobile.'"}';
            }else if($user_id!=''){
                $requestData = '{"act":"golds_zd_reg","user_id":"'.$user_id.'"}';
            }
            $targetUrl = 'http://www.xiaoshushidai.com/mapi/index.php?r_type=1&i_type=2&mrt=mrt7';
            $data = array('requestData'=>  $requestData);
            $header = array();
            $response =$this->curl_http($targetUrl, $data, $header, 5);
            $data=json_decode($response, true);

            $row = Member::field('id,nickname')->where('mobile', $data['mobile'])->find();//是否已在Golds注册过
            if (!$row) {
                $user = new Member;
                $user->mobile = $data['mobile'];
                $user->id_card = $data['idno'];
                $user->rec_type=2;
                $user->sor_code = $sor_code;
                $user->truename = $data['real_name'];
                //$user->nickname = $data['user_name'];
                $user->nickname = $data['mobile'];
                $user->passwd = $data['mobile'];
                $user->rec_uid=0;
                $user->rec_uname = '';
                $user->save();
                $row = Member::field('id,nickname')->where('mobile', $data['mobile'])->find();
                Session::set('uid', $row['id']);
                Session::set('uname', $row['nickname']);
            } else {
                Session::set('uid', $row['id']);
                Session::set('uname', $row['nickname']);
            }
        }
        //csy golds350 关于小树Golds推荐页在小树时代中的自动执行登录注册动作 End

        $this->assign('curr','recommend');
        $this->assign('nav',1);
        $this->assign('seo_title','分享推荐');
        return $this->fetch();
    }

    //推荐页二
    public function recommends(){
        $mobile = input('mobile','');
        $sor_code = input('sor_code','');
        $user_id=input('user_id','');
        if(($sor_code!=''&&$mobile!='')||($sor_code!=''&&$user_id!='')) {
            if($mobile!=''){
                $requestData = '{"act":"golds_zd_reg","mobile":"'.$mobile.'"}';
            }else if($user_id!=''){
                $requestData = '{"act":"golds_zd_reg","user_id":"'.$user_id.'"}';
            }
            $targetUrl = 'http://www.xiaoshushidai.com/mapi/index.php?r_type=1&i_type=2&mrt=mrt7';
            $data = array('requestData'=>  $requestData);
            $header = array();
            $response =$this->curl_http($targetUrl, $data, $header, 5);
            $data=json_decode($response, true);

            $row = Member::field('id,nickname')->where('mobile', $data['mobile'])->find();//是否已在Golds注册过
            if (!$row) {
                $user = new Member;
                $user->mobile = $data['mobile'];
                $user->id_card = $data['idno'];
                $user->rec_type=2;
                $user->sor_code = $sor_code;
                $user->truename = $data['real_name'];
                //$user->nickname = $data['user_name'];
                $user->nickname = $data['mobile'];
                $user->passwd = $data['mobile'];
                $user->rec_uid=0;
                $user->rec_uname = '';
                $user->save();
                $row = Member::field('id,nickname')->where('mobile', $data['mobile'])->find();
                Session::set('uid', $row['id']);
                Session::set('uname', $row['nickname']);
            } else {
                Session::set('uid', $row['id']);
                Session::set('uname', $row['nickname']);
            }
        }
        //csy golds350 关于小树Golds推荐页在小树时代中的自动执行登录注册动作 End

        $this->assign('curr','recommend');
        $this->assign('nav',1);
        $this->assign('seo_title','分享推荐');
        return $this->fetch();
    }


    public function agreement(){
        $golds_source = input('golds_source');
        $this->assign('isshow',$golds_source);
        $this->assign('seo_title','小树Golds资料授权及免责协议');
        return $this->fetch();
    }

    public function apply($id){

        if(!$id){
            $this->error('参数错误');
        }
        $row = Merchant::field('id,url')->find($id);
        if(!$row){
            $this->error('商家已经不存在');
        }
        $url = $row->url?$row->url:url('detail',['id'=>$id]);
        $golds_source = input('golds_source');
        $this->assign('isshow',$golds_source);
        $this->assign('id',$id);
        $this->assign('url',$url);
        $this->assign('seo_title','资料一键授权');
        return $this->fetch();
    }

}
