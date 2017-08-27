import Vue from 'vue/dist/vue.common';
import VueResource from 'vue-resource';
import VueCookie from 'vue-cookie';

Vue.use(VueResource);
Vue.use(VueCookie);

Vue.http.interceptors.push((request, next) => {
    request.headers.set('X-CSRF-Token', Cake.csrfToken);

    document.querySelector('.block-ui').classList.add('blocked');
    next(() => {
        document.querySelector('.block-ui').classList.remove('blocked');
    });
});

import { store } from './store';
import Modal from './components/Modal.vue';
import Dialog from './components/Dialog.vue';
import Ranking from './components/ranking/Ranking.vue';
import Titles from './components/titles/Title.vue';

export let WEB_ROOT = '/admin/';

new Vue({
    store,
    el: '.content',
    props: {
        domain: String,
    },
    data: {
        countryId: '',
        disabled: true,
    },
    components: {
        modal: Modal,
        appDialog: Dialog,
        ranking: Ranking,
        titles: Titles,
    },
    methods: {
        changeCountry($event) {
            this.countryId = $event.target.value;
            this.disabled = (this.countryId === '');
        },
        newPlayer(_url) {
            this.openModal(`${_url}?country_id=${this.countryId}`);
        },
        openModal(_url, _width, _height) {
            this.$store.dispatch('openModal', {
                url: _url,
                width: _width,
                height: _height,
            });
        },
    },
});
