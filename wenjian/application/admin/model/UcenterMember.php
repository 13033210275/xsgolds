<?php

namespace app\admin\model;

use think\Model;

define('UC_AUTH_KEY', \think\Config::get('uc_auth_key'));

class UcenterMember extends Model {

    // 定义时间戳字段名
    protected $createTime = 'reg_time';
    protected $updateTime = 'update_time';
    protected $insert = ['password', 'status' => 1, 'reg_ip', 'reg_time'];
    protected $update = ['password', 'update_time'];

    protected static function init() {
        UcenterMember::afterInsert(function ($user) {
            Partment::where('id',$user->partment_id)->setInc('num',1);
        });
    }

    public function part() {
        return $this->belongsTo('Partment', 'partment_id')->field('partname');
    }

    /**
     * 根据配置指定用户状态
     * @return integer 用户状态
     */
    protected function getStatusTextAttr($value, $data) {
        $arr = [
            0 => '禁用',
            1 => '启用'
        ];
        return $arr[$data['status']]; //TODO: 暂不限制，下一个版本完善
    }

    public function getRateAttr($value) {
        return handle_zero($value);
    }
    
    
    public function getRateTextAttr($value, $data) {
        if ($data['rate'] > 0) {
            return handle_zero($data['rate']) . '%';
        }
        return '-';
    }

    protected function setPasswordAttr($value, $data) {
        return think_ucenter_md5($data['password'], UC_AUTH_KEY);
    }

    protected function setRegIpAttr($value, $data) {
        return get_client_ip(1);
    }

    /**
     * 用户登录认证
     * @param  string  $username 用户名
     * @param  string  $password 用户密码
     * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function login($username, $password, $type = 1) {
        $map = array();
        switch ($type) {
            case 1:
                $map['username'] = $username;
                break;
            case 2:
                $map['email'] = $username;
                break;
            case 3:
                $map['mobile'] = $username;
                break;
            case 4:
                $map['id'] = $username;
                break;
            default:
                return 0; //参数错误
        }
        $user = $this->where($map)->find();
        if ($user) {
            $user = $user->toArray();
        }
        if ($user && $user['status']) {
            /* 验证用户密码 */
            if (think_ucenter_md5($password, UC_AUTH_KEY) === $user['password']) {
                /* 更新登录信息 */
                $data = array(
                    'uid' => $user['uid'],
                    'login' => array('exp', '`login`+1'),
                    'last_login_time' => time(),
                    'last_login_ip' => get_client_ip(1),
                );
                $this->where(array('uid' => $user['uid']))->update($data);
                $auth = array(
                    'uid' => $user['uid'],
                    'username' => $user['username'],
                    'last_login_time' => $user['last_login_time'],
                );
                session('user_auth', $auth);
                session('user_auth_sign', data_auth_sign($auth));
                return $user['uid']; //登录成功，返回用户ID
            } else {
                return -2; //密码错误
            }
        } else {
            return -1; //用户不存在或被禁用
        }
    }

    /**
     * 获取用户信息
     * @param  string  $uid         用户ID或用户名
     * @param  boolean $is_username 是否使用用户名查询
     * @return array                用户信息
     */
    public function info($uid, $is_username = false) {
        return $this->info($uid, $is_username);
    }

    /**
     * 检测用户名
     * @param  string  $field  用户名
     * @return integer         错误编号
     */
    public function checkUsername($username) {
        return $this->checkField($username, 1);
    }

    /**
     * 检测邮箱
     * @param  string  $email  邮箱
     * @return integer         错误编号
     */
    public function checkEmail($email) {
        return $this->checkField($email, 2);
    }

    /**
     * 检测手机
     * @param  string  $mobile  手机
     * @return integer         错误编号
     */
    public function checkMobile($mobile) {
        return $this->where('mobile', $mobile)->value('mobile');
    }

    /**
     * 更新用户信息
     * @param int $uid 用户id
     * @param string $password 密码，用来验证
     * @param array $data 修改的字段数组
     * @return true 修改成功，false 修改失败
     * @author xsgolds
     */
    public function updateInfo($uid, $password, $data) {
        if ($this->updateUserFields($uid, $password, $data) !== false) {
            $return['status'] = true;
        } else {
            $return['status'] = false;
            $return['info'] = $this->getError();
        }
        return $return;
    }

}
