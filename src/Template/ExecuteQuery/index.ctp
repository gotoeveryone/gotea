<script type="text/javascript">
    $(document).ready(function() {
        // 更新ボタン押下時
        $('#update').click(function() {
            // クエリを整形
            // 前後の空白をトリムして、空行を削除
            var queries = $('#updateText').val();
            var repText = $.trim(queries).replace(/;[\t]/g, ';\n').replace(/　/g, '').replace(/[\t]/g, '').replace(new RegExp(/^\n/gm), '');
            $('#updateText').val(repText);

            if ($('#updateText').val() !== '') {
                // 更新処理
                var confirm = $("#confirm");
                confirm.html('更新します。よろしいですか？');
                confirm.click();
            } else {
                var dialog = $("#dialog");
                dialog.html('更新対象が1件も存在しません。');
                dialog.click();
            }
        });
        // クリアボタン押下時
        $('#clear').click(function() {
            $('#updateText').val('');
        });
    });
</script>

<form id="mainForm" method="post" action="<?= $this->Url->build('/ExecuteQuery'); ?>">
    <section class="updateScore">
        <?=$this->Form->textarea('テキスト', ['id' => 'updateText', 'name' => 'updateText']);?>
        <?=$this->Form->button('更新', [
            'type' => 'button',
            'id' => 'update'
        ]);?>
        <?=$this->Form->button('クリア', [
            'type' => 'button',
            'id' => 'clear'
        ]);?>
    </section>
</form>
