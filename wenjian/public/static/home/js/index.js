/******我要取钱********/
//监听input事件，获取range的value值，也可以直接element.value获取该range的值
var rangeList = document.querySelectorAll('input[type="range"]');
for(var i = 0, len = rangeList.length; i < len; i++) {
	rangeList[i].addEventListener('input', function() {
		if(this.id.indexOf('field') >= 0) {
			document.getElementById(this.id + '-input').value = this.value;
		} else {
			document.getElementById(this.id + '-val').innerHTML = this.value;
		}
	});
}



/***小树头条上下滚动****/
var oMarquee = document.getElementById("xs_time"); //滚动对象 
var iLineHeight = 20; //单行高度，像素 
var iLineCount = 5; //实际行数 
var iScrollAmount = 2; //每次滚动高度，像素 
function run() {
	oMarquee.scrollTop += iScrollAmount;
	if(oMarquee.scrollTop == iLineCount * iLineHeight) oMarquee.scrollTop = 0;
	if(oMarquee.scrollTop % iLineHeight == 0) {
		window.setTimeout("run()", 5000);
	} else {
		window.setTimeout("run()", 80);
	}
}
oMarquee.innerHTML += oMarquee.innerHTML;
window.setTimeout("run()", 5000);