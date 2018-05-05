function startHandler(data) {
     login();//检测会员是否登陆，如果没有，则显示缓冲广告，VIP节目不能播放！
}
function stateHandler(data) {
     if (data == "cmping") showMsg("正在播放 " + cmpo.item("label")+ " - " + cmpo.config("name"));
     if (data == "paused")  showMsg("暂停播放 " + cmpo.item("label")+ " - " + cmpo.config("name"));
     if (data == "buffering") showMsg("正在缓冲 " + cmpo.item("label")+ " - " + cmpo.config("name"));
     if (data == "viewstop") showMsg("停止播放 " + cmpo.item("label")+ " - " + cmpo.config("name"));
}
function login() {
     showMsg("友情提示：VIP节目请登陆,谢谢！！  {:C('name')}  -  {:C('description')}");    
}
//播放错误时
function errorHandler() {
     showMsg("友情提示：未找到节目源,请联系QQ客服处理，谢谢！！  {:C('name')}  -  {:C('description')}");
     var arr = [];	
     arr.push('<b>友情提示：未找到节目源,请联系QQ客服处理，谢谢！！&nbsp;&nbsp;&nbsp;&nbsp;{:C('qq_kefu')}&nbsp;&nbsp;&nbsp;&nbsp;{:C('qq_qun')}</b>');                
     var cmpinfo = arr.join("<br />");
     document.getElementById("cmpinfo").innerHTML = cmpinfo	;
}
function lrcHandler(data) { 
     var lrc = cmpo.item("lrc");
     if (!lrc||!lrc.length) {
           showMsg("友情提示：没有找到歌词,请联系QQ客服处理，谢谢！！{:C('name')}  -  {:C('description')}", true);
	   var arr = [];
	   arr.push('<b>节目地址:</b> <a href="'+cmpo.item("src")+'" target="_blank">'+cmpo.item("src")+'</a>');
           arr.push('<b>友情提示：如果你播放的音乐没有找到歌词,请联系QQ客服处理，谢谢！！&nbsp;&nbsp;&nbsp;&nbsp;{:C('qq_kefu')}&nbsp;&nbsp;&nbsp;&nbsp;{:C('qq_qun')}</b>');
 	   var cmpinfo = arr.join("<br />");
	   document.getElementById("cmpinfo").innerHTML = cmpinfo; 
     }else{
           showMsg("成功找到歌词，自动显示到歌词窗口", true);
	   var arr = [];		
	   arr.push('<b>节目地址:</b> <a href="'+cmpo.item("src")+'" target="_blank">'+cmpo.item("src")+'</a>');
           arr.push('<b>歌词地址:</b> <a href="'+cmpo.item("lrc")+'" target="_blank">'+cmpo.item("lrc")+'</a>');                 
 	   var cmpinfo = arr.join("<br />");
	   document.getElementById("cmpinfo").innerHTML = cmpinfo	;  
     }
}
function viewstopHandler(data) {
    showMsg("非常感谢您点播本节目,欢迎再次光临，谢谢！！  {:C('name')}  -  {:C('description')}");
    var arr = [];	
    arr.push('<b>非常感谢您点播本节目,欢迎再次光临，谢谢！！&nbsp;&nbsp;&nbsp;&nbsp;{:C('qq_kefu')}&nbsp;&nbsp;&nbsp;&nbsp;{:C('qq_qun')}</b>');                
    var cmpinfo = arr.join("<br />");
    document.getElementById("cmpinfo").innerHTML = cmpinfo	;
}
var tid;
function showMsg(str, red) {
   if (str) {
	document.title =str;                
        $(".message").html(str);
        $(".lrc_content").html(str);
		if (red) {
			$(".message").addClass("red");
		} else {
			$(".message").removeClass("red");
		}
	clearTimeout(tid);
	tid = setTimeout(hideMsg, 8000);
    }
}
function hideMsg() {
    showMsg("{:C('name')} - {:C('description')}");
}
function timeHandler() {	
    var start_bytes = window.cmpo.item("start_bytes");	
    window.cmpo.cookie("start_bytes", start_bytes);
}