import Pikaday from 'pikaday';
import 'pikaday/css/pikaday.css';

import dayjs from 'dayjs';
import 'dayjs/locale/ja';

import { Window } from '@/types';

// ロケールを設定
dayjs.locale('ja');

declare let window: Window;
declare let document: Document;

// APIコールの設定
window.Cake = window.Cake || {};

// 日付選択オプション
const pikadayOptions = (element: HTMLElement, birthday: boolean): Pikaday.PikadayOptions => {
  const nowYear = dayjs().year();
  const startYear = birthday ? 1920 : nowYear - 10;
  const endYear = birthday ? nowYear - 5 : nowYear + 1;
  const defaultDate = dayjs().set('years', birthday ? endYear : nowYear).toDate();
  const maxDate = dayjs().set('years', endYear).toDate();
  return {
    field: element,
    i18n: {
      previousMonth: '前の月',
      nextMonth: '次の月',
      months: [
        '1月',
        '2月',
        '3月',
        '4月',
        '5月',
        '6月',
        '7月',
        '8月',
        '9月',
        '10月',
        '11月',
        '12月',
      ],
      weekdays: ['日曜日', '月曜日', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日'],
      weekdaysShort: ['日', '月', '火', '水', '木', '金', '土'],
    },
    // closeText: '閉じる',
    // currentText: '今日',
    // weekHeader: '週',
    minDate: dayjs('1920-01-01').toDate(),
    defaultDate,
    maxDate,
    format: 'YYYY/MM/DD',
    firstDay: 0,
    isRTL: false,
    // changeYear: true,
    yearRange: [startYear, endYear],
    showMonthAfterYear: true,
    yearSuffix: '年',
  };
};

// 日付選択のイベントを登録
document.addEventListener(
  'focus',
  (event: Event) => {
    const element = event.target as HTMLInputElement;
    if (
      element.classList &&
      element.classList.contains('datepicker') &&
      !element.classList.contains('set-event')
    ) {
      element.classList.add('set-event');
      new Pikaday(pikadayOptions(element, element.classList.contains('birthday'))).show();
    }
  },
  true
);

// タブ変更
const changeTab = (element: HTMLInputElement): void => {
  // タブ・コンテンツを非表示
  const tabs = document.querySelectorAll('.tabs .tab');
  Array.prototype.slice.call(tabs, 0).forEach((ce: HTMLElement) => ce.classList.remove('selectTab'), false);
  const tabContents = document.querySelectorAll('.tab-contents');
  Array.prototype.slice
    .call(tabContents, 0)
    .forEach((ce: HTMLElement) => ce.classList.add('not-select'), false);

  // 選択したコンテンツを表示
  element.classList.add('selectTab');
  const selectTab = element.getAttribute('data-tabname');
  const selectContents = document.querySelector(`[data-contentname=${selectTab}]`);
  if (selectContents) {
    selectContents.classList.remove('not-select');
  }
};

// タブ押下時
const tabWrap = document.querySelector('.tabs');
if (tabWrap) {
  const tabs = tabWrap.querySelectorAll('.tab');
  // イベントの設定
  Array.prototype.slice.call(tabs, 0).forEach((element: HTMLElement) => {
    element.addEventListener('click', (event: Event) => changeTab(event.target as HTMLInputElement), false);
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
    const target = document.querySelector(`[data-target="${checked.getAttribute('data-checked')}"]`) as HTMLInputElement;
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

// クエリ整形
const inputQueries = document.querySelector('#input-queries');
if (inputQueries) {
  inputQueries.addEventListener(
    'blur',
    (event: Event) => {
      // クエリを整形
      // 前後の空白をトリムして、空行を削除
      const target = event.target as HTMLInputElement;
      target.value = target.value
        .trim()
        .replace(/;[\t]/g, ';\n')
        .replace(/\u3000/g, '')
        .replace(/[\t]/g, '')
        .replace(new RegExp(/^\r/gm), '')
        .replace(new RegExp(/^\n/gm), '');
    },
    false
  );
}

// クエリ更新
const updateQuery = document.querySelector('[data-button-type=execute-queries]');
if (updateQuery) {
  updateQuery.addEventListener(
    'click',
    (event: Event) => {
      const queryText = document.querySelector('#input-queries') as HTMLInputElement;
      if (!queryText.value) {
        event.preventDefault();
        window.App.openDialog(null, ['更新対象が1件も存在しません。'], 'warning');
        return;
      }

      if (!confirm('更新します。よろしいですか？')) {
        event.preventDefault();
      }
    },
    false
  );
}

// クエリクリア
const clearQuery = document.querySelector('[data-button-type=clear-queries]');
if (clearQuery) {
  clearQuery.addEventListener(
    'click',
    () => {
      const textarea = document.querySelector('#input-queries') as HTMLInputElement;
      textarea.value = '';
    },
    false
  );
}
