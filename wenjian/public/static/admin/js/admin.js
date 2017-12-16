$(function(){

	$('#submit').on('click', function(event) {
		  var url = $(this).data('url');
		  if(!url){
		  	 return;
		  }
		  $.post(url, $("#form").serialize(), function(json) {
		  	    if(json.error==0){
		  	    	layer.msg('操作成功！', {
		  	    		icon: 6,
		  	    		time:1000
		  	    	},function(){
		  	    		window.location.href = json.data;
		  	    	});
		  	    }else{
		  	    	layer.msg(json.message);
		  	    }
		  },'json');
	});


	$('.statusBtn').on('click', function(event) {
		 var url = $(this).data('url'),
		 	 id = $(this).data('id'),
		 	 type = $(this).data('type'),
		 	 confirm = $(this).data('confirm'),
		 	 obj = $(this).data('obj'),
		 	 title = $(this).attr('title');
		 if(!url || url==undefined || !id || id==undefined || !type || type==undefined){
		 	 return;
		 } 
		 if(confirm==1){
		 	title = title?title:'确定该操作吗?';
		 	layer.confirm(title, {
		 		title:false,
			  	btn: ['确定','取消'] 
			}, function(){
				$.post(url, {'cmd': type,'id':id,'obj':obj}, function(json) {
				 	 if(json.error==0){
						 success('操作成功');
				 	 }else{
				 	 	 layer.msg(json.message);
				 	 }
				},'json');
			});
		 }else{
		 	$.post(url, {'cmd': type,'id':id,'obj':obj}, function(json) {
			 	if(json.error==0){
					 success('操作成功');
			 	 }else{
			 	 	 layer.msg(json.message);
			 	 }
			},'json');
		 }  
	});

	$(".export").on('click', function(event) {
		event.preventDefault();
		var url = $(this).attr('url');
		window.location.href = url;
	});

});



//选择跳转
function eventSelect(id, url) {
	var type = 1;
	if(!url || url==undefined){
		url = window.location.href;
		type = 2;
	} 
	var name;
	if(id.indexOf('#')==-1){ //没有#
		name = id;
		id = '#'+id; 
	}else{
		name = id.replace('#','');
	}
    $(id).change(function () {
        var val = $(this).val(); 
        if(url.indexOf('?')>0){
    		url+='&'+name+'='+val;
    	}else{
    		url+='?'+name+'='+val;
    	}
        window.location.href = url;
    });
}


function success(msg,url){
	url = url?url:'';
	layer.msg(msg, {
		icon: 1,
		time:1000
	},function(){
		if(url){
			window.location.href=url;
		}else{
			window.location.reload();
		}
	});
}