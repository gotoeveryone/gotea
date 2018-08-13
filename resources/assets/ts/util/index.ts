import Pikaday from 'pikaday'
import 'pikaday/css/pikaday.css'

import moment from 'moment'
import 'moment/locale/ja'

// ロケールを設定
moment.locale('ja')

declare var window: any
declare var document: any

// APIコールの設定
window.Cake = window.Cake || {}

// 日付選択オプション
const pikadayOptions = (element: any, birthday: boolean) => {
    const nowYear = moment().year()
    const startYear = birthday ? 1920 : nowYear - 10
    const endYear = birthday ? nowYear - 5 : nowYear + 1
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
            weekdays: [
                '日曜日',
                '月曜日',
                '火曜日',
                '水曜日',
                '木曜日',
                '金曜日',
                '土曜日',
            ],
            weekdaysShort: ['日', '月', '火', '水', '木', '金', '土'],
        },
        closeText: '閉じる',
        currentText: '今日',
        weekHeader: '週',
        minDate: moment('1920-01-01').toDate(),
        maxDate: moment().toDate(),
        format: 'YYYY/MM/DD',
        firstDay: 0,
        isRTL: false,
        changeYear: true,
        yearRange: [startYear, endYear],
        showMonthAfterYear: true,
        yearSuffix: '年',
    }
}

// 日付選択のイベントを登録
document.addEventListener(
    'focus',
    (event: any) => {
        const element = event.target
        if (
            element.classList &&
            element.classList.contains('datepicker') &&
            !element.classList.contains('set-event')
        ) {
            element.classList.add('set-event')
            new Pikaday(
                pikadayOptions(element, element.classList.contains('birthday'))
            ).show()
        }
    },
    true
)

// タブ変更
const changeTab = (element: any) => {
    // タブ・コンテンツを非表示
    const tabs = document.querySelectorAll('.tabs .tab')
    Array.prototype.slice
        .call(tabs, 0)
        .forEach((ce: any) => ce.classList.remove('selectTab'), false)
    const tabContents = document.querySelectorAll('.tab-contents')
    Array.prototype.slice
        .call(tabContents, 0)
        .forEach((ce: any) => ce.classList.add('not-select'), false)

    // 選択したコンテンツを表示
    element.classList.add('selectTab')
    const selectTab = element.getAttribute('data-tabname')
    const selectContents = document.querySelector(
        `[data-contentname=${selectTab}]`
    )
    if (selectContents) {
        selectContents.classList.remove('not-select')
    }
}

// タブ押下時
const tabWrap = document.querySelector('.tabs')
if (tabWrap) {
    const tabs = tabWrap.querySelectorAll('.tab')
    // イベントの設定
    Array.prototype.slice.call(tabs, 0).forEach((element: any) => {
        element.addEventListener(
            'click',
            (event: any) => changeTab(event.target),
            false
        )
    })

    // 初期表示タブの指定があればそのタブを表示
    const selectTabName = tabWrap.getAttribute('data-selecttab')
    if (selectTabName) {
        const selectTab = document.querySelector(
            `[data-tabname=${selectTabName}]`
        )
        if (selectTab) {
            changeTab(selectTab)
        }
    } else {
        // 無ければ1つ目
        changeTab(tabs[0])
    }
}

// 引退フラグ・引退日
const isRetired = document.querySelector('#is-retired')
if (isRetired) {
    // 引退フラグにチェックされていれば引退日の入力欄を設定可能に
    const setRetired = () => {
        const retired = document.querySelector('[name=retired]')
        if (retired) {
            if (isRetired.checked) {
                retired.disabled = false
            } else {
                retired.disabled = true
                retired.value = ''
            }
        }
    }
    setRetired()
    isRetired.addEventListener('click', () => setRetired(), false)
}

// クエリ整形
const inputQueries = document.querySelector('#input-queries')
if (inputQueries) {
    inputQueries.addEventListener(
        'blur',
        (event: any) => {
            // クエリを整形
            // 前後の空白をトリムして、空行を削除
            event.target.value = event.target.value
                .trim()
                .replace(/;[\t]/g, ';\n')
                .replace(/\u3000/g, '')
                .replace(/[\t]/g, '')
                .replace(new RegExp(/^\r/gm), '')
                .replace(new RegExp(/^\n/gm), '')
        },
        false
    )
}

// クエリ更新
const updateQuery = document.querySelector(
    '[data-button-type=execute-queries]'
)
if (updateQuery) {
    updateQuery.addEventListener(
        'click',
        (event: any) => {
            const queryText = document.querySelector('#input-queries')
            if (!queryText.value) {
                event.preventDefault()
                window.App.openDialog(
                    null,
                    ['更新対象が1件も存在しません。'],
                    'warning'
                )
                return
            }

            if (!confirm('更新します。よろしいですか？')) {
                event.preventDefault()
            }
        },
        false
    )
}

// クエリクリア
const clearQuery = document.querySelector('[data-button-type=clear-queries]')
if (clearQuery) {
    clearQuery.addEventListener(
        'click',
        () => {
            const textarea = document.querySelector('#input-queries')
            textarea.value = ''
        },
        false
    )
}
