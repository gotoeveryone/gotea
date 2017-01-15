<section class="titles">
    <div>
        <?=$this->Form->create(null, [
            'id' => 'searchForm',
            'type' => 'post',
            'url' => ['action' => 'search'],
            'templates' => [
                'inputContainer' => '{{content}}',
                'textFormGroup' => '{{input}}',
                'selectFormGroup' => '{{input}}'
            ]
        ])?>
            <ul class="search-header">
                <?=$this->Form->hidden('is_search', ['id' => 'isSearch'])?>
                <li class="search-row">
                    <label>対象国：</label>
                    <?=
                        $this->Form->input('country_id', [
                            'options' => $countries,
                            'class' => 'country'
                        ]);
                    ?>
                    <label>終了棋戦：</label>
                    <?=
                        $this->Form->input('is_closed', [
                            'options' => [
                                '0' => '検索しない',
                                '1' => '検索する'
                            ],
                            'class' => 'excluded'
                        ]);
                    ?>
                    <div class="button-column">
                        <?=$this->Form->button('検索', ['type' => 'submit'])?>
                        <?=$this->Form->button('行追加', [
                            'type' => 'button',
                            'id' => 'addRow',
                            'data-button-type' => 'control'
                        ])?>
                        <?=$this->Form->button('一括更新', [
                            'type' => 'button',
                            'form' => 'saveAllForm',
                            'id' => 'save',
                            'data-button-type' => 'control'
                        ])?>
                        <?=$this->Form->button('JSON出力', [
                            'type' => 'button',
                            'id' => 'outputJson',
                            'data-button-type' => 'control'
                        ])?>
                    </div>
                </li>
            </ul>
        <?=$this->Form->end()?>
    </div>

    <div class="search-results">
        <?=$this->Form->create(null, [
            'id' => 'saveAllForm',
            'type' => 'post',
            'url' => ['action' => 'saveAll'],
            'templates' => [
                'inputContainer' => '{{content}}',
                'textFormGroup' => '{{input}}',
                'selectFormGroup' => '{{input}}'
            ]
        ])?>
            <?=$this->Form->hidden('is_closed')?>
            <?=$this->Form->hidden('country_id')?>
            <ul class="table-header">
                <li class="table-row">
                    <span class="name">タイトル名</span>
                    <span class="name">タイトル名（英語）</span>
                    <span class="holding">期</span>
                    <span class="winner">優勝者</span>
                    <span class="order">並び<br>順</span>
                    <span class="team">団体</span>
                    <span class="filename">HTML<br>ファイル名</span>
                    <span class="modified">修正日</span>
                    <span class="closed">終了<br>棋戦</span>
                    <span>詳細</span>
                </li>
            </ul>
            <?php if (!empty($titles) && count($titles) > 0) : ?>
            <ul class="table-body">
                <?php foreach ($titles as $key => $title) : ?>
                <li class="table-row<?= (($title->is_closed) ? ' excluded-row' : ''); ?>">
                    <?=$this->Form->hidden('titles['.$key.'][is_save]', ['value' => false])?>
                    <?=$this->Form->hidden('titles['.$key.'][id]', ['value' => $title->id])?>
                    <?=$this->Form->hidden('titles['.$key.'][optimistic_key]', ['value' => $this->Date->format($title->modified, 'YYYYMMddHHmmss')])?>
                    <span class="name">
                        <?=
                            $this->Form->text('titles['.$key.'][name]', [
                                'value' => $title->name,
                                'class' => 'checkChange'
                            ]);
                        ?>
                        <?=$this->Form->hidden('titles['.$key.'][bean_name]', ['value' => $title->name])?>
                    </span>
                    <span class="name">
                        <?=
                            $this->Form->text('titles['.$key.'][name_english]', [
                                'value' => $title->name_english,
                                'class' => 'checkChange'
                            ]);
                        ?>
                        <?=$this->Form->hidden('titles['.$key.'][bean_name_english]', ['value' => $title->name_english])?>
                    </span>
                    <span class="holding">
                        <?=
                            $this->Form->text('titles['.$key.'][holding]', [
                                'value' => $title->holding,
                                'maxlength' => 3,
                                'class' => 'checkChange'
                            ]);
                        ?>
                        <?=$this->Form->hidden('titles['.$key.'][bean_holding]', ['value' => $title->holding])?>
                    </span>
                    <span class="winner">
                        <?php
                            if (empty($title->retention_histories)) {
                                echo '';
                            } else {
                                $retention = $title->retention_histories[0];
                                if ($title->is_team) {
                                    echo $retention->win_group_name;
                                } else {
                                    echo "{$retention->player->name} {$retention->rank->name}";
                                }
                            }
                        ?>
                    </span>
                    <span class="order">
                        <?=
                            $this->Form->text('titles['.$key.'][sort_order]', [
                                'value' => $title->sort_order,
                                'maxlength' => 2,
                                'class' => 'checkChange'
                            ]);
                        ?>
                        <?=$this->Form->hidden('titles['.$key.'][bean_sort_order]', ['value' => $title->sort_order])?>
                    </span>
                    <span class="team">
                        <?=
                            $this->Form->checkbox('titles['.$key.'][is_team]', [
                                'checked' => $title->is_team,
                                'class' => 'checkChange'
                            ]);
                        ?>
                        <?=$this->Form->hidden('titles['.$key.'][bean_is_team]', ['value' => var_export($title->is_team, TRUE)])?>
                    </span>
                    <span class="filename">
                        <?=
                            $this->Form->text('titles['.$key.'][html_file_name]', [
                                'value' => $title->html_file_name,
                                'class' => 'checkChange'
                            ]);
                        ?>
                        <?=$this->Form->hidden('titles['.$key.'][bean_html_file_name]', ['value' => $title->html_file_name])?>
                    </span>
                    <span class="modified">
                        <?php
                            $htmlFileModified = $this->Date->format($title->html_file_modified, 'YYYY/MM/dd');
                        ?>
                        <?=
                            $this->Form->text('titles['.$key.'][html_file_modified]', [
                                'value' => $htmlFileModified,
                                'class' => 'checkChange datepicker'
                            ]);
                        ?>
                        <?=$this->Form->hidden('titles['.$key.'][bean_html_file_modified]', ['value' => $htmlFileModified])?>
                    </span>
                    <span class="closed">
                        <?=
                            $this->Form->checkbox('titles['.$key.'][is_closed]', [
                                'checked' => $title->is_closed,
                                'class' => 'checkChange'
                            ]);
                        ?>
                        <?=$this->Form->hidden('titles['.$key.'][bean_is_closed]', ['value' => var_export($title->is_closed, TRUE)])?>
                    </span>
                    <span class="center">
                        <?=$this->Html->link('開く', ['action' => "detail/{$title->id}"], ['class' => 'colorbox'])?>
                    </span>
                </li>
                <?php endforeach ?>
            </ul>
            <?php endif ?>
        <?=$this->Form->end()?>
    </div>
