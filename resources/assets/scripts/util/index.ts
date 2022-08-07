import Pikaday from 'pikaday';
import 'pikaday/css/pikaday.css';

import dayjs from 'dayjs';
import 'dayjs/locale/ja';

// ロケールを設定
dayjs.locale('ja');

declare let document: Document;

// 日付選択オプション
export const pikadayOptions = (element: HTMLElement, birthday: boolean): Pikaday.PikadayOptions => {
  const nowYear = dayjs().year();
  const startYear = birthday ? 1920 : nowYear - 10;
  const endYear = birthday ? nowYear - 5 : nowYear + 1;
  const defaultDate = dayjs()
    .set('years', birthday ? endYear : nowYear)
    .toDate();
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

// タブ変更
export const changeTab = (element: HTMLInputElement): void => {
  // タブ・コンテンツを非表示
  const tabs = document.querySelectorAll('.tabs .tab');
  Array.prototype.slice
    .call(tabs, 0)
    .forEach((ce: HTMLElement) => ce.classList.remove('selectTab'), false);
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
