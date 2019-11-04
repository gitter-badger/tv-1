import axios from 'axios'
import originJsonp from 'jsonp'

export const BASE = {
	Title: 'VipTV',
	PROURL: process.env.NODE_ENV === 'production' ? 'http://live.miguvideo.com' : '/api',
	VER: '1.0.0',
	ABOUT: 'QQ客服：2236639958'
}

const request = axios.create({
	baseURL: BASE.PROURL, // 设置跨域代理接口统一的前置地址
	timeout: 5000,
	headers: {
		'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
	},
	withCredentials: false
})

request.interceptors.request.use(config => {
	return Promise.resolve(config)
}, err => {
	return Promise.reject(err)
})

request.interceptors.response.use(data => {
	return Promise.resolve(data)
}, err => {
	return Promise.reject(err)
})

export function jsonp(url, data, option) {
	url += (url.indexOf('?') < 0 ? '?' : '&') + param(data);
	console.info('jsonp', url);
	return new Promise((resolve, reject) => {
		originJsonp(url, option, (err, data) => {
			if (!err) {
				resolve(data)
			} else {
				reject(err)
			}
		})
	})
}

export function form_json(json) {
	if (json) {
		let attr = [];
		for (let item of Object.entries(json)) {
			if (item[1]) {
				attr.push(item.join("="));
			}
		}
		attr = attr.join("&");
		return attr;
	}
}

export function param(data) {
	let url = '';
	for (var k in data) {
		let value = data[k] !== undefined ? data[k] : '';
		url += '&' + k + '=' + encodeURIComponent(value);
	}
	return url ? url.substring(1) : '';
}

export async function Migu(idx = '70002091') {

	return request('/live/v2/tv-data/' + idx)

}
