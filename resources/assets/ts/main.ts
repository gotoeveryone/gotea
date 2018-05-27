import './util'

import Vue from 'vue'

import store from './store'

import Dialog from './components/parts/Dialog.vue'
import Modal from './components/parts/Modal.vue'
import AddButton from './components/players/Button.vue'
import Ranking from './components/ranking/Index.vue'
import Titles from './components/titles/Index.vue'
import AddHistory from './components/titles/AddHistory.vue'
import Ranks from './components/ranks/Index.vue'

declare var window: any

/**
 * アプリケーションのVueインスタンス
 */
window.App = new Vue({
    store,
    el: '.container',
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
        changeCountry($event: any) {
            this.countryId = $event.target.value
            if (!this.changed) {
                this.changed = true
            }
        },
        openModal(url: string, width: string, height: string) {
            this.$store.dispatch('openModal', {
                url: url,
                width: width,
                height: height,
            })
        },
        openDialog(title: string | null, messages: string[], type: string) {
            this.$store.dispatch('openDialog', {
                title: title,
                messages: messages,
                type: type,
            })
        },
        select(historyId: string) {
            this.historyId = parseInt(historyId, 10)
        },
        clearHistory() {
            this.historyId = 0
        },
    },
    computed: {
        modal(): any {
            return this.$store.getters.modalOptions()
        },
        dialog(): any {
            return this.$store.getters.dialogOptions()
        },
    },
})
