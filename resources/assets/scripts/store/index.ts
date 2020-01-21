import Vue from 'vue';
import Vuex from 'vuex';
import { DialogOption, ModalOption, State } from '@/types';

Vue.use(Vuex);

const initialState: State = {
  modal: {
    url: null,
    height: null,
    width: null,
    callback: null,
  },
  dialog: {
    modalColor: null,
    headerColor: null,
    title: null,
    messages: [],
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
    openModal(state, _modal: ModalOption) {
      state.modal = { ..._modal };
    },
    closeModal(state) {
      state.modal = { ...initialState.modal };
    },
    openDialog(state, _dialog: DialogOption) {
      state.dialog = { ..._dialog };
    },
    closeDialog(state) {
      state.dialog = { ...initialState.dialog };
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
