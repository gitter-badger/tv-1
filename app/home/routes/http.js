import Vue from 'vue'
import axios from 'axios'

axios.defaults.baseURL = '/api'

Vue.prototype.$http = axios


export default axios
