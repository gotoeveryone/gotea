import dayjs from 'dayjs';
import 'dayjs/locale/ja';

// ロケールを設定
dayjs.locale('ja');

declare let document: Document;

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
