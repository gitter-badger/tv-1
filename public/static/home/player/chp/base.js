function chp(VideoUrl) {
	var videoObject = {        
		container: '#a1', //容器的ID,如果获到容器定义的是ID则需要增加#，如果是class，则不需要添加或添加.
		variable: 'player',//调用函数名称，该属性主要用于flashplayer发送监听内容时使用，如call('player.time',10)
		volume: 3, //默认音量，范围是0-1
		seek: 0, //默认需要跳转的时间
		loaded: 'loadedHandler',
		autoplay: true, //是否自动播放，默认true=自动播放，false=默认暂停状态
		drag: 'start',
		video: VideoUrl//视频地址数组。视频地址可以分多种形式，具体在下面会有详细介绍
	}; 
	var player = new chplayer(videoObject);//创建一个播放器函数并附给player变量，（player需要和 var videoObject里的属性variable相同） 
	if(player.playerType=='html5video'){
		if(player.getFileExt(videoUrl)=='.flv' || player.getFileExt(videoUrl)=='.m3u8' || player.getFileExt(videoUrl)=='.f4v' || videoUrl.substr(0,4)=='rtmp'){
			player.removeChild();                
			player=null;
			player = new chplayer();
			player.embed(newVideoObject);
		}else{
			player.newVideo(newVideoObject);
		}
	}else{
		if(player.getFileExt(videoUrl)=='.mp4' || player.getFileExt(videoUrl)=='.webm' || player.getFileExt(videoUrl)=='.ogg'){
			player=null;
			player = new chplayer();
			player.embed(newVideoObject);
		}else{
			player.newVideo(newVideoObject);
		}
	}
}
function getUrlParam(name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
	var r = window.location.search.substr(1).match(reg); //匹配目标参数
	if(r != null) return unescape(r[2]);
	return null; //返回参数值
}