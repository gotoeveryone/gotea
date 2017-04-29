// 確認ダイアログ
var openConfirm = function(message, form) {
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

var checkChange = function(obj) {
    var self = obj;
    var parent = self.parent();
    var bean = parent.find('input[type=hidden][name*=bean_]');
    var compare = (self.attr('type') === 'checkbox' ? self.prop('checked') : self.val());
    if (compare !== bean.val()) {
        self.addClass('changed');
    } else {
        self.removeClass('changed');
    }
};

// 初期処理
$(document).ready(function() {
    // 各種初期設定
    setDatepicker();
    setTooltip();
    setColorbox();

    $('#dialog').click(function (event) {
        $(this).dialog('open');
        event.preventDefault();
    });

    // 値変更時
    $('.checkChange').on('change', function() {
        checkChange($(this));
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

function selectTab(tabName) {
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
		onSelect: function() {
            if ($(this).hasClass('checkChange')) {
                checkChange($(this));
            }
		}
    };
}

// 日付欄の入力制御
function setDatepicker() {
	$('.datepicker').datepicker(getDatepickerObject());
    // 誕生日の場合は初期表示を20年前に設定
    $('.datepicker.birthday').each(function() {
        if (!$(this).val()) {
            $(this).datepicker("option", "defaultDate", "-20y");
        }
    });
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

// カラーボックスの設定
function setColorbox(href) {
    var options = {
        iframe: true,
        width: "60%",
        height: "90%"
    };
    if (href) {
        options["href"] = href;
        $.colorbox(options);
    } else {
        $('.colorbox').colorbox(options);
    }
}

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
