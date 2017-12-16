var Detail = new Vue({
	el: '#Detail',
	data:{
		api:window.Apis.detail,
		id:proId,
		data:false
	},
	methods:{
		tongji:function(e){
			var url = 'ajax/tongji?action_id=9&record_id=' + this.id;
			this.$http.post(url);
			var href = e.target.getAttribute("data-href");
			document.location.href = href;
		}
	},
	mounted:function(){
		var url = this.api,
			that = this;
		var pra = {
			id:that.id
		}
        this.$http.post(url,pra).then(function(data){
			var json = data.data
			if(json.error != 0){
				console.log(json.message)
				return;
			}
            that.data = json.data;
        })
	}
});
(function(){
	var url = 'ajax/tongji?action_id=7&record_id=' + proId;
    Vue.prototype.$http.post(url);
})();