<?php 
namespace app\admin\Controller;

use think\Controller;
use com\Sms;
use think\Session;
use app\admin\model\Member;

/**
 * 后台首页控制器 
 */
class Publics extends Controller {

    public function __construct() {
        /* 读取数据库中的配置 */
        $config = cache('db_config_data');
        if (!$config) {
            $config = api('Config/lists');
            $config ['var_module'] = request()->module();
            $config ['var_controller'] = request()->controller();
            $config ['var_action'] = request()->action();
            $config ['template']['view_path'] = APP_PATH . 'admin/view/' . $config['admin_view_path'] . '/'; //模板主题
            $config['dispatch_error_tmpl'] = APP_PATH . 'admin' . DS . 'view' . DS . $config['admin_view_path'] . DS . 'public' . DS . 'error.html'; // 默认错误跳转对应的模板文件
            $config['dispatch_success_tmpl'] = APP_PATH . 'admin' . DS . 'view' . DS . $config['admin_view_path'] . DS . 'public' . DS . 'success.html'; // 默认成功跳转对应的模板文件
            cache('db_config_data', $config);
        }
        config($config); //添加配置
        parent::__construct();
    }

    /**
     * 后台用户登录 
     */
    public function login($phone = null, $password = null, $verify = null) {
        
        if (request()->isPost()) { 
            if(!$verify || Session::get($phone)!=$verify){
               // $this->error('无效短信验证码');
            }
            $user = new Member;
            $row = $user->where('mobile',$phone)->where('is_reg',0)->field('id,mobile,nickname,status,truename,passwd')->find();
            
            if(!$row){
                $this->error('用户不存在或被禁用');
            }
//            if($row->passwd!= think_ucenter_md5($password)){
//                $this->error('密码错误');
//            }
            if($row->status==0){
                $this->error('用户不存在或被禁用');
            }
            
            Session::delete($phone);
            $time = time();
            $data = array(
                'id'              => $row->id,
                'last_login_time' => $time,
                'last_login_ip'   => get_client_ip(1),
            ); 
            $user->where('id',$row->id)->update($data);
            $auth = array(
                'id' => $row->id,
                'username' => $row->login_name,
                'last_login_time' => $time,
            );
            session('user_auth', $auth);
            session('user_auth_sign', data_auth_sign($auth));
            $this->success('登录成功！', url('Index/index'));
        } else {
            if (is_login()) {
                $this->redirect('Index/index');
            } else {
                /* 读取数据库中的配置 */
                $config = cache('db_config_data');
                if (!$config) {
                    $config = model('Config')->lists();
                    cache('db_config_data', $config);
                }
                config($config); //添加配置
                $key = md5(\think\Request::instance()->url(true));
                Session::set('key',$key);
                $this->assign('key',$key);
                return $this->fetch();
            }
        }
    }

    /* 退出登录 */
    public function logout() {
        if (is_login()) {
            Session::delete('user_auth');
            Session::delete('user_auth_sign');
            $this->success('退出成功！', url('login'));
        } else {
            $this->redirect('login');
        }
    }
    
    
    public function send(){
        $this->error('测试期间，短信发送关闭');
        if (!request()->isPost()) {
            $this->error('非法操作');
        }
        $key = input('post.key/s','');
        $phone = input('post.phone/s','');
        
        $result = $this->validate($_POST, [
            'key'  => 'require',
            'phone' => 'require|length:11'
        ],[
            'key.require'=>'非法来源',
            'phone.length'=>'手机格式不正确', 
        ]);
        if(true !== $result){
            $this->error($result);
        }          
        if(Session::get('key')!=$key){
            $this->error('非法来源');
        } 
        
        $user = new Member;
        if(!$user->where('mobile',$phone)->find()){
            $this->error('该手机号未注册');
        } 
        $obj = new \app\admin\model\Code();
        $day = date('Y-m-d');
        $condition = [
            'day'=>$day,
            'phone'=>$phone
        ];
        $num = $obj->where($condition)->count();
        if($num>2){
           // $this->error('今日发送次数已经用完');
        }
        
        $time = time();
        $row = $obj->where($condition)->field('send_time')->order('id desc')->find();
        if($row && $time-$row['send_time']<60){
            $this->error('发送太频繁了，稍后再试');
        } 
        
        $code = rand(100000,999999);
        
        
        
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
            $this->error('发送失败');
        }
        if($response->Code=='OK'){
            $flag = $obj->insert([
                'phone'=>$phone,
                'code'=>$code,
                'ip'=> \think\Request::instance()->ip(1),
                'send_time'=>$time,
                'day'=>$day
            ]); 
            Session::delete('key');
            Session::set($phone,$code); 
            $this->success('验证码发送成功');
        }else{
            $this->error($response->Message);
        }
    }

}
