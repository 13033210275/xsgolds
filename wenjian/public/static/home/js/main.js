var attention = new Vue({
	el:'#attention-tip',
	data:function(){
		return {
			show_attention:false,
			count:0,
		}
	},
	watch:{
		'count':function(){
			if(this.count >= 4){
				if(!sessionStorage.getItem('show_attention')){
					this.show_attention = true;
					document.getElementsByClassName("mainBox")[0].classList.add('attention-top');
				}
			}
		}
	},
	methods:{
		toggle:function(type){
			this[type] = !this[type]
			sessionStorage.setItem('show_attention',1)
			document.getElementsByClassName("mainBox")[0].classList.remove('attention-top')
		}
	},
	mounted:function(){
		var self = this;
		// setTimeout(function(){
		// 	if(!sessionStorage.getItem('show_attention')){
		// 		self.show_attention = true;
		// 		// document.getElementsByClassName("mainBox")[0].classList.add('attention-top');
		// 	}
		// },500)
		
	}
})


var banner = new Vue({
	el: '#banner',
	data:{
		pics:[]
	},
	mounted:function(){
		var url = window.Apis.banner,
			that = this;
		this.$http.get(url).then(function(data){

			attention.count ++ ;

			var json = data.data;
			if(json.error != 0){
				console.log(json.message)
				return ;
			}
            that.pics = json.data;
            that.$nextTick(function(){
            	mui(that.$el).slider({
					interval:5000
				});
				tongji.banner();
            })
        })
	}
});

// var people = new Vue({
// 	el:'#people',
// 	data:{
// 		data:[]
// 	},
// 	mounted:function(){
// 		var url = window.Apis.people,
// 			that = this;
// 		this.$http.get(url).then(function(data){
// 			var json = data.data;
// 			if(json.error != 0){
// 				console.log(json.message);
// 				return;
// 			}
//             that.data = json.data;
//         })
// 	}
// })

var serverBox = new Vue({
	el:'#server',
	data:{
		data:[],
		index:-1,
		list:[],
	},
	created:function(){
		var url = window.Apis.recommend_index,
			that = this;
        this.$http.get(url).then(function(data){

        	attention.count ++ ;

			var json = data.data;
			if(json.error != 0){
				console.log(json.message);
				return;
			}
            that.data = json.data;
            if(json.data.length > 0){
            	that.index = json.data[0].id
            	that.list = json.data[0].datas
            	// that.getRecommend(json.data[0])
            }
            that.$nextTick(function(){
				tongji.zhuanti();
            })
        });
        
	},
	mounted:function(){
		
	},
	methods:{
		getRecommend:function(item){
			if(item.id == this.index) return false;
			this.index = item.id;
			this.list = item.datas;
		}
		// getRecommend:function(item){
		// 	if(item.id == this.index ) return false;
		// 	this.index = item.id ;
		// 	var url_recommend = window.Apis.recommend_index + item.id,
		// 		self = this;
		// 	console.log(url_recommend)
		// 	this.$http.get(url_recommend).then(function(data){
		// 		var json = data.data;
		// 		if(json.error != 0){
		// 			console.log(json.message);
		// 			return;
		// 		}
	 //            self.list = json.data;
		// 	})
		// }
	}
})

var userShow = new Vue({
	el:'#userShow',
	data:{
		list:[]
	},
	mounted:function(){
		var url = window.Apis.userShow,
			that = this;
		this.$http.get(url).then(function(data){

			attention.count ++ ;

			var json = data.data;

			if(json.error != 0){
				console.log(json.message);
				return;
			}
            that.list = json.data;
            that.$nextTick(function(){
            	//mui("#userText")[0].innerHTML = json.data.text;
				mui(that.$el).slider({
					interval:6000
				});
				var p = mui('p.mui-ellipsis',that.$el);
				for(var i=0;i<p.length-2;i++){
					p[i+1].innerHTML = json.data[i].text;
				}
				p[p.length-1].innerHTML = json.data[0].text;
				p[0].innerHTML = json.data[p.length-3].text;
            })
        })
	}
})

// var recommend = new Vue({
// 	el:'#recommend',
// 	data:{
// 		list:[]
// 	},
// 	mounted:function(){
// 		var url = window.Apis.recommend,
// 			that = this;
// 		this.$http.get(url).then(function(data){
// 			var json = data.data;
// 			if(json.error != 0){
// 				console.log(json.message);
// 				return;
// 			}
//             that.list = json.data;
//             that.$nextTick(function(){
// 				tongji.recommend();
//             })
//         })
// 	}
// })

var card = new Vue({
	el:'#card',
	data:{
		list:[]
	},
	mounted:function(){
		var url = window.Apis.card,
			that = this;
		this.$http.get(url).then(function(data){

			attention.count ++ ;

			var json = data.data;
			if(json.error != 0){
				console.log(json.message);
				reutrn;
			}
            that.list = json.data;
        })
	}
})

var info = new Vue({
	el:'#info',
	data:{
		list:[]
	},
	mounted:function(){
		var url = window.Apis.info,
			that = this;
		this.$http.get(url).then(function(data){

			attention.count ++ ;

			var json = data.data;
			if(json.error != 0){
				console.log(json.message);
				reutrn;
			}
            that.list = json.data;
        })
	}
})

var money = {
	btn:'#money',
	box:'#moneyBox',
	show:false,
	init:function(){
		var that = this;
		var cash = 50000;
		mui('body').on('tap',that.btn,function(){
			if(that.show){
				that.show = false;
				mui(that.box)[0].style.display = 'none';
			}
			else{
				that.show = true;
				mui(that.box)[0].style.display = 'block';
			}
		});
	/*	document.getElementById('field-range-input').addEventListener('input',function(){
	        document.getElementById('field-range').value = this.value;
	        cash = this.value;
	    });*/
	/*    document.getElementById('field-range').addEventListener('input',function(){
            document.getElementById('field-range-input').value = this.value;
            cash = this.value;
        });*/
        mui('body').on('tap','#moneyBox .btn',function(){
        	var url = '/home/ajax/tongji?action_id=10&record_id=' + cash;
        	Vue.prototype.$http.post(url);
        	document.location.href = ' /home/product/index?cash=' + cash;
        });
	}
}
money.init();

var tongji = {
	url:'/ajax/tongji',
	init:function(){
		var that = this;
		var pra = {
			action_id:4,
			record_id:0
		};
		Vue.prototype.$http.post(that.url,pra);
		this.people();
	},
	banner:function(){
		var that = this;
		mui('#banner').on('click','.mui-slider-item a',function(){
			var pra = {
				action_id:3
			};
			pra.record_id = this.getAttribute('data-id');
			Vue.prototype.$http.post(that.url,pra)
		});
	},
	people:function(){
		var that = this;
		mui('#people').on('click','li a',function(){
			var pra = {
				action_id:1
			};
			pra.record_id = this.getAttribute('data-id');
			Vue.prototype.$http.post(that.url,pra)
		});
	},
	zhuanti:function(){
		var that = this;
		mui('#server').on('click','li a',function(){
			var pra = {
				action_id:1
			};
			pra.record_id = this.getAttribute('data-id');
			Vue.prototype.$http.post(that.url,pra)
		});
	},
	// recommend:function(){
	// 	var that = this;
	// 	mui('#recommend').on('click','li a',function(){
	// 		var pra = {
	// 			action_id:5
	// 		};
	// 		pra.record_id = this.getAttribute('data-id');
	// 		Vue.prototype.$http.post(that.url,pra)
	// 	});
	// }
}
tongji.init();









