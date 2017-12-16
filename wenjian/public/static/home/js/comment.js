var Detail = new Vue({
	el: '#comment',
	data:{
		stars:0,
		text:'',
		max:140,
		id:proId,
		type:0,
		cash:'',
		api:Apis.postComment,
		type_data:[
			{
				val:1,
				text:'已申请'
			},
			{
				val:2,
				text:'出额度'
			},
			{
				val:3,
				text:'已放款'
			},
			{
				val:4,
				text:'被拒绝'
			},
			{
				val:5,
				text:'其他'
			}
		],
	},
	methods:{
		selectStar:function(e){
			var stars = parseInt(e.target.getAttribute("data-index"))+1;
			this.stars = stars;
		},
		textInput:function(e){
			if(e.target.value.length>this.max){
				this.text = e.target.value.substr(0,this.max);
				e.target.value = this.text;
			}
			else{
				this.text = e.target.value;
			}
		},
		typeFun:function(e){
			this.type = e.target.getAttribute('data-val');
		},
		cashFun:function(e){
			this.cash = e.target.value;
		},
		postIt:function(){
			if(this.text==''){
				mui.toast('评论内容不能为空！');
                return;
			}
			else if(this.stars==0){
				mui.toast('评分不能为0！');
                return;
			}
			else if(this.type==0){
				mui.toast('请选择借款状态！');
                return;
			}
			var that = this;
			var url = this.api;
			var pra = {
				mert_id:that.id,
				score:that.stars,
				contents:that.text,
				type:that.type
			};
			if(that.cash){
				pra.cash = that.cash
			}
	        this.$http.post(url,pra).then(function(data){
				var json = data.data;
				if(json.error != 0){
					mui.toast(json.message)
					reutrn;
				}
				document.location.href = '/home/comment/index/id/'+that.id;
	        })
		}
	},
	mounted:function(){

	}
});