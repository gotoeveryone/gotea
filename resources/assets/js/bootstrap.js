import Vue from 'vue/dist/vue.common';
import VueResource from 'vue-resource';
import Vuex from 'vuex';

// Element-ui
import lang from 'element-ui/lib/locale/lang/ja';
import locale from 'element-ui/lib/locale';
import 'element-ui/lib/theme-default/icon.css';

locale.use(lang);

Vue.use(VueResource);
Vue.use(Vuex);

Vue.http.interceptors.push((request, next) => {
    request.headers.set('Content-Type', 'application/json');
    request.headers.set('X-CSRF-Token', Cake.csrfToken);
    if (Cake.accessUser) {
        request.headers.set('X-Access-User', Cake.accessUser);
    }

    document.querySelector('.block-ui').classList.add('blocked');
    next(() => {
        document.querySelector('.block-ui').classList.remove('blocked');
    });
});

window.Vue = Vue;
