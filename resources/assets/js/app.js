import './bootstrap';

import { store } from './store';

import Dialog from './components/parts/Dialog.vue';
import Modal from './components/parts/Modal.vue';

import AddButton from './components/players/Button.vue';
import Ranking from './components/ranking/Index.vue';
import Titles from './components/titles/Index.vue';
import AddHistory from './components/titles/AddHistory.vue';
import Ranks from './components/ranks/Index.vue';

window.App = new Vue({
    store,
    el: '.main-content',
    data: {
        countryId: '',
        changed: false,
        historyId: 0,
    },
    components: {
        modal: Modal,
        appDialog: Dialog,
        addButton: AddButton,
        addHistory: AddHistory,
        ranking: Ranking,
        titles: Titles,
        ranks: Ranks,
    },
    methods: {
        changeCountry($event) {
            this.countryId = $event.target.value;
            if (!this.changed) {
                this.changed = true;
            }
        },
        openModal(_url, _width, _height) {
            this.$store.dispatch('openModal', {
                url: _url,
                width: _width,
                height: _height,
            });
        },
        openDialog(_title, _messages, _type) {
            this.$store.dispatch('openDialog', {
                title: _title,
                messages: _messages,
                type: _type,
            });
        },
        select(_historyId) {
            this.historyId = parseInt(_historyId, 10)
        },
        clearHistory() {
            this.historyId = 0;
        },
    },
    computed: {
        modal() {
            return this.$store.getters.modalOptions();
        },
        dialog() {
            return this.$store.getters.dialogOptions();
        },
    },
});
