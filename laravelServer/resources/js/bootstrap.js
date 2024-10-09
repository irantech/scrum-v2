window._ = require('lodash');
try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');
    require('bootstrap');
} catch (e) {
}
import axios from 'axios';
import Vue from 'vue';
import VueRouter from 'vue-router';
import VueSimpleAlert from "vue-simple-alert";
import vueSmoothScroll from 'vue2-smooth-scroll'
import ViewUI from 'view-design';
import 'view-design/dist/styles/iview.css';

window.axios = axios;
window.Vue = Vue;
Vue.use(VueRouter);
Vue.use(vueSmoothScroll,{
    updateHistory : false,
    duration: 400,
    offset : -70
});
Vue.use(VueSimpleAlert);
Vue.use(ViewUI);

window.axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': window.Laravel.csrfToken
};
axios.interceptors.request.use(function (config) {
    const accessToken = localStorage.getItem('token');
    if(accessToken){
        config.headers.Authorization = "Bearer " + accessToken;
    }
    return config;
});

axios.interceptors.response.use(response => {
    // NProgress.done();
    return response
});

window.axios.defaults.baseURL = `${Laravel.baseUrl}/api/`;
