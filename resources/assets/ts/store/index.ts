import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        modal: {
            url: '',
            height: '',
            width: '',
            callback: null,
        },
        dialog: {
            title: '',
            messages: '',
            type: 'info',
            server: false,
        },
    },
    getters: {
        modalOptions: (state) => () => {
            return state.modal
        },
        dialogOptions: (state) => () => {
            return state.dialog
        },
    },
    mutations: {
        openModal(state, _modal) {
            state.modal = _modal
        },
        closeModal(state) {
            state.modal = {
                url: '',
                width: '',
                height: '',
                callback: null,
            }
        },
        openDialog(state, _dialog) {
            state.dialog = _dialog
        },
        closeDialog(state) {
            state.dialog = {
                title: '',
                messages: '',
                type: 'info',
                server: false,
            }
        },
    },
    actions: {
        openModal(context, _modal) {
            context.commit('openModal', _modal)
        },
        closeModal(context) {
            context.commit('closeModal')
        },
        openDialog(context, _dialog) {
            context.commit('openDialog', _dialog)
        },
        closeDialog(context) {
            context.commit('closeDialog')
        },
    },
})
