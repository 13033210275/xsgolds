var zhuanTi = new Vue({
	el: '#zhuanTi',
	data:{
		list:[],
		name:'',
		desc:'',
		id:ztId,
		size:8,
		api:window.Apis.zhuantiList,
		page:1
	},
	methods:{
		Refresh:function(){
			var url = this.api+'?&page='+this.page,
				that = this,
				list = this.list;
			this.$http.post(url,{id:that.id,size:that.size}).then(function(data){
				var json = data.data;
				if(json.error != 0){
					if(json.error == 2){
		            	mui('#wayIndex').pullRefresh().endPullupToRefresh(true);
		            }
		            else{
		            	console.log(json.message)
						reutrn;
		            }
				}
	            else{
	            	that.list = list.concat(json.data);
	            	that.page++;
	            	mui('#wayIndex').pullRefresh().endPullupToRefresh(false);
	            }
	        })
		}
	},
	mounted:function(){
		var url = this.api,
			that = this;
		this.$http.post(url,{id:that.id,size:that.size}).then(function(data){
			var json = data.data;
			if(json.error != 0){
				console.log(json.message)
				return;
			}
			that.page++;
			that.name = json.data.zt_name;
			that.desc = json.data.shortdesc;
            that.list = json.data.product;
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
        })
	}
});





