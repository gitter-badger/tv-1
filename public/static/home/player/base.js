  
        var isiPad = navigator.userAgent.match(/iPad|iPhone|Mac|mac|ios|IOS|Linux|Android|iPod/i) != null;
        var elementLogin = null; 
        var loginOrNo = false; 
        var loginShow = false; 
        var hls=getUrlParam('hls');
        var Playlist = new Array();
        var box = new LightBox();
        var player=null;
        var cmpo,title,player,cmp_xml,ext,nowD = 0;
        if(getUrlParam('ext')){
            ext = getUrlParam('ext');
        }else{
            ext = getUrlParam('base').split('.').pop().toLowerCase().substring(0,4) ;
        } 

    function ckp(Url){
        var cookie = {
            set: function(name, value) {
                var Days = 30;
                var exp = new Date();
                exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
                document.cookie = name + '=' + escape(value) + ';expires=' + exp.toGMTString();
            },
            get: function(name) {
                var arr, reg = new RegExp('(^| )' + name + '=([^;]*)(;|$)');
                if(arr = document.cookie.match(reg)) {
                    return unescape(arr[2]);
                } else {
                    return null;
                }
            },
            del: function(name) {
                var exp = new Date();
                exp.setTime(exp.getTime() - 1);
                var cval = getCookie(name);
                if(cval != null) {
                    document.cookie = name + '=' + cval + ';expires=' + exp.toGMTString();
                }
            }
        };
        var videoID = 10; 
        var cookieTime = cookie.get('time_' + videoID); 
        //console.log(cookieTime);
        if(!cookieTime || cookieTime == undefined) { 
            cookieTime = 0;
        }
        if(cookieTime > 0) {
            alert('本视频记录的上次观看时间(秒)为：' + cookieTime);
        }       
        var videoObject = {
            container: '#a1', 
            variable: 'player', 
            loop: '', 
            autoplay: true, 
            loaded:'loadedHandler',
            drag: 'start', 
            seek: 0, 
            front: 'frontFun', 
            next:'nextFun',
            mobileCkControls:true,			
            video: Url
        };
        if(cookieTime > 0) { 
            videoObject['seek'] = cookieTime;
        }   
        var player = new ckplayer(videoObject);
        
    }
    function chp(Url) { 
        if(isiPad){
            $('#a1').html('<video src="'+Url+'" controls="controls" autoplay="autoplay" preload="preload" poster="loading_wap.gif" width="100%" height="100%"></video>');
        }else{
            var cvideoObject = {        
                container: '#a1', 
                variable: 'player',
                volume: 3, 
                seek: 0, 
                front: 'frontFun', 
                next: 'nextFun', 
                autoplay: true, 
                flashplayer: true,
                drag: 'start',
                video: Url
            };  
            if(hls!=null){
                videoObject['html5m3u8']=true;
            }
            var player = new chplayer(cvideoObject);
        }   
    }
    function Dp(Url) {
        if(isiPad){
            $('#a1').html('<video src="'+video+'" controls="controls" autoplay="autoplay" width="100%" height="100%" style="psotion:relative;""></video>');
        }else{
            var dp = new DPlayer({
                container: document.getElementById('a1'),
                screenshot: true,
                video: {
                    url: video,
                    pic: 'demo.jpg',
                    thumbnails: 'thumbnails.jpg'
                },
                subtitle: {
                    url: 'webvtt.vtt'
                },
                danmaku: {
                    id: 'demo',
                    api: 'https://api.prprpr.me/dplayer/'
                }
            });
        }
    }
    function cmp(Url) {
        if(isiPad){
            $('#a1').html('<video src="'+video+'" controls="controls" autoplay="autoplay" width="100%" height="100%" style="psotion:relative;""></video>');
        }else{
            var flashvars = { 
                name : title,
                link : "{$url}",            
                logo : "bg/logo.png",
                list : Url,
                skins : "skins/imgo.zip",
                models : "plugins/HLS.swf",
                backgrounds : "{src:bg/ads.swf,xywh:[0R,0R,100P,100P]}",
                play_id : "1",
                auto_play : "1",
                click_play : "1",
                api : "cmp_loaded"
            };
            document.getElementById("a1").innerHTML = CMP.create("cmp", "100%", "100%",  "/public/static/home/player/cmp/cmp_2017.swf", flashvars,{wmode:"opaque"});
        }
    }
    function jwp(Url) {
        if(isiPad){
            $('#a1').html('<video src="'+video+'" controls="controls" autoplay="autoplay" width="100%" height="100%" style="psotion:relative;""></video>');
        }else{
            jwplayer("a1").setup({
                playlist: [{
                    file: url,
                    provider: '/public/static/home/player/jwp/HLSProvider6.swf',
                    type: 'mp4',
                    title: title
                }],
                flashplayer: '/public/static/home/player/jwp/jwplayer.swf?t='+new Date().getTime(),
                width: '100%',
                height: '100%',
                primary: "flash",
                autostart: true
            });
        }   
    }
    function hide(){
        if($('#right').width()==300){
            $('#right').css({'width':20}).css('float','right').css('marginLeft','-20px');
            $('#listcontent').hide();
            $('#shows').show();
            $('#hide').hide();
            $('#play').css({'width':$('.videobox').width()-$('#right').width()+"px"});
        }else{
            $('#right').css({'width':300}).css('float','left').css('marginLeft','-300px');
            $('#listcontent').show();
            $('#shows').hide();
            $('#hide').show();
            $('#play').css({'width':$('.videobox').width()-$('#right').width()+"px"});
        }
    }
    function main(id) {
        if (id == "1") {
            $('#ul1').show();
            $('#ul2').hide();
            $('#ul3').hide();
            $('#ul4').hide();
            $('#main1 i').addClass("dz");
            $('#main2 i').removeClass("dz");
            $('#main3 i').removeClass("dz");
            $('#main4 i').removeClass("dz");
            $('#main1').css('border-color','#00C300');
            $('#main2').css('border-color','#353535');
            $('#main3').css('border-color','#353535');
            $('#main4').css('border-color','#353535');
        } else if (id == "2") {
            $('#ul1').hide();
            $('#ul2').show();
            $('#ul3').hide();
            $('#ul4').hide();
            $('#main1 i').removeClass("dz");
            $('#main2 i').addClass("dz");
            $('#main3 i').removeClass("dz");
            $('#main4 i').removeClass("dz");
            $('#main1').css('border-color','#353535');
            $('#main2').css('border-color','#00C300');
            $('#main3').css('border-color','#353535');
            $('#main4').css('border-color','#353535');
        } else if (id == "3") {
            $('#ul1').hide();
            $('#ul2').hide();
            $('#ul3').show();
            $('#ul4').hide();
            $('#main1 i').removeClass("dz");
            $('#main2 i').removeClass("dz");
            $('#main3 i').addClass("dz");
            $('#main4 i').removeClass("dz");
            $('#main1').css('border-color','#353535');
            $('#main2').css('border-color','#353535');
            $('#main3').css('border-color','#00C300');
            $('#main4').css('border-color','#353535');
        } else if (id == "4") {
            $('#ul1').hide();
            $('#ul2').hide();
            $('#ul3').hide();
            $('#ul4').show();
            $('#main1 i').removeClass("dz");
            $('#main2 i').removeClass("dz");
            $('#main3 i').removeClass("dz");
            $('#main4 i').addClass("dz");
            $('#main1').css('border-color','#353535');
            $('#main2').css('border-color','#353535');
            $('#main3').css('border-color','#353535');
            $('#main4').css('border-color','#00C300');
        }
    }  
    function cmp_loaded(key) {
        cmpo = CMP.get("cmp");
        if (cmpo) {
            document.title = cmpo.config("name");
            cmpo.addEventListener("model_load", "cmp_model_load");
            cmpo.addEventListener("list_change", "cmp_list_change");
            cmpo.addEventListener("model_complete", "cmp_model_complete");
            cmpo.addEventListener("model_error", "cmp_model_error");
            cmpo.addEventListener("model_state", "cmp_model_state");
            cmpo.addEventListener("lrc_rowchange", "cmp_lrc_rowchange");
            cmpo.addEventListener("control_next", "cmp_control_next");
            cmpo.addEventListener("control_play", "cmp_control_play");
            cmpo.addEventListener("control_pause", "cmp_control_pause");
            cmpo.addEventListener("control_prev", "cmp_control_prev");
            cmpo.addEventListener("view_random", "cmp_view_random");
            cmpo.addEventListener("view_repeat", "cmp_view_repeat");
            cmpo.addEventListener("view_more", "cmp_view_more");
        }
    }   
    function getUrlParam(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); 
        var r = window.location.search.substr(1).match(reg); //匹配目标参数
        if(r != null) return unescape(r[2]);
        return null; //返回参数值
    }
    function loadedHandler() { //播放器加载后会调用该函数
        player.addListener('time', timeHandler); //监听播放时间
        player.addListener('play', playHandler); //监听播放状态
        player.addListener('full', fullHandler); //监听全屏切换
    }
    function frontFun() {
        nowD--;
        if (nowD >= Playlist.length ) {
            nowD = 0;
        }
        player(nowD);
    }
    function nextFun() {
        nowD++;
        if (nowD >= Playlist.length ) {
            nowD = 0;
        }
        player(nowD);   
    }
    function playHandler() { //监听播放状态
        if(loginShow) {
            player.videoPause();
        }
    } 
    function fullHandler(b) { //监听全屏切换
        if(loginShow && elementLogin) {
            player.deleteElement(elementLogin);
            elementLogin = null;
            window.setTimeout('showLogin()', 200);
        }
    } 
    function timeHandler(t) { //监听播放时间
        cookie.set('time_' + videoID, t); //当前视频播放时间写入cookie
        if(t >= 10 && !loginOrNo) { //如果播放时间大于1，则又没有登录，则弹出登录/注册层
            player.videoPause();
            if(!loginShow && !elementLogin) {
                showLogin();
            }
        }
    } 
    function showLogin() { //显示登录/注册层
        loginShow = true;
        var meta = player.getMetaDate();
        var x = (meta['width'] - 307) * 0.5;
        var y = (meta['height'] - 39) * 0.5 - 80;
        var attribute = {
            list: [ //list=定义元素列表
                {
                    type: 'image', //定义元素类型：只有二种类型，image=使用图片，text=文本
                    file: 'pic/login/login_01.png', //图片地址
                    radius: 0, //图片圆角弧度
                    width: 140, //定义图片宽，必需要定义
                    height: 39, //定义图片高，必需要定义
                    alpha: 1, //图片透明度(0-1)
                    marginLeft: 0, //图片离左边的距离
                    marginRight: 0, //图片离右边的距离
                    marginTop: 0, //图片离上边的距离
                    marginBottom: 0 //图片离下边的距离
                },
                {
                    type: 'image', //定义元素类型：只有二种类型，image=使用图片，text=文本
                    file: 'http://www.ckplayer.com/sampleX/pic/login/login_02.png', //图片地址
                    radius: 0, //图片圆角弧度
                    width: 69, //定义图片宽，必需要定义
                    height: 39, //定义图片高，必需要定义
                    alpha: 1, //图片透明度(0-1)
                    marginLeft: 0, //图片离左边的距离
                    marginRight: 0, //图片离右边的距离
                    marginTop: 0, //图片离上边的距离
                    marginBottom: 0, //图片离下边的距离
                    clickEvent: 'javaScript->userLogin()'
                },
                {
                    type: 'image', //定义元素类型：只有二种类型，image=使用图片，text=文本
                    file: 'http://www.ckplayer.com/sampleX/pic/login/login_03.png', //图片地址
                    radius: 0, //图片圆角弧度
                    width: 70, //定义图片宽，必需要定义
                    height: 39, //定义图片高，必需要定义
                    alpha: 1, //图片透明度(0-1)
                    marginLeft: 0, //图片离左边的距离
                    marginRight: 0, //图片离右边的距离
                    marginTop: 0, //图片离上边的距离
                    marginBottom: 0, //图片离下边的距离
                    clickEvent: 'javaScript->userReg()'
                },
                {
                    type: 'image', //定义元素类型：只有二种类型，image=使用图片，text=文本
                    file: 'http://www.ckplayer.com/sampleX/pic/login/login_04.png', //图片地址
                    radius: 0, //图片圆角弧度
                    width: 28, //定义图片宽，必需要定义
                    height: 39, //定义图片高，必需要定义
                    alpha: 1, //图片透明度(0-1)
                    marginLeft: 0, //图片离左边的距离
                    marginRight: 0, //图片离右边的距离
                    marginTop: 0, //图片离上边的距离
                    marginBottom: 0 //图片离下边的距离
                }
            ],
            x: x, //元件x轴坐标，注意，如果定义了position就没有必要定义x,y的值了，支持数字和百分比
            y: y, //元件y轴坐标
            alpha: 1, //元件的透明度
            backgroundColor: '0x000000', //元件的背景色
            backAlpha: 0.1, //元件的背景透明度(0-1)
            backRadius: 0 //元件的背景圆角弧度
        }
        elementLogin = player.addElement(attribute);
    } 
    function userLogin(){
        alert('点击了登录按钮');
    }
    function userReg(){
        alert('点击了注册按钮');
    }
    function loginTrue() { //附加的处理用户登录后执行的动作
        loginOrNo = true;
        if(loginShow && elementLogin) {
            player.deleteElement(elementLogin);
            elementLogin = null;
            loginShow = false;
            player.videoPlay();
        }
    }