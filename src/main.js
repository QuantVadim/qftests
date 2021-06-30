import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import axios from 'axios'
import CONST from './others/Constants'
import VueAxios from 'vue-axios'
import PrimeVue from 'primevue/config';
import store from './store'
import EqualVue from 'equal-vue'
import 'equal-vue/dist/style.css'
import ToastService from 'primevue/toastservice';

import Block from './components/Block'


let Vue = createApp(App);
Vue.component('block', Block)

Vue.use(store)
.use(router)
.use(CONST)
.use(EqualVue)
.use(PrimeVue)
.use(ToastService)
.use(VueAxios, axios)
.mount('#app')
