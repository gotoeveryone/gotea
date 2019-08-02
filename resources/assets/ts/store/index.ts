import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

const initialState = {
  modal: {
    url: '',
    height: '',
    width: '',
    callback: () => {},
  },
  dialog: {
    modalColor: '',
    headerColor: '',
    title: '',
    messages: '',
    type: 'info',
    server: false,
  },
};

export default new Vuex.Store({
  state: Object.assign({}, initialState),
  getters: {
    modalOptions: state => () => {
      return state.modal;
    },
    dialogOptions: state => () => {
      return state.dialog;
    },
  },
  mutations: {
    openModal(state, _modal) {
      state.modal = _modal;
    },
    closeModal(state) {
      state.modal = Object.assign({}, initialState.modal);
    },
    openDialog(state, _dialog) {
      state.dialog = _dialog;
    },
    closeDialog(state) {
      state.dialog = Object.assign({}, initialState.dialog);
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
