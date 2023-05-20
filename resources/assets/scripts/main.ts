import { createApp } from 'vue';
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
import TableTemplateListPage from '@/pages/table-templates/index.vue';
import { Window } from '@/types';
import { changeTab } from '@/util';

declare let window: Window;

/**
 * アプリケーションのVueインスタンス
 */
const app = createApp({
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
    TableTemplateListPage,
  },
  data: () => {
    return {
      countryId: '',
      changed: false,
      historyId: null as number | null,
      hide: true,
    };
  },
  mounted() {
    // APIコールの設定
    window.Cake = window.Cake || {};

    // タブ押下時
    const tabWrap = document.querySelector('.tabs');
    if (tabWrap) {
      const tabs = tabWrap.querySelectorAll('.tab');
      // イベントの設定
      Array.prototype.slice.call(tabs, 0).forEach((element: HTMLElement) => {
        element.addEventListener(
          'click',
          (event: Event) => changeTab(event.target as HTMLInputElement),
          false,
        );
      });

      // 初期表示タブの指定があればそのタブを表示
      const selectTabName = tabWrap.getAttribute('data-selecttab');
      if (selectTabName) {
        const selectTab = document.querySelector(`[data-tabname=${selectTabName}]`);
        if (selectTab) {
          changeTab(selectTab as HTMLInputElement);
        }
      } else {
        // 無ければ1つ目
        changeTab(tabs[0] as HTMLInputElement);
      }
    }

    // チェックボックスと入力項目の連動
    const checked = document.querySelector('[data-checked]') as HTMLInputElement;
    if (checked) {
      // 引退フラグにチェックされていれば引退日の入力欄を設定可能に
      const setChecked = (withClear = false): void => {
        const target = document.querySelector(
          `[data-target="${checked.getAttribute('data-checked')}"]`,
        ) as HTMLInputElement;
        if (target) {
          if (checked.checked) {
            target.disabled = checked.getAttribute('data-is-check') === 'disabled';
          } else {
            target.disabled = checked.getAttribute('data-is-check') !== 'disabled';
          }
          if (target.disabled && withClear) {
            target.value = '';
          }
        }
      };
      setChecked();
      checked.addEventListener('click', () => setChecked(true), false);
    }

    // リクエスト
    axios.interceptors.request.use(
      (config) => {
        config.headers['Content-Type'] = 'application/json';
        if (window.Cake.accessUser) {
          config.headers['X-Access-User'] = window.Cake.accessUser;
        }
        this.hide = true;
        return config;
      },
      (error) => {
        this.hide = false;
        return Promise.reject(error);
      },
    );

    // レスポンス
    axios.interceptors.response.use(
      (response) => {
        this.hide = false;
        return response;
      },
      (error) => {
        this.hide = false;
        return Promise.reject(error.response);
      },
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

app.use(store);

app.mount('.container');

window.App = app;

export default app;
