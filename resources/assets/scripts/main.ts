import Vue from 'vue';
import axios from 'axios';

import store from '@/store';

import BlockUI from '@/components/parts/BlockUI.vue';
import Dialog from '@/components/parts/Dialog.vue';
import Modal from '@/components/parts/Modal.vue';
import AddButton from '@/components/players/Button.vue';
import Ranking from '@/components/ranking/Index.vue';
import Titles from '@/components/titles/Index.vue';
import AddHistory from '@/components/titles/AddHistory.vue';
import Ranks from '@/components/ranks/Index.vue';
import NotificationListPage from '@/pages/notifications/index.vue';
import { Window } from '@/types';

declare let window: Window;

/**
 * アプリケーションのVueインスタンス
 */
const App = new Vue({
  store,
  el: '.container',
  components: {
    appBlock: BlockUI,
    appModal: Modal,
    appDialog: Dialog,
    addButton: AddButton,
    addHistory: AddHistory,
    ranking: Ranking,
    titles: Titles,
    ranks: Ranks,
    NotificationListPage,
  },
  data: {
    countryId: '',
    changed: false,
    historyId: null as number | null,
    hide: true,
  },
  mounted() {
    require('@/util');

    // リクエスト
    axios.interceptors.request.use(
      config => {
        config.headers = {
          'Content-Type': 'application/json',
        };
        if (window.Cake.accessUser) {
          config.headers['X-Access-User'] = window.Cake.accessUser;
        }
        this.hide = true;
        return config;
      },
      error => {
        this.hide = false;
        return Promise.reject(error);
      }
    );

    // レスポンス
    axios.interceptors.response.use(
      response => {
        this.hide = false;
        return response;
      },
      error => {
        this.hide = false;
        return Promise.reject(error.response);
      }
    );

    this.hide = false;
  },
  methods: {
    changeCountry($event: Event) {
      const target = $event.target as HTMLInputElement;
      this.countryId = target.value;
      if (!this.changed) {
        this.changed = true;
      }
    },
    openModal(url: string, width: string, height: string) {
      this.$store.dispatch('openModal', {
        url: url,
        width: width,
        height: height,
      });
    },
    openDialog(title: string | null, messages: string[], type: string) {
      this.$store.dispatch('openDialog', {
        title: title,
        messages: messages,
        type: type,
      });
    },
    select(historyId: string) {
      this.historyId = parseInt(historyId, 10);
    },
    clearHistory() {
      this.historyId = 0;
    },
  },
});

window.App = App;

export default App;
