
var banner = new Vue({
	el: '#banner',
	data:{
		pics:[]
	},
	mounted:function(){
		var url = window.Apis.recommend_banner,
			that = this;
		this.$http.get(url).then(function(data){
			var json = data.data;
			if(json.error != 0){
				console.log(json.message)
				reutrn;
			}
            that.pics = json.data;
            that.$nextTick(function(){
            	mui(that.$el).slider({
					interval:5000
				});
				tongji.banner();
            })
        })

    document.title = '小树Golds'
    // console.log(document.title)
	}
});

var recommend = new Vue({
  el:'#recommend-content',
  data:{
    api:window.Apis.recommend_list,
    page:1,
    list:null,
    nothing:false,
  },
  created:function(){
    var _self = this;
    var _url = this.api + '?page=' + this.page;
    this.$http.get(_url).then(function(data){
      var json = data.data;
      if(json.error != 0){
        console.log(json.message)
        _self.nothing = true;
        return ;
        
      }
      _self.list = json.data;
      _self.page ++;
      if(json.data.length > 0){
        _self.$nextTick(function(){
          mui.init({
            pullRefresh:{
              container:"#recommend-page",
              up:{
                contentrefresh:'正在加载...',
                callback:_self.Refresh
              }
            }
          })
        })
      }else{
        _self.nothing = true;
      }
      
    })
  },
  mounted:function(){
    if(Util.getUrlString('sid')){
      sessionStorage.setItem('recommend_sid',Util.getUrlString('sid'));
    }
  },
  methods:{
    Refresh:function(){
      var _url = this.api + '?page=' + this.page,
          _self = this,
          list = this.list;
      this.$http.get(_url).then(function(data){
        var json = data.data;
        if(json.error != 0){
          if(json.error == 2){
            mui('#recommend-page').pullRefresh().endPullupToRefresh(true);
          }else{
            console.log(json.message);
            return ;
          }
        }else{
          _self.list = list.concat(json.data);
          _self.page ++ ;
          mui('#recommend-page').pullRefresh().endPullupToRefresh(false);
        }
      })
    }
  }
})
var recommend_bottom = new Vue({
  el:'#recommend-bottom',
  data:{
    is_show_bottom:null,
    recommend_bottom:null,
  },
  created:function(){
    var _self = this;
    var _url = window.Apis.recommend_bottom;
    if(Util.getUrlString('sid')){
      _url += '?sid=' + Util.getUrlString('sid')
    }
    this.$http.get(_url).then(function(data){
      var json = data.data;
      if(json.error != 0){
        return ;
      }
      _self.recommend_bottom = json.data;
    });
  },
  mounted:function(){
    var page = document.getElementById('recommend-page');
    removeClass(page, 'hide');
    if(sessionStorage.getItem('is_show_bottom')){
      this.is_show_bottom = sessionStorage.getItem('is_show_bottom')*1;
    }else{
      this.is_show_bottom = true;
    }
  },
  methods:{
    toggle:function(type){
      this[type] = !this[type];
      sessionStorage.setItem('is_show_bottom',0);
    },
  }
})
var tongji = {
	url:'./ajax/tongji',
	init:function(){
		var that = this;
		var pra = {
			action_id:4,
			record_id:0
		};
		Vue.prototype.$http.post(that.url,pra);
		// this.people();
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
	// people:function(){
	// 	var that = this;
	// 	mui('#people').on('click','li a',function(){
	// 		var pra = {
	// 			action_id:1
	// 		};
	// 		pra.record_id = this.getAttribute('data-id');
	// 		Vue.prototype.$http.post(that.url,pra)
	// 	});
	// },
	// zhuanti:function(){
	// 	var that = this;
	// 	mui('#server').on('click','li a',function(){
	// 		var pra = {
	// 			action_id:1
	// 		};
	// 		pra.record_id = this.getAttribute('data-id');
	// 		Vue.prototype.$http.post(that.url,pra)
	// 	});
	// },
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
function hasClass(elem, cls) {
  cls = cls || '';
  if (cls.replace(/\s/g, '').length == 0) return false; //当cls没有参数时，返回false
  return new RegExp(' ' + cls + ' ').test(' ' + elem.className + ' ');
}
function removeClass(elem, cls) {
  if (hasClass(elem, cls)) {
    var newClass = ' ' + elem.className.replace(/[\t\r\n]/g, '') + ' ';
    while (newClass.indexOf(' ' + cls + ' ') >= 0) {
      newClass = newClass.replace(' ' + cls + ' ', ' ');
    }
    elem.className = newClass.replace(/^\s+|\s+$/g, '');
  }
}
