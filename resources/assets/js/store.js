import Vue from 'vue/dist/vue.common';
import Vuex from 'vuex';

Vue.use(Vuex);

export const store = new Vuex.Store({
    state: {
        modal: {
            url: '',
            height: '',
            width: '',
        },
        dialog: {
            title: '',
            messages: '',
        },
    },
    getters: {
        modalOptions: (state, getters) => () => {
            return state.modal;
        },
        dialogOptions: (state, getters) => () => {
            return state.dialog;
        },
    },
    mutations: {
        openModal(state, _modal) {
            state.modal = _modal;
        },
        closeModal(state) {
            state.modal = {
                url: '',
                width: '',
                height: '',
            };
        },
        openDialog(state, _dialog) {
            state.dialog = _dialog;
        },
        closeDialog(state) {
            state.dialog = {
                title: '',
                messages: '',
            };
        },
    },
    actions: {
        openModal(context, _modal) {
            context.commit('openModal', _modal);
        },
        closeModal(context) {
            context.commit('closeModal');
        },
        openDialog(context, _message, _title) {
            context.commit('openDialog', {
                messages: _message,
                title: _title,
            });
        },
        closeDialog(context) {
            context.commit('closeDialog');
        },
    },
});
