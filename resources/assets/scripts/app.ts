import { defineComponent } from 'vue';
import axios from 'axios';
import Pikaday from 'pikaday';

import BlockUI from '@/components/parts/BlockUI.vue';
import Dialog from '@/components/parts/Dialog.vue';
import Modal from '@/components/parts/Modal.vue';
import AddButton from '@/components/players/Button.vue';
import Ranking from '@/components/ranking/Index.vue';
import Titles from '@/components/titles/Index.vue';
import AddHistory from '@/components/titles/AddHistory.vue';
import Ranks from '@/components/ranks/Index.vue';
import { pikadayOptions } from '@/libs/pikaday';
import NotificationListPage from '@/pages/notifications/index.vue';
import TableTemplateListPage from '@/pages/table-templates/index.vue';

// タブ変更
const changeTab = (element: HTMLElement): void => {
  // タブ・コンテンツを非表示
  const tabs = document.querySelectorAll('.tabs .tab');
  tabs.forEach((ce) => ce.classList.remove('selectTab'), false);
  const tabContents = document.querySelectorAll('.tab-contents');
  tabContents.forEach((ce) => ce.classList.add('not-select'), false);

  // 選択したコンテンツを表示
  element.classList.add('selectTab');
  const selectTab = element.getAttribute('data-tabname');
  const selectContents = document.querySelector(`[data-contentname=${selectTab}]`);
  if (selectContents) {
    selectContents.classList.remove('not-select');
  }
};

/**
 * アプリケーションのVueインスタンス
 */
export default defineComponent({
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
    this.setDatepickerEvent();
    this.setTabEvent();
    this.setCheckboxEvent();
    this.setAxiosInterceptor();

    this.hide = false;
  },
  methods: {
    setAxiosInterceptor() {
      // リクエスト
      axios.interceptors.request.use(
        (config) => {
          config.headers['Content-Type'] = 'application/json';
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
    },
    setCheckboxEvent() {
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
    },
    setDatepickerEvent() {
      // 日付選択のイベントを登録
      document.addEventListener(
        'focus',
        (event: Event) => {
          const element = event.target as HTMLElement;
          if (
            element.classList &&
            element.classList.contains('datepicker') &&
            !element.classList.contains('set-event')
          ) {
            element.classList.add('set-event');
            new Pikaday(pikadayOptions(element, element.classList.contains('birthday'))).show();
          }
        },
        true,
      );
    },
    setTabEvent() {
      // タブ押下時
      const tabWrapper = document.querySelector('.tabs');
      if (!tabWrapper) {
        return;
      }

      const tabs = tabWrapper.querySelectorAll('.tab');
      if (!tabs.length) {
        return;
      }

      // イベントの設定
      tabs.forEach((element) => {
        element.addEventListener(
          'click',
          (event: Event) => changeTab(event.target as HTMLInputElement),
          false,
        );
      });

      // 初期表示タブの指定があればそのタブを表示
      const selectTabName = tabWrapper.getAttribute('data-selecttab');
      if (selectTabName) {
        const selectTab = document.querySelector(`[data-tabname=${selectTabName}]`);
        if (selectTab) {
          changeTab(selectTab as HTMLInputElement);
        }
      } else {
        // 無ければ1つ目
        changeTab(tabs[0] as HTMLInputElement);
      }
    },
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
