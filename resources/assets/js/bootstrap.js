import Vue from 'vue/dist/vue.common';
import VueResource from 'vue-resource';
import Vuex from 'vuex';

Vue.use(VueResource);
Vue.use(Vuex);

Vue.http.interceptors.push((request, next) => {
    request.headers.set('X-CSRF-Token', Cake.csrfToken);
    request.headers.set('Content-Type', 'application/json');

    document.querySelector('.block-ui').classList.add('blocked');
    next(() => {
        document.querySelector('.block-ui').classList.remove('blocked');
    });
});

window.Vue = Vue;
