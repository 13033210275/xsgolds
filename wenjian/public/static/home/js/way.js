var wayMain = new Vue({
	el: '#wayMain',
	data:{
		list:[],
		nothing:false,
		api:window.Apis.wayIndex,
		page:1,
		cashText:'',
		user_type:'',
		cash:'',
        class1:true,
        class2:false,
        class3:true,
        class4:false,
		loan_type:'',
		tag_id:'',
		openSlt:false,
		user_data:[
			{
				val:0,
				text:'全部'
			},
			{
				val:1,
				text:'自由职业'
			},
			{
				val:2,
				text:'上班族'
			},
			{
				val:4,
				text:'生意人'
			}
		],
		cash_data:[
			{
				val:'0',
				text:'综合排序'
			},
			{
				val:'0-1',
				text:'0~1000元'
			},
			{
				val:'1-5',
				text:'1000~5000元'
			},
			{
				val:'5-10',
				text:'5000~10000元'
			},
			{
				val:'10-20',
				text:'10000~20000元'
			},
			{
				val:'20+',
				text:'20000以上'
			}
		],
		loan_data:[
			{
				val:0,
				text:'全部'
			},
			{
				val:1,
				text:'现金贷'
			},
			{
				val:3,
				text:'信用卡'
			}
		],
		tag_data:[
			{
				val:0,
				text:'默认'
			},
			{
				val:1,
				text:'成功率高'
			},
			{
				val:2,
				text:'速度快'
			},
			{
				val:3,
				text:'额度高'
			},{
				val:4,
				text:'利率低'
			}
		]
	},
	methods:{
		Refresh:function(){
			var url = this.api+'?&page='+this.page,
				that = this,
				list = this.list;
			this.$http.get(url).then(function(data){
				var json = data.data;
				if(json.error != 0){
					if(json.error == 2){
		            	mui('#wayIndex').pullRefresh().endPullupToRefresh(true);
		            }
		            else{
		            	console.log(json.message)
						return;
		            }
				}
	            else{
	            	that.list = list.concat(json.data);
	            	that.page++;
	            	mui('#wayIndex').pullRefresh().endPullupToRefresh(false);
	            }
	        })
		},
		cashFun:function(e){
			this.cash = e.target.value;
			this.cashText = this.cash_data[e.target.selectedIndex].text;
			var url = 'ajax/tongji?action_id=10&record_id=' + this.cash;
			this.$http.post(url);
			this.goto();
		},
		userFun:function(e){
			this.user_type = e.target.getAttribute('data-val');
		},
		loanFun:function(e){
			this.loan_type = e.target.getAttribute('data-val');
		},
		tagFun:function(e){
			this.tag_id = e.target.getAttribute('data-val');
			var url = 'ajax/tongji?action_id=11&record_id=' + this.tag_id;
			this.$http.post(url);
		},
        openSltFun:function(){
            this.class1 =false;
            this.class2 = true;
            this.class3 =false;
            this.class4 = true;
            this.openSlt = this.openSlt ? false:true;
        },
		openSltFun2:function(){
            this.class1 =true;
            this.class2 = false;
            this.class3 =true;
            this.class4 = false;
        },
		GetQueryString:function(name){
             var reg = new RegExp('(^|&)'+ name +'=([^&]*)(&|$)');
             var r = window.location.search.substr(1).match(reg);
             if(r!=null)return  unescape(r[2]); return null;
        },
        goto:function(){
        	var url = '/home/product/index?' + '&user_type=' + this.user_type + '&cash=' + this.cash + '&loan_type=' +this.loan_type +'&tag_id=' + this.tag_id;
        	document.location.href = url;
        }
	},
	mounted:function(){
		var that = this,
			p_user_type = this.GetQueryString('user_type'),
			p_cash = this.GetQueryString('cash'),
			p_loan_type = this.GetQueryString('loan_type'),
			p_tag_id = this.GetQueryString('tag_id'),
			url = this.api + '?&user_type=' + p_user_type + '&cash=' + p_cash + '&loan_type=' + p_loan_type +'&tag_id=' + p_tag_id;
		this.user_type = p_user_type ? p_user_type:this.user_data[0].val;
		this.cash = p_cash ? p_cash:this.cash_data[0].val;
		this.loan_type = p_loan_type ? p_loan_type:this.loan_data[0].val;
		this.tag_id = p_tag_id ? p_tag_id:this.tag_data[0].val;
		this.cashText = this.cash_data[0].text;
		if(p_cash){
			for(var i=0;i<this.cash_data.length;i++){
				if(p_cash == this.cash_data[i].val){
					this.cash = this.cash_data[i].val;
					this.cashText = this.cash_data[i].text;
					mui('#wayMain .waySelect select')[0].value = this.cash_data[i].val;
					break;
				}
			}
		}
		if(this.cash == ''){
			this.cashText = this.cash_data[0].text;
		}
		this.api = url;
		this.$http.get(url).then(function(data){
			var json = data.data;
			if(json.error != 0){
				console.log(json.message);
				that.nothing = true;
				return;
			}
            that.list = json.data;
            that.page++;
           	if(json.data.length>0){
	            that.$nextTick(function(){
	            	mui.init({
						pullRefresh: {
							container: '#wayIndex',
							up: {
								contentrefresh: '正在加载...',
								callback:that.Refresh
							}
						}
					});
	            })
	        }
	        else{
	        	that.nothing = true;
	        }
        })
	}
});





