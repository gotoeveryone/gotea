import './bootstrap';

import { store } from './store';

import Dialog from './components/parts/Dialog.vue';
import Modal from './components/parts/Modal.vue';

import Ranking from './components/ranking/Index.vue';
import Titles from './components/titles/Index.vue';
import Ranks from './components/ranks/Index.vue';

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
        ranks: Ranks,
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
