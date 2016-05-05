// 初期処理
$(document).ready(function() {
    // 日付入力欄が存在すればクラスを設定
    setDatepicker();
    setTooltip();

    $('#confirm, #dialog').click(function (event) {
        $(this).dialog('open');
        event.preventDefault();
    });

    // 確認ダイアログ
    $('#confirm').dialog({
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
                    $('#mainForm').submit();
                    var button = $('.ui-dialog-buttonpane').find('button:contains("OK")');
                    button.attr('disabled', true);
                    button.addClass('ui-state-disabled');
                }
            },
            {
                text: 'キャンセル',
                click: function () {
                    $(this).dialog('close');
                }
            }
        ]
    });

    $('#dialog').dialog({
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
                click: function () {
                    $(this).dialog('close');
                }
            }
        ]
    });

    var dialog = $('#dialog');
    var message = dialog.text();
    // 空白文字をすべて置換（改行コードやタブなど）
    message = message.replace(/\s/g, '');
    // エラーメッセージが格納されていればダイアログに表示
    if (message) {
        dialog.click();
    }

    // 値変更時（テキスト）
    $('input[type=text].checkChange').blur(function() {
        var id = $(this).attr('id');
        var beforeId = '#bean-' + id;
        if ($(this).val() !== $(beforeId).val()) {
            $(this).addClass('red');
        } else {
            $(this).removeClass('red');
        }
    });

    // 値変更時（チェックボックス）
    $('input[type=checkbox].checkChange').blur(function() {
        var id = $(this).attr('id');
        var beforeId = '#bean-' + id;
        if ($(this).prop('checked') !== $(beforeId).val()) {
            $(this).addClass('red');
        } else {
            $(this).removeClass('red');
        }
    });

    selectTab($('section.details').eq(0).attr('id'));

    // タブ押下時
    $('section.tabs').click(function () {
        selectTab($(this).attr('name'));
    });
});

function selectTab(tabName) {
    $.each($('section.details'), function() {
        var id = $(this).attr('id');
        if (id === tabName) {
            $(this).removeClass('unVisible');
            $('section#tabs section[name=' + id + ']').addClass('selectTab');
        } else {
            $(this).addClass('unVisible');
            $('section#tabs section[name=' + id + ']').removeClass('selectTab');
        }
    });
}

function getDatepickerObject() {
    return {
		closeText: '閉じる',
		prevText: '&#x3c;前',
		nextText: '次&#x3e;',
		currentText: '今日',
		monthNames: ['1月','2月','3月','4月','5月','6月',
		'7月','8月','9月','10月','11月','12月'],
		monthNamesShort: ['1月','2月','3月','4月','5月','6月',
		'7月','8月','9月','10月','11月','12月'],
		dayNames: ['日曜日','月曜日','火曜日','水曜日','木曜日','金曜日','土曜日'],
		dayNamesShort: ['日','月','火','水','木','金','土'],
		dayNamesMin: ['日','月','火','水','木','金','土'],
		weekHeader: '週',
		minDate: '1920/01/01',
		dateFormat: 'yy/mm/dd',
		firstDay: 0,
		isRTL: false,
		changeYear: true,
		showMonthAfterYear: true,
		yearSuffix: '年',
		onSelect: function() {
			var id = $(this).attr('id');
			var idArray = id.split('-');
			var beforeId = '#bean-' + idArray[0] + '-' + idArray[1];
            var label = $('#' + id + '-label');
			if ($(this).val() !== $(beforeId).val()) {
				$(this).addClass('red');
                label.addClass('red');
			} else {
				$(this).removeClass('red');
                label.removeClass('red');
			}
            label.text($(this).val());
		}
    };
}

// 日付欄の入力制御
function setDatepicker() {
	$('.datepicker').datepicker(getDatepickerObject());
}

// ツールチップの設定
function setTooltip() {
    $('.tooltip').tooltip({
        show: {
            delay: 200
        },
        hide: {
            delay: 200
        },
        position: {
            my: 'left top',
            at: 'left bottom',
            collision: 'fit'
        },
        track: true
    });
}

// キーの入力制御
function kdown(event) {
	var msg = "";
	var flg = 1;
    var obj = event.target;
    switch (obj.tagName) {
        case "INPUT":
            // typeがテキストもしくはパスワードの場合、押下を許可
            if (obj.type !== "text"
                    && obj.type !== "password" && event.keyCode !== 9
                    && event.keyCode !== 16 && event.keyCode !== 32) {
                return false;
            } else {
                flg = 0;
            }
            break;
        case "TEXTAREA":
            flg = 0;
            break;
        case "SELECT":
            flg = 0;
            break;
        case "A":
            flg = 2;
            break;
	}
	// キーコードの取得・判定
	switch (event.keyCode) {
	case 8:
		msg = "BS";
		break;
	case 13:
		msg = "Enter";
		break;
	case 27:
		msg = "Esc";
		break;
	case 78:
		if (event.ctrlKey) {
			msg = "Ctrl+N";
		}
		break;
	case 82:
		if (event.ctrlKey) {
			msg = "Ctrl+R";
		}
		break;
	case 116:
		msg = "F5";
		break;
	case 122:
		msg = "F11";
		break;
	}
	if (event.altKey) {
		msg = "Alt";
	}
	if (flg === 0) {
		switch (event.keyCode) {
		case 8:
			msg = "";
			break;
		case 13:
			msg = "";
			break;
		}
	} else if (flg === 2 && event.keyCode === 13) {
		msg = "";
	}
	if (msg !== "") {
		event.keyCode = 0;
		return false;
	} else {
		return true;
	}
}
document.onkeydown = kdown;

// Hiddenに選択した値を設定
function setHidden(obj, setId) {
	$('#' + setId).val(obj.val());
}

// ウィンドウをクローズし、親ウィンドウにフォーカスをあてる
function windowClose() {
    window.close();
    window.opener.focus();
}

// Ajax処理からフォームをサブミットする
function submitForm(targetForm) {
    targetForm.submit();
    $.blockUI();
}
