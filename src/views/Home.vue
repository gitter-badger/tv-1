<template>
	<el-container>
		<el-header></el-header>
		<el-container style=" border: 1px solid #eee">
			<el-aside width="20%" style="background-color: rgb(238, 241, 246)">
				<el-menu v-for="(x, y) in migutvNav" :key="y">
					<el-menu-item-group>
						<el-menu-item @click="say(x.vomsID)">{{ x.name }}</el-menu-item>
					</el-menu-item-group>
				</el-menu>
			</el-aside>
			<el-main v-if="reseted">
				<el-row>
					<el-col :span="8" v-for="(o, index) in migutvCont" :key="index">
						<router-link  :to="{path:'/play/'+o.pID}" >
						<el-card :body-style="{ padding: '0px' }" >
							<img v-bind:src="o.h5pics.lowResolutionH" class="image" />
							<time class="time">{{ o.name }}</time>
						</el-card>
						</router-link>
					</el-col>
				</el-row>
			</el-main>
		</el-container>
		<el-footer></el-footer>
	</el-container>
</template>
<script>
import { mapGetters} from 'vuex'	
export default {
	data() {
		return {	        
			reseted: true
		}
	},
	created() {		
		this.say('e7716fea6aa1483c80cfc10b7795fcb8');
	},
	methods: {
		say(nID) {
			this.$store.dispatch('migutv_data', nID);
		},
	},
	computed: {
		...mapGetters(['migutvNav', 'migutvCont'])
	}
}
</script>
<style>
.el-container {
	padding: 0px;
	margin: 0px;
	height: 100%;
}

.el-header {
	font-size: 30px;
	text-align: center;
	background-color: #b3c0d1;
	color: #333;
	line-height: 60px;
}
.el-carousel__item h3 {
	color: #475669;
	font-size: 18px;
	opacity: 0.75;
	line-height: 300px;
	margin: 0;
}

.el-carousel__item:nth-child(2n) {
	background-color: #99a9bf;
}

.el-carousel__item:nth-child(2n + 1) {
	background-color: #d3dce6;
}
.el-footer {
	font-size: 30px;
	text-align: center;
	background-color: #b3c0d1;
	color: #333;
	line-height: 60px;
}
.time {
	font-size: 13px;
	color: #999;
}

.bottom {
	margin-top: 13px;
	line-height: 12px;
}

.image {
	width: 100%;
	display: block;
}

.clearfix:before,
.clearfix:after {
	display: table;
	content: '';
}

.clearfix:after {
	clear: both;
}
.el-aside {
	color: #333;
}
</style>