</section>

<?php $this->MyHtml->scriptStart(['inline' => false, 'block' => 'script']); ?>
<script>
    $(function() {
        // ボタンの活性制御
        if ($('#isSearch').val()) {
            $('[data-button-type=control]').removeAttr('disabled');
        } else {
            $('[data-button-type=control').attr('disabled', true);
        }

        // JSON出力ボタン押下時
        $('#outputJson').on('click', function() {
            $.ajax({
                type: 'GET',
                url: '<?=$this->Url->build(['controller' => 'api', 'action' => 'news'])?>?make=true',
                contentType: 'application/json'
            }).done(function (data) {
                var dialog = $("#dialog");
                dialog.html('JSON出力に成功しました。');
                dialog.click();
            }).fail(function (data) {
                var dialog = $("#dialog");
                dialog.html('<span class="red">JSON出力に失敗しました。</span>');
                dialog.click();
            });
        });

        var counter = <?=(empty($titles) ? 0 : count($titles))?>;
        // 行追加ボタン押下時
        $('#addRow').on('click', function() {
            counter++;

            // TR要素を作成
            var tr = $('<tr>')
                    .append($('<input>', {type: 'hidden', name: 'titles[' + counter + '][is_save]', value: false}))
                    .append($('<td>', {class: 'left titleName'})
                        .append($('<input>', {type: 'text', name: 'titles[' + counter + '][name]', class: 'red'}))
                    )
                    .append($('<td>', {class: 'left titleNameEn'})
                        .append($('<input>', {type: 'text', name: 'titles[' + counter + '][name_english]', class: 'red'}))
                    )
                    .append($('<td>', {class: 'left holding'})
                        .append($('<input>', {type: 'text', name: 'titles[' + counter + '][holding]', class: 'red', maxlength: 3}))
                    )
                    .append($('<td>', {class: 'left winner'})
                        .html('&nbsp;')
                    )
                    .append($('<td>', {class: 'left order'})
                        .append($('<input>', {type: 'text', name: 'titles[' + counter + '][sort_order]', class: 'red', maxlength: 2}))
                    )
                    .append($('<td>', {class: 'left groupFlag'})
                        .append($('<input>', {type: 'hidden', name: 'titles[' + counter + '][is_team]', value: 0}))
                        .append($('<input>', {type: 'checkbox', name: 'titles[' + counter + '][is_team]', class: 'red'}))
                    )
                    .append($('<td>', {class: 'left htmlFileName'})
                        .append($('<input>', {type: 'text', name: 'titles[' + counter + '][html_file_name]', class: 'red'}))
                    )
                    .append($('<td>', {class: 'left htmlModifyDate'})
                        .append($('<input>', {type: 'text', name: 'titles[' + counter + '][html_file_modified]', class: 'red datepicker'}))
                    )
                    .append($('<td>', {class: 'left deleteFlag'})
                        .append($('<input>', {type: 'hidden', name: 'titles[' + counter + '][is_closed]', value: 0}))
                        .append($('<input>', {type: 'checkbox', name: 'titles[' + counter + '][is_closed]', class: 'red'}))
                    )
                    .append($('<td>', {class: 'center openRetain'})
                        .html('新規')
                    );

            // 日付ダイアログの設定
            tr.find('.datepicker').datepicker(getDatepickerObject());

            // 一覧に要素を追加
            $('table.titles').append(tr);
        });

        // 一括更新ボタン押下時
        $('#save').on('click', function() {
            var tbody = $('table.titles tbody');
            if (!tbody.find('input[type!=hidden]').hasClass('red')) {
                // 変更対象がないので更新しない
                var dialog = $("#dialog");
                dialog.html('変更された項目がありません！');
                dialog.click();
            } else {
                var rows = tbody.find('tr');
                rows.each(function() {
                    var obj = $(this);
                    if (obj.find('input[type!=hidden]').hasClass('red')) {
                        obj.find('[name*=is_save]').val(true);
                    }
                });
                submitForm($("#saveAllForm"));
            }
        });
    });
</script>
<?php $this->MyHtml->scriptEnd(); ?>
