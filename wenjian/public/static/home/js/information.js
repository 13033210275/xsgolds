 new Vue({
    el: '#getProduct',
    data:{
        list:[],
        nothing:false,
        api:window.Apis.info,
        page:1,
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
                        mui('#products').pullRefresh().endPullupToRefresh(true);
                    }
                    else{
                        console.log(json.message)
                        reutrn;
                    }
                }
                else{
                    that.list = list.concat(json.data);
                    that.page++;
                    mui('#products').pullRefresh().endPullupToRefresh(false);
                }
            })
        },
    },
    mounted:function(){
        var that = this, url = this.api ;
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
                            container: '#products',
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





