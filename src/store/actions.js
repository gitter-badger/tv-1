import {
	Migu
} from '@/api'
export default {
	async migutv_data(context, migutvId) {
		return Migu(migutvId).then(res => {
			if (res.data.code === 200) {
				context.commit('migutv_nav', res.data.body.liveList)
				context.commit('migutv_cont', res.data.body.dataList)
			}
		})
	}
}
