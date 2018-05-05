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
	    alert("CMP�޷���ʼ��");
	}
    } 
    function startHandler(data) {
        cmpo = CMP.get("cmp");    
	if (cmpo) {
            showMsg("��ӭ����" + cmpo.config("name")+ "  -  " + cmpo.config("description")+ "  ���ڲ��ţ�"+cmpo.item("label"));
            var arr = [];	
            arr.push('<b>�����ڲ��ţ�'+cmpo.item("label")+' - <a href="'+cmpo.item("src")+'" target="_blank">�鿴��ĿԴ</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;qq2236639958}</b>');                
            var cmpinfo = arr.join("<br />");
            document.getElementById("cmpinfo").innerHTML = cmpinfo	;	
	}	
    }
    function stateHandler(data) {
        if (data == "playing") showMsg("���ڲ��� " + cmpo.item("label")+ " - " + cmpo.config("name"));
        if (data == "paused")  showMsg("��ͣ���� " + cmpo.item("label")+ " - " + cmpo.config("name"));
        if (data == "buffering") showMsg("���ڻ��� " + cmpo.item("label")+ " - " + cmpo.config("name"));
        if (data == "viewstop") showMsg("ֹͣ���� " + cmpo.item("label")+ " - " + cmpo.config("name"));
    }
    function errorHandler() {
        showMsg("������ʾ��δ�ҵ���ĿԴ,����ϵQQ�ͷ�����лл����  " + cmpo.config("name")+ "  -  " + cmpo.config("description"));
        var arr = [];	
        arr.push('<b>������ʾ��δ�ҵ���ĿԴ,����ϵQQ�ͷ�����лл����&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;qq2236639958</b>');                
        var cmpinfo = arr.join("<br />");
        document.getElementById("cmpinfo").innerHTML = cmpinfo	;
    }
    function lrcHandler(data) { 
        var lrc = cmpo.item("lrc");
        if (!lrc||!lrc.length) {
            showMsg("������ʾ��û���ҵ����,����ϵQQ�ͷ�����лл����" + cmpo.config("name")+ "  -  " + cmpo.config("description"), true);
	        var arr = [];
	        arr.push('<b>������ַ:</b> <a href="'+cmpo.item("src")+'" target="_blank">'+cmpo.item("src")+'</a>');
            arr.push('<b>������ʾ������㲥�ŵ�����û���ҵ����,����ϵQQ�ͷ�����лл����&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;qq2236639958</b>');
 	        var cmpinfo = arr.join("<br />");
	        document.getElementById("cmpinfo").innerHTML = cmpinfo; 
        }else{
            showMsg("�ɹ��ҵ���ʣ��Զ���ʾ����ʴ���", true);
	        var arr = [];		
	        arr.push('<b>��Ŀ��ַ:</b> <a href="'+cmpo.item("src")+'" target="_blank">'+cmpo.item("src")+'</a>&nbsp;&nbsp;&nbsp;&nbsp;<b>��ʵ�ַ:</b> <a href="'+cmpo.item("lrc")+'" target="_blank">'+cmpo.item("lrc")+'</a>');                         
 	        var cmpinfo = arr.join("<br />");
	        document.getElementById("cmpinfo").innerHTML = cmpinfo	;  
        }
    }
    function viewstopHandler(data) {
        showMsg("�ǳ���л���㲥����Ŀ,��ӭ�ٴι��٣�лл����  " + cmpo.config("name")+ "  -  " + cmpo.config("description"));
        var arr = [];	
        arr.push('<b>�ǳ���л���㲥����Ŀ,��ӭ�ٴι��٣�лл����&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;qq2236639958</b>');                
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