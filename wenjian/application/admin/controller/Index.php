<?php
 
namespace app\admin\controller;  
use app\admin\model\Comment;
use app\admin\model\Member;
use app\admin\model\Merchant;
use app\admin\model\Partment;


/**
 * 后台首页控制器 
 */
class Index extends Admin  {

    /**
     * 后台首页
     * @author xsgolds
     */
    
    public function index(){  
        $this->assign('meta_title','系统首页') ;
        return $this->fetch();
    }
    
    
    public function info(){ 
        
        
        $num = Comment::where([
            'status'=>0
        ])->count();
        $this->assign('num',$num);
        $this->assign('meta_title','待办事务') ;
        return $this->fetch();
    }
    
    
    public function tongji(){
        
        //今日新增会员数
        $todayMember = Member::whereTime('reg_time', 'today')->where('is_reg',1)->count();
        $this->assign('todayMember',$todayMember);
        
        //总会员数
        $totalMember = Member::where('is_reg',1)->count();
        $this->assign('totalMember',$totalMember);
        
        //今日新作机构
        $todayMerchant = Merchant::whereTime('create_time', 'today')->count();
        $this->assign('todayMerchant',$todayMerchant);
        
        //总机构数
        $totalMerchant = Merchant::count();
        $this->assign('totalMerchant',$totalMerchant);
        
        
        //新增业务合伙人
        $todayPart = Partment::whereTime('create_time', 'today')->where('is_medi',1)->count();
        $this->assign('todayPart',$todayPart);
        
        
        //总业务合伙人
        $totalPart = Partment::where('is_medi',1)->count();
        $this->assign('totalPart',$totalPart);
        
        $this->assign('meta_title','网站数据统计') ;
        return $this->fetch();
    }

}
