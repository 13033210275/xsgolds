var Detail = new Vue({
	el: '#Detail',
	data:{
		baseApi:window.Apis.detail,
		listApi:window.Apis.comment,
		goodApi:window.Apis.good,
		id:proId,
		data:false,
		list:[],
		page:1,
		size:8
	},
	methods:{
		Refresh:function(){
			var url = this.listApi,
				that = this,
				list = this.list;
			var pra2 = {
	        	mert_id:that.id,
	        	page:that.page,
	        	size:that.size
	        };
			this.$http.post(url,pra2).then(function(data){
				var json = data.data;
				if(json.error != 0){
					if(json.error == 2){
		            	mui('#commentList').pullRefresh().endPullupToRefresh(true);
		            }
		            else{
		            	console.log(json.message)
						reutrn;
		            }
				}
	            else{
	            	that.list = list.concat(json.data);
	            	console.log(that.list)
	            	that.page++;
	            	mui('#commentList').pullRefresh().endPullupToRefresh(false);
	            }
	        })
		},
		goodFun:function(e){
			var _index = e.currentTarget.getAttribute('data-index'),
				good = this.list[_index].isgood,
				id = this.list[_index].id;
				console.log(_index)
				console.log(good)
			if(good) return;
			var pra = {
				comment_id:id
			};
			var that = this;
			this.$http.post(that.goodApi,pra).then(function(data){
				var json = data.data;
				if(json.error != 0){
					mui.toast(json.message)
					return;
				}
				that.list[_index].isgood = 1;
				that.list[_index].goods_num++;
	        })

		}
	},
	mounted:function(){
		var baseUrl = this.baseApi,
			listUrl = this.listApi,
			that = this;
		var pra = {
			id:that.id
		}
        this.$http.post(baseUrl,pra).then(function(data){
			var json = data.data;
			console.log(json)
			if(json.error != 0){
				console.log(json.message)
				return;
			}
            that.data = json.data;
            mui('#comment_num')[0].innerHTML = json.data.comment_num;
        })
        var pra2 = {
        	mert_id:that.id,
        	page:that.page,
        	size:that.size
        };
        this.$http.post(listUrl,pra2).then(function(data){
			var json = data.data;
			console.log(json)
			if(json.error != 0){
				console.log(json.message)
				return;
			}
            that.list = json.data;
            that.page++;
            that.$nextTick(function(){
            	mui.init({
					pullRefresh: {
						container: '#commentList',
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