window.Apis = {
	banner:'/home/ajax/indexAdv',	//首页banner的josn
	people:'/json/people.json',	//首页上班族生意人等人群
	userShow:'/home/ajax/indexLunbo',	//首页上班群下面轮播
	recommend:'/home/ajax/indexHot',		//首页精品推荐
	card:'/home/ajax/indexCredit',	//首页办信用卡
	info:'/home/ajax/indexNews',	//首页口子资讯
	zhuanti:'/home/ajax/zhuanti',	//首页专题
	zhuantiList:'/home/ajax/getZtProduct',	//专题列表
	wayIndex:'/home/ajax/getProductList',	//口子大全
	detail:'/home/ajax/getProductDetail',	//产品详情页的josn
	comment:'/home/ajax/commentList',	//评论json
	postComment:'/home/ajax/postComment',	//提交评论
	getCode:'/home/ajax/getCode',		//获取验证码
	getCodes:'/home/ajax/getCodes',//着陆页获取验证码
	register:'/home/ajax/reg',		//注册
	reset:'/home/ajax/reset',		//重置密码
	passLogin:'/home/ajax/loginByAccount',		//密码登录
	codeLogin:'/home/ajax/loginByPhone',		//验证码登录
	isLogin:'/home/login/isLogin',		//是否登录
	logout:'/home/login/logout',	//退出登录
	invite:'/home/ajax/getInviteUrl',		//邀请码
	apply:'/home/ajax/apply', //申请商家
  good:'/home/ajax/goods',		//点赞

  recommend_bottom:'../ajax/getBottomData', //分销推荐页底部入口
  recommend_banner:'../ajax/getBannerView',  //分销推荐页banner
  recommend_list:'../ajax/getDistributionList',  //分销推荐页list
  recommend_index: '/home/ajax/getAllRecommend',
}

window.Util = {
  //获取url参数
  getUrlString:function(key){
    var reg=new RegExp("(^|&)" + key + "=([^&]*)(&|$)","i");
    var r=window.location.search.substr(1).match(reg);
    if(r!=null){
        return (r[2]);
    }
    return null ;
  },
}