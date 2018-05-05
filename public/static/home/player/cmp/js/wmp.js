/*
 * WMP for CMP
 * http://bbs.cenfun.com/
 *
 * file: http://cenfunmusicplayer.googlecode.com/svn/trunk/js/wmp.js
 */
if(typeof window.WMP==="undefined") {
	var WMP=window.WMP=new (function(){
		var key,cmpo,wmpo,display,wmping;
		this.init=function(_key, _cmpo){
			if (!cmpo) {
				key = _key;
				cmpo = _cmpo;
				cmpo.addEventListener("model_start", "WMP.update");
				cmpo.addEventListener("model_state", "WMP.update");
				cmpo.addEventListener("resize", "WMP.update");
				cmpo.addEventListener("control_fullscreen", "WMP.fullscreen");
			}
		};
		this.update = function(data) {
			display = false;
			wmping = false;
			var item = cmpo.item();
			if (item) {
				if (item.type == "wmp") {
					if (!wmpo) {
						wmpo = $("WMP_" + key);
						if (wmpo) {
							wmpo.uiMode = "None";
							wmpo.fullScreen = false;
							wmpo.stretchToFit = true;
							wmpo.enableContextMenu = true;
							wmpo.style.top = "0px";
							wmpo.style.left = "0px";
							wmpo.style.position = "absolute";
							cmpo.parentNode.appendChild(wmpo);
						}
					}
					var state = cmpo.config("state");
					if (state == "playing") {
						wmping = true;
						var is_show = cmpo.skin("media", "display");
						if (is_show) {
							display = true;		
						}
					}
				}
			}
			wmpo.style.visibility = display ? "visible" : "hidden";
			if (display) {
				var tx = 0;
				var ty = 0;
				if (!cmpo.config("video_max")) {
					tx = parseInt(cmpo.skin("media", "x")) + parseInt(cmpo.skin("media.video", "x"));
					ty = parseInt(cmpo.skin("media", "y")) + parseInt(cmpo.skin("media.video", "y"));
				}
				var tw = cmpo.config("video_width");
				var th = cmpo.config("video_height");
				wmpo.style.top = ty + "px";
				wmpo.style.left = tx + "px";
				wmpo.width = tw;
				wmpo.height = th;
			}
		}
		this.fullscreen = function(data) {
			var item = cmpo.item();
			if (item.type == "wmp" && wmping) {
				var fullscreen = cmpo.config("fullscreen");
				if (fullscreen) {
					cmpo.sendEvent("view_fullscreen");
					wmpo.fullScreen = true;
				}
			}
		}
	})();
}