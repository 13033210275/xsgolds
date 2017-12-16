var Detail = new Vue({
	el: '#Detail',
	data:{
		api:window.Apis.detail,
		id:proId,
		data:false,
		cashArr:[],
		dateArr:[]
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
            var cashArr = [],
            	dateArr = [];
            if(json.data.cash_step>0){
	            for(var i=json.data.min_cash;i<json.data.max_cash+json.data.cash_step;i+=json.data.cash_step){
	            	var num = i>json.data.max_cash ? json.data.max_cash:i;
	            	that.cashArr.push(num);
	            }
	        }
	        if(json.data.date_step>0){
	            for(var j=json.data.min_date;j<json.data.max_date+json.data.date_step;j+=json.data.date_step){
	            	var num = j>json.data.max_cash ? json.data.max_cash:j;
	            	that.dateArr.push(num);
	            }
	        }
        })
	}
});