import Vuex from 'vuex';

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
            type: 'info',
        },
    },
    getters: {
        modalOptions: (state) => () => {
            return state.modal;
        },
        dialogOptions: (state) => () => {
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
        openDialog(context, _dialog) {
            context.commit('openDialog', _dialog);
        },
        closeDialog(context) {
            context.commit('closeDialog');
        },
    },
});
