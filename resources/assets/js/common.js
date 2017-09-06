// ドキュメント準備完了
window.onload = () => {
    // 戻るボタン
    const back = document.querySelector('.back');
    if (back) {
        back.addEventListener('click', () => {
            location.href = '/';
        }, false);
    }

    // 棋士保存
    const save = document.querySelector('[data-button-type=player]');
    if (save) {
        save.addEventListener('click', (e) => {
            openConfirm('棋士情報を' + e.target.innerText + 'します。よろしいですか？');
        });
    }

    // 引退フラグ・引退日
    const isRetired = document.querySelector('#retired');
    if (isRetired) {
        // 引退フラグにチェックされていれば引退日の入力欄を設定可能に
        const setRetired = function() {
            var isRetired = document.querySelector('#retired');
            if (isRetired) {
                var retired = document.querySelector('[name=retired]');
                if (isRetired.checked) {
                    retired.disabled = true;
                    retired.value = '';
                } else {
                    retired.disabled = false;
                }
            }
        };
        setRetired();
        isRetired.addEventListener('click', () => {
            setRetired();
        }, false);
    }

    // クエリ更新
    const updateScore = document.querySelector('[data-button-type=execute-queries]');
    if (updateScore) {
        updateScore.addEventListener('click', () => {
            const textarea = document.querySelector('#input-queries');
            // クエリを整形
            // 前後の空白をトリムして、空行を削除
            const queries = textarea.value;
            const repText = queries.trim().replace(/;[\t]/g, ';\n').replace(/　/g, '')
                    .replace(/[\t]/g, '').replace(new RegExp(/^\r/gm), '').replace(new RegExp(/^\n/gm), '');

            if (repText) {
                // 更新処理
                textarea.value = repText;
                openConfirm('更新します。よろしいですか？');
            } else {
                const dialog = document.querySelector("#dialog");
                dialog.innerText = '更新対象が1件も存在しません。';
                dialog.click();
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
};

// ダイアログのメッセージ
window.outMessage = '';

// 確認ダイアログ
window.openConfirm = (message, form) => {
    var confirm = $('#confirm');
    confirm.text(message);

    // ダイアログの設定
    confirm.dialog({
        autoOpen: false,
        modal: true,
        top: 0,
        left: 0,
        width: 400,
        open: function (event, ui) {
            $('.ui-dialog-titlebar-close').hide();
        },
        buttons: [
            {
                text: 'OK',
                click: function (event) {
                    if (!form) {
                        form = $("#mainForm");
                    }
                    form.submit();
                    var button = $('.ui-dialog-buttonpane').find('button:contains("OK")');
                    button.attr('disabled', true);
                    button.addClass('ui-state-disabled');
                }
            },
            {
                text: 'キャンセル',
                class: "cancel",
                click: function () {
                    $(this).dialog('close');
                }
            }
        ]
    });

    // ダイアログオープン
    confirm.dialog("open");
};

// タブ選択
window.selectTab = (tabName) => {
    var details = $('.detail-dialog .detail > *');
    details.hide();
    if (!tabName) {
        var target = details.eq(0);
        $('#tabs [name=' + target.attr("id") + ']').addClass('selectTab');
        target.fadeIn();
        return;
    }
    $.each(details, function() {
        var obj = $(this);
        var id = obj.attr('id');
        if (id === tabName) {
            $('#tabs [name=' + id + ']').addClass('selectTab');
            obj.fadeIn();
        } else {
            $('#tabs [name=' + id + ']').removeClass('selectTab');
        }
    });
}

// Ajax処理からフォームをサブミットする
window.submitForm = (targetForm) => {
    targetForm.submit();
    $.blockUI();
}

function getDatepickerObject() {
    return {
		closeText: "閉じる",
		prevText: "&#x3c;前",
		nextText: "次&#x3e;",
		currentText: "今日",
		monthNames: ["1月","2月","3月","4月","5月","6月",
		"7月","8月","9月","10月","11月","12月"],
		monthNamesShort: ["1月","2月","3月","4月","5月","6月",
		"7月","8月","9月","10月","11月","12月"],
		dayNames: ["日曜日","月曜日","火曜日","水曜日","木曜日","金曜日","土曜日"],
		dayNamesShort: ["日","月","火","水","木","金","土"],
		dayNamesMin: ["日","月","火","水","木","金","土"],
		weekHeader: "週",
		minDate: "1920/01/01",
		dateFormat: "yy/mm/dd",
		firstDay: 0,
		isRTL: false,
		changeYear: true,
        yearRange: "-100:+1",
		showMonthAfterYear: true,
        yearSuffix: "年",
		onSelect: function(d, i) {
            // onChangeイベントを強制発火
            if (d !== i.lastVal) {
                var customEvent = document.createEvent('HTMLEvents');
                customEvent.initEvent('change', true, false);
                $(this).get(0).dispatchEvent(customEvent);
            }
		}
    };
}

// 日付欄の入力制御
function setDatepicker() {
    $('.content').on('focus', '.datepicker', function() {
        $(this).datepicker(getDatepickerObject());
    });
    // 誕生日の場合は初期表示を20年前に設定
    $('.datepicker.birthday').each(function() {
        if (!$(this).val()) {
            $(this).datepicker("option", "defaultDate", "-20y");
        }
    });
}

// 初期処理
$(document).ready(function() {
    // 各種初期設定
    setDatepicker();

    $("main").animate({opacity: "show"}, 500);

    $('#dialog').click(function (event) {
        $(this).dialog('open');
        event.preventDefault();
    });

    // タブ押下時
    $("#tabs .tab").on("click", function () {
        var obj = $(this);
        if (obj.hasClass("selectTab")) {
            return false;
        }
        selectTab(obj.attr('name'));
    });
});
