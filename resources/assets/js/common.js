// 画面をブロック
const block = () => {
    const blockui = document.querySelector('.block-ui');
    blockui.classList.add('blocked');
};

// ブロック解除
const unblock = () => {
    const blockui = document.querySelector('.block-ui');
    blockui.classList.remove('blocked');
};

// タブ変更
const changeTab = (_element) => {
    // タブ・コンテンツを非表示
    const tabs = document.querySelectorAll('.tabs .tab');
    Array.prototype.slice.call(tabs, 0).forEach(element => {
        element.classList.remove('selectTab');
    }, false);
    const tabContents = document.querySelectorAll('.tab-contents');
    Array.prototype.slice.call(tabContents, 0).forEach(element => {
        element.classList.add('not-select');
    }, false);

    // 選択したコンテンツを表示
    _element.classList.add('selectTab');
    const selectTab = _element.getAttribute('data-tabname');
    const selectContents = document.querySelector(`[data-contentname=${selectTab}]`);
    if (selectContents) {
        selectContents.classList.remove('not-select');
    }
};

// 日付選択オプション
const pikadayOptions = (_element, _birthday) => {
    const nowYear = new Date().getFullYear();
    const startYear = (_birthday ? 1920 : nowYear - 10);
    const endYear = (_birthday ? nowYear - 5 : nowYear + 1);
    return {
        field: _element,
        i18n: {
            previousMonth: '前の月',
            nextMonth: '次の月',
            months: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
            weekdays: ['日曜日', '月曜日', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日'],
            weekdaysShort: ['日', '月', '火', '水', '木', '金', '土'],
        },
        closeText: '閉じる',
        currentText: '今日',
        weekHeader: '週',
        minDate: '1920/01/01',
        format: 'YYYY/MM/DD',
        firstDay: 0,
        isRTL: false,
        changeYear: true,
        yearRange: [startYear, endYear],
        showMonthAfterYear: true,
        yearSuffix: '年',
    };
};

const main = document.querySelector('.main');
main.classList.add('hide');

// ドキュメント準備完了
window.onload = () => {
    const main = document.querySelector('.main');
    main.classList.remove('hide');

    // リンク・ボタンにブロック追加
    const links = document.querySelectorAll('a[href]');
    Array.prototype.slice.call(links, 0).forEach(element => {
        element.addEventListener('click', () => {
            block();
        }, false);
    });
    const forms = document.querySelectorAll('form');
    Array.prototype.slice.call(forms, 0).forEach(element => {
        element.addEventListener('submit', () => {
            block();
        }, false);
    });
};

// タブ押下時
const tabWrap = document.querySelector('.tabs');
if (tabWrap) {
    const tabs = tabWrap.querySelectorAll('.tab');
    // イベントの設定
    Array.prototype.slice.call(tabs, 0).forEach(element => {
        element.addEventListener('click', (event) => {
            changeTab(event.target);
        }, false);
    });

    // 初期表示タブの指定があればそのタブを表示
    const selectTabName = tabWrap.getAttribute('data-selecttab');
    if (selectTabName) {
        const selectTab = document.querySelector(`[data-tabname=${selectTabName}]`);
        if (selectTab) {
            changeTab(selectTab);
        }
    } else {
        // 無ければ1つ目
        changeTab(tabs[0]);
    }
}

import Pikaday from 'pikaday';
import 'pikaday/css/pikaday.css';
const datepicker = document.querySelectorAll('.datepicker');
Array.prototype.slice.call(datepicker, 0).forEach(element => {
    new Pikaday(pikadayOptions(element, element.classList.contains('birthday')));
});

// 戻るボタン
const back = document.querySelector('.back');
if (back) {
    back.addEventListener('click', () => {
        location.href = '/';
    }, false);
}

// 引退フラグ・引退日
const isRetired = document.querySelector('#retired');
if (isRetired) {
    // 引退フラグにチェックされていれば引退日の入力欄を設定可能に
    const setRetired = function () {
        var isRetired = document.querySelector('#retired');
        if (isRetired) {
            var retired = document.querySelector('[name=retired]');
            if (isRetired.checked) {
                retired.disabled = false;
            } else {
                retired.disabled = true;
                retired.value = '';
            }
        }
    };
    setRetired();
    isRetired.addEventListener('click', () => {
        setRetired();
    }, false);
}

// クエリ整形
const inputQueries = document.querySelector('#input-queries');
if (inputQueries) {
    inputQueries.addEventListener('blur', (event) => {
        // クエリを整形
        // 前後の空白をトリムして、空行を削除
        event.target.value = event.target.value.trim().replace(/;[\t]/g, ';\n').replace(/　/g, '')
            .replace(/[\t]/g, '').replace(new RegExp(/^\r/gm), '').replace(new RegExp(/^\n/gm), '');
    }, false);
}

// クエリ更新
const updateQuery = document.querySelector('[data-button-type=execute-queries]');
if (updateQuery) {
    updateQuery.addEventListener('click', (event) => {
        const inputQueries = document.querySelector('#input-queries');
        if (!inputQueries.value) {
            event.preventDefault();
            App.openDialog(null, '更新対象が1件も存在しません。');
            unblock();
            return;
        }

        if (!confirm('更新します。よろしいですか？')) {
            event.preventDefault();
        }
    }, false);
}

// クエリクリア
const clearQuery = document.querySelector('[data-button-type=clear-queries]');
if (clearQuery) {
    clearQuery.addEventListener('click', () => {
        const textarea = document.querySelector('#input-queries');
        textarea.value = '';
    }, false);
}
