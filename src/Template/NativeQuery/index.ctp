<?=$this->Form->create(null, [
    'id' => 'mainForm',
    'method' => 'post',
    'url' => ['action' => 'execute'],
    'templates' => [
        'inputContainer' => '{{content}}',
        'textFormGroup' => '{{input}}',
        'selectFormGroup' => '{{input}}'
    ]
])?>
    <section class="updateScore">
        <?=$this->Form->textarea('executeTargets', ['id' => 'executeTargets'])?>
        <?=
            $this->Form->button('更新', [
                'type' => 'button',
                'id' => 'update'
            ])
        ?>
        <?=
            $this->Form->button('クリア', [
                'type' => 'button',
                'id' => 'clear'
            ])
        ?>
    </section>
<?=$this->Form->end()?>
<script type="text/javascript">
    $(function() {
        // 更新ボタン押下時
        $('#update').click(function() {
            // クエリを整形
            // 前後の空白をトリムして、空行を削除
            var queries = $('#executeTargets').val();
            var repText = $.trim(queries).replace(/;[\t]/g, ';\n').replace(/　/g, '')
                    .replace(/[\t]/g, '').replace(new RegExp(/^\r/gm), '').replace(new RegExp(/^\n/gm), '');
            $('#executeTargets').val(repText);

            if ($('#executeTargets').val()) {
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
            $('#executeTargets').val('');
        });
    });
</script>