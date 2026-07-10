import { onMounted, ref } from 'vue';
import axios from 'axios';
import { useStore } from 'vuex';

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
export default {
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
  setup() {
    const store = useStore();
    const countryId = ref('');
    const changed = ref(false);
    const historyId = ref<number | null>(null);
    const hide = ref(true);
    const setAxiosInterceptor = () => {
      // リクエスト
      axios.interceptors.request.use(
        (config) => {
          config.headers['Content-Type'] = 'application/json';
          hide.value = true;
          return config;
        },
        (error) => {
          hide.value = false;
          return Promise.reject(error);
        },
      );

      // レスポンス
      axios.interceptors.response.use(
        (response) => {
          hide.value = false;
          return response;
        },
        (error) => {
          hide.value = false;
          return Promise.reject(error.response);
        },
      );
    };
    const setCheckboxEvent = () => {
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
    };
    const setTabEvent = () => {
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
    };
    const changeCountry = ($event: Event) => {
      const target = $event.target as HTMLInputElement;
      countryId.value = target.value;
      if (!changed.value) {
        changed.value = true;
      }
    };
    const openModal = (url: string, width: string, height: string) => {
      store.dispatch('openModal', {
        url: url,
        width: width,
        height: height,
      });
    };
    const openDialog = (title: string | null, messages: string[], type: string) => {
      store.dispatch('openDialog', {
        title: title,
        messages: messages,
        type: type,
      });
    };
    const select = (value: string) => {
      historyId.value = parseInt(value, 10);
    };
    const clearHistory = () => {
      historyId.value = 0;
    };

    onMounted(() => {
      setTabEvent();
      setCheckboxEvent();
      setAxiosInterceptor();
      hide.value = false;
    });

    return {
      changeCountry,
      clearHistory,
      hide,
      historyId,
      openDialog,
      openModal,
      countryId,
      changed,
      select,
    };
  },
};
