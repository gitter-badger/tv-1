    window.CMP = window.CMP || {};
    CMP.cookie.set("cmp_window_opened", true);
    window.onbeforeunload = window.onunload = function() {
	CMP.cookie.del("cmp_window_opened");
    }
    var cmpo,str; 
    function cmp_loaded(key) {
	cmpo = CMP.get("cmp");
	if (cmpo) {
            cmpo.addEventListener("model_start", "startHandler");
            cmpo.addEventListener("model_state", "stateHandler");
            cmpo.addEventListener("model_time", "timeHandler"); 
            cmpo.addEventListener("model_error", "errorHandler");
            cmpo.addEventListener("lrc_complete", "lrcHandler");
            cmpo.addEventListener("view_stop", "viewstopHandler");
	    document.title = cmpo.config("name");
	    cmpo.addEventListener("model_load", "cmp_model_load");
	}else {
	    alert("CMP无法初始化");
	}
    } 
    function startHandler(data) {
        cmpo = CMP.get("cmp");    
	if (cmpo) {
            showMsg("欢迎光临" + cmpo.config("name")+ "  -  " + cmpo.config("description")+ "  正在播放："+cmpo.item("label"));
            var arr = [];	
            arr.push('<b>您正在播放：'+cmpo.item("label")+' - <a href="'+cmpo.item("src")+'" target="_blank">查看节目源</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;qq2236639958}</b>');                
            var cmpinfo = arr.join("<br />");
            document.getElementById("cmpinfo").innerHTML = cmpinfo	;	
	}	
    }
    function stateHandler(data) {
        if (data == "playing") showMsg("正在播放 " + cmpo.item("label")+ " - " + cmpo.config("name"));
        if (data == "paused")  showMsg("暂停播放 " + cmpo.item("label")+ " - " + cmpo.config("name"));
        if (data == "buffering") showMsg("正在缓冲 " + cmpo.item("label")+ " - " + cmpo.config("name"));
        if (data == "viewstop") showMsg("停止播放 " + cmpo.item("label")+ " - " + cmpo.config("name"));
    }
    function errorHandler() {
        showMsg("友情提示：未找到节目源,请联系QQ客服处理，谢谢！！  " + cmpo.config("name")+ "  -  " + cmpo.config("description"));
        var arr = [];	
        arr.push('<b>友情提示：未找到节目源,请联系QQ客服处理，谢谢！！&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;qq2236639958</b>');                
        var cmpinfo = arr.join("<br />");
        document.getElementById("cmpinfo").innerHTML = cmpinfo	;
    }
    function lrcHandler(data) { 
        var lrc = cmpo.item("lrc");
        if (!lrc||!lrc.length) {
            showMsg("友情提示：没有找到歌词,请联系QQ客服处理，谢谢！！" + cmpo.config("name")+ "  -  " + cmpo.config("description"), true);
	        var arr = [];
	        arr.push('<b>歌曲地址:</b> <a href="'+cmpo.item("src")+'" target="_blank">'+cmpo.item("src")+'</a>');
            arr.push('<b>友情提示：如果你播放的音乐没有找到歌词,请联系QQ客服处理，谢谢！！&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;qq2236639958</b>');
 	        var cmpinfo = arr.join("<br />");
	        document.getElementById("cmpinfo").innerHTML = cmpinfo; 
        }else{
            showMsg("成功找到歌词，自动显示到歌词窗口", true);
	        var arr = [];		
	        arr.push('<b>节目地址:</b> <a href="'+cmpo.item("src")+'" target="_blank">'+cmpo.item("src")+'</a>&nbsp;&nbsp;&nbsp;&nbsp;<b>歌词地址:</b> <a href="'+cmpo.item("lrc")+'" target="_blank">'+cmpo.item("lrc")+'</a>');                         
 	        var cmpinfo = arr.join("<br />");
	        document.getElementById("cmpinfo").innerHTML = cmpinfo	;  
        }
    }
    function viewstopHandler(data) {
        showMsg("非常感谢您点播本节目,欢迎再次光临，谢谢！！  " + cmpo.config("name")+ "  -  " + cmpo.config("description"));
        var arr = [];	
        arr.push('<b>非常感谢您点播本节目,欢迎再次光临，谢谢！！&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;qq2236639958</b>');                
        var cmpinfo = arr.join("<br />");
        document.getElementById("cmpinfo").innerHTML = cmpinfo	;
    }    
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
        showMsg(cmpo.config("name")+ "  -  " + cmpo.config("description"));
    } 
    function timeHandler() {	
        var start_bytes = window.cmpo.item("start_bytes");	
        window.cmpo.cookie("start_bytes", start_bytes);
    }  
    function QueryString(){
        var name,value,i;
        var str=location.href;
        var num=str.indexOf("?")
        str=str.substr(num+1);
        var arrtmp=str.split("&");
        for(i=0;i < arrtmp.length;i++){
           num=arrtmp[i].indexOf("=");
	   if(num>0){
		name=arrtmp[i].substring(0,num);
		value=arrtmp[i].substr(num+1);
		this[name]=value;
	    }
       }
    }
    function cmp_model_load(data) {
	document.title = cmpo.item("label");
    }