<?php

return [
    'job'   => [
	    ['title'=>'拉沟网首页信息',	'url'=>'https://m.lagou.com/search.json?city=%E6%B7%B1%E5%9C%B3&positionName=web&pageNo={$api}&pageSize=15'],
		['title'=>'得到工作岗位的详细信息',	'url'=>'https://m.lagou.com/jobs/{$api}.html'],
	],
	'joke'   => [
	    ['title'=>'笑话接口','url'=>'http://3g.163.com/touch/jsonp/joke/chanListNews/T141931628472/2/{$api}-10.html?callback=data'],
	],
    'douban'   => [
	    ['title'=>'正在上映电影列表','url'=>'http://api.douban.com/v2/movie/in_theaters?start={$api}&count=9'],
		['title'=>'电影详情','url'=>'http://api.douban.com/v2/movie/subject/{$api}'],
	],	
	'kugou'   => [
	    ['title'=>'音乐新歌榜','url'=>'http://m.kugou.com/&json=true'],
		['title'=>'音乐排行榜','url'=>'http://m.kugou.com/rank/list&json=true'],
		['title'=>'排行榜下的音乐列表','url'=>'http://m.kugou.com/rank/info/{$api}&json=true'],		
		['title'=>'歌单','url'=>'http://m.kugou.com/plist/index&json=true'],
		['title'=>'歌单下的列表','url'=>'http://m.kugou.com/rank/list/{$api}&json=true'],
		['title'=>'歌手分类','url'=>'http://m.kugou.com/singer/class&json=true'],
		['title'=>'分类下面的歌手列表','url'=>'http://m.kugou.com/singer/list/{$api}&json=true'],
		['title'=>'歌手信息','url'=>'http://m.kugou.com/singer/info/{$api}&json=true'],
		['title'=>'音乐详情','url'=>'http://m.kugou.com/app/i/getSongInfo.php?cmd=playInfo&hash={$api}'],
		['title'=>'带歌词歌曲信息','url'=>'http://www.kugou.com/yy/index.php?r=play/getdata&hash={$api}'],
		['title'=>'音乐搜索','url'=>'http://mobilecdn.kugou.com/api/v3/search/song?format=json&keyword={$api}&page=1&pagesize=20&showtype=1'],
	],
	'nba'   => [
	    ['title'=>'获取赛事直播列表','url'=>'https://nb.3g.qq.com/nba/api/schedule@getList?md={$api}&sid='],
		['title'=>'比赛直播详情信息','url'=>'https://nb.3g.qq.com/nba/api/live@getInfo?i_schid={$api}&i_liveid={$ipa}'],
	    ['title'=>'直播内容','url'=>'https://live.3g.qq.com/g/s?aid=action_api&module=nba&action=broadcast_content%2Cbroadcast_info&sch_id={$api}'],
	    ['title'=>'球员技术统计','url'=>'https://live.3g.qq.com/g/s?aid=action_api&module=nba&action=live_stat_4_nba%2Cbroadcast_info&sch_id={$api}&bid=2009605'],
	    ['title'=>'球员详情','url'=>'https://live.3g.qq.com/g/s?aid=action_api&module=nba&action=player_detail&playerId={$api}&sid='],
		['title'=>'联盟排名','url'=>'https://matchweb.sports.qq.com/rank/team?columnId=100000&from=NBA'],
	    ['title'=>'球队详情','url'=>'https://live.3g.qq.com/g/s?aid=action_api&module=nba&action=team_detail&teamId={$api}&sid='],
		['title'=>'球队赛程','url'=>'https://nb.3g.qq.com/nba/api/schedule@getMonthListByTeam?teamid={$api}&mouth={$ipa}&sid='],
	    ['title'=>'球队阵容','url'=>'https://live.3g.qq.com/g/s?aid=action_api&module=nba&action=team_player&teamId={$api}&sid='],
		['title'=>'网易NBA新闻列表','url'=>'https://3g.163.com/touch/reconstruct/article/list/BD2AQH4Qwangning/{$api}-15.html'],
	    ['title'=>'网易NBA新闻详情','url'=>'http://3g.163.com/touch/article/{$api}/full.html'],
		['title'=>'网易NBA文章评论','url'=>'https://comment.news.163.com/api/v1/products/a2869674571f77b5a0867c3d71db5856/threads/{$api}/comments/newList?offset=0&limit=20&headLimit=1&tailLimit=2&ibc=newswap&showLevelThreshold'],		
	],
	'163'   => [
	    ['title'=>'api地址','url'=>'http://c.m.163.com/nc/article/headline/list/0-10.html?from=toutiao&passport=&devId'],
		['title'=>'新闻列表','url'=>'http://c.m.163.com/nc/article/headline/{$api}/{$ipa}-10.html'],
	    ['title'=>'新闻详情','url'=>'http://c.m.163.com/nc/article/{$api}/full.html'],
	    ['title'=>'当地新闻列表','url'=>'http://3g.163.com/touch/jsonp/article/local/{$api}/{$ipa}-10.html'],
	    ['title'=>'视频首页接口','url'=>'http://c.3g.163.com/nc/video/home/0-10.html'],
		['title'=>'视频分类列表','url'=>'http://c.m.163.com/nc/video/list/{$api}/y/{$ipa}-10.html'],
		['title'=>'视频详情','url'=>'http://3g.163.com/touch/video/detail/{$api}.html'],
	],	
	'tianqi'   => [
	    ['title'=>'获取城市天气预报','url'=>'http://api.map.baidu.com/telematics/v3/weather?location={$api}&output=json&ak=32da004455c52b48d84a3a484c0dbc99'],
		['title'=>'IP获取城市天气预报','url'=>'http://api.map.baidu.com/location/ip?ak=enYCQ2yaIIjL8IZfYdA1gi6hK2eqqI2T&ip={$api}&coor=bd09ll'],
	   
	],	
	'zhihu'   => [
	    ['title'=>'最新日报列表','url'=>'http://news-at.zhihu.com/api/4/news/latest'],
		['title'=>'知乎日报文章详情','url'=>'http://news-at.zhihu.com/api/4/news/{$api}'],
	    ['title'=>'知乎日报短评论','url'=>'http://news-at.zhihu.com/api/4/story/{$api}/short-comments'],
	],
    
];