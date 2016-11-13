<?=$this->Form->create(null, [
    'id' => 'mainForm',
    'method' => 'post',
    'url' => ['action' => 'search'],
    'templates' => [
        'inputContainer' => '{{content}}',
        'textFormGroup' => '{{input}}',
        'selectFormGroup' => '{{input}}'
    ]
])?>
<section class="searchHeader titles">
    <section class="searchRow">
        <section class="label">対象国：</section>
        <section>
            <?=
                $this->Form->input('searchCountry', [
                    'options' => $countries,
                    'value' => h($this->request->data('searchCountry')),
                    'class' => 'country'
                ]);
            ?>
        </section>
        <section class="label">終了棋戦：</section>
        <section>
            <?=
                $this->Form->input('searchDelete', [
                    'options' => [
                        'false' => '検索しない',
                        'true' => '検索する'
                    ],
                    'value' => h($this->request->data('searchDelete')),
                    'class' => 'retired'
                ]);
            ?>
        </section>
        <section class="right">
            <?=$this->Form->button('検索', ['type' => 'submit'])?>
            <?=$this->Form->button('行追加', [
                'type' => 'button',
                'id' => 'addRow'
            ])?>
            <?=$this->Form->button('一括更新', [
                'type' => 'button',
                'id' => 'save'
            ])?>
            <?=$this->Form->button('JSON出力', [
                'type' => 'button',
                'id' => 'outputJson'
            ])?>
        </section>
    </section>
</section>

<table class="searchView titles">
    <thead>
        <tr>
            <th class="titleName">タイトル名</th>
            <th class="titleNameEn">タイトル名（英語）</th>
            <th class="holding">期</th>
            <th class="winner">優勝者</th>
            <th class="order">並び<br>順</th>
            <th class="groupFlag">団体<br>戦</th>
            <th class="htmlFileName">HTML<br>ファイル名</th>
            <th class="htmlModifyDate">修正日</th>
            <th class="deleteFlag">終了<br>棋戦</th>
            <th class="openRetain">詳細</th>
        </tr>
    </thead>
    <?php if (!empty($titles) && count($titles) > 0) : ?>
    <tbody>
        <?php foreach ($titles as $key => $title) : ?>
        <?php
            $class = '';
            if ($title->is_closed) {
                $class = $class.'retired';
            }
            if ($class !== '') {
                $class = ' class="'.$class.'"';
            }
        ?>
        <tr <?=$class?>>
            <?=$this->Form->hidden('titles['.$key.'][updateFlag]', ['id' => 'updateFlag-'.$key, 'value' => 'false'])?>
            <?=$this->Form->hidden('titles['.$key.'][titleId]', ['value' => h($title->id)])?>
            <?=$this->Form->hidden('titles['.$key.'][lastUpdate]', ['value' => $this->Date->format($title->modified, 'YYYYMMddHHmmss')])?>
            <td class="left titleName">
                <?=
                    $this->Form->text('titles['.$key.'][titleName]', [
                        'id' => 'titleName-'.$key,
                        'value' => $title->name,
                        'class' => 'checkChange'
                    ]);
                ?>
                <?=
                    $this->Form->hidden('titles['.$key.'][bean-titleName]', [
                        'id' => 'bean-titleName-'.$key,
                        'value' => $title->name
                    ]);
                ?>
            </td>
            <td class="left titleNameEn">
                <?=
                    $this->Form->text('titles['.$key.'][titleNameEn]', [
                        'id' => 'titleNameEn-'.$key,
                        'value' => $title->name_english,
                        'class' => 'checkChange'
                    ]);
                ?>
                <?=
                    $this->Form->hidden('titles['.$key.'][bean-titleNameEn]', [
                        'id' => 'bean-titleNameEn-'.$key,
                        'value' => $title->name_english
                    ]);
                ?>
            </td>
            <td class="left holding">
                <?=
                    $this->Form->text('titles['.$key.'][holding]', [
                        'id' => 'holding-'.$key,
                        'maxlength' => 3,
                        'value' => h($title->holding),
                        'class' => 'checkChange'
                    ]);
                ?>
                <?=
                    $this->Form->hidden('titles['.$key.'][bean-holding]', [
                        'id' => 'bean-holding-'.$key,
                        'value' => h($title->holding)]
                    );
                ?>
            </td>
            <td class="left winner">
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
            </td>
            <td class="left order">
                <?=
                    $this->Form->text('titles['.$key.'][order]', [
                        'id' => 'order-'.$key,
                        'maxlength' => 2,
                        'value' => h($title->sort_order),
                        'class' => 'checkChange'
                    ]);
                ?>
                <?=
                    $this->Form->hidden('titles['.$key.'][bean-order]', [
                        'id' => 'bean-order-'.$key,
                        'value' => h($title->sort_order)]
                    );
                ?>
            </td>
            <td class="left groupFlag">
                <?=
                    $this->Form->checkbox('titles['.$key.'][groupFlag]', [
                        'id' => 'groupFlag-'.$key,
                        'checked' => $title->is_team,
                        'class' => 'checkChange'
                    ]);
                ?>
                <?=
                    $this->Form->hidden('titles['.$key.'][bean-groupFlag]', [
                        'id' => 'bean-groupFlag-'.$key,
                        'value' => var_export($title->is_team, TRUE)
                    ]);
                ?>
            </td>
            <td class="left htmlFileName">
                <?=
                    $this->Form->text('titles['.$key.'][htmlFileName]', [
                        'id' => 'htmlFileName-'.$key,
                        'value' => h($title->html_file_name),
                        'class' => 'checkChange'
                    ]);
                ?>
                <?=
                    $this->Form->hidden('titles['.$key.'][bean-htmlFileName]', [
                        'id' => 'bean-htmlFileName-'.$key,
                        'value' => h($title->html_file_name)
                    ]);
                ?>
            </td>
            <td class="left htmlModifyDate">
                <?php
                    $htmlModifyDate = $this->Date->format($title->html_file_modified, 'YYYY/MM/dd');
                ?>
                <?=
                    $this->Form->text('titles['.$key.'][htmlModifyDate]', [
                        'id' => 'htmlModifyDate-'.$key,
                        'value' => h($htmlModifyDate),
                        'class' => 'checkChange datepicker'
                    ]);
                ?>
                <?=
                    $this->Form->hidden('titles['.$key.'][bean-htmlModifyDate]', [
                        'id' => 'bean-htmlModifyDate-'.$key,
                        'value' => h($htmlModifyDate)
                    ]);
                ?>
            </td>
            <td class="left deleteFlag">
                <?=
                    $this->Form->checkbox('titles['.$key.'][deleteFlag]', [
                        'id' => 'deleteFlag-'.$key,
                        'checked' => $title->is_closed,
                        'class' => 'checkChange'
                    ]);
                ?>
                <?=
                    $this->Form->hidden('titles['.$key.'][bean-deleteFlag]', [
                        'id' => 'deleteFlag-'.$key,
                        'value' => var_export($title->is_closed, TRUE)
                    ]);
                ?>
            </td>
            <td class="center openRetain">
                <?=$this->Html->link('開く', ['action' => "detail/{$title->id}"], ['class' => 'colorbox'])?>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
    <?php endif ?>
</table>
<?=$this->Form->end()?>
<script type="text/javascript">
    $(function() {
        if ($('#searchFlag').val() === 'false') {
            $('#addRow').attr('disabled', true);
            $('#save').attr('disabled', true);
            $('#outputJson').attr('disabled', true);
        } else {
            $('#addRow').removeAttr('disabled');
            $('#save').removeAttr('disabled');
            $('#outputJson').removeAttr('disabled');
        }

        // JSON出力ボタン押下時
        $('#outputJson').click(function() {
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
        $('#addRow').click(function() {
            counter++;

            // TR要素を作成
            var tr = $('<tr>', {class: 'newRow'})
                    .append($('<input>', {type: 'hidden', id: 'insertFlag-' + counter, name: 'titles[' + counter + '][insertFlag]', value: true}))
                    .append($('<input>', {type: 'hidden', id: 'updateFlag-' + counter, name: 'titles[' + counter + '][updateFlag]', value: false}))
                    .append($('<td>', {class: 'left titleName'})
                        .append($('<input>', {type: 'text', id: 'titleName-' + counter, name: 'titles[' + counter + '][titleName]', class: 'red checkChange'}))
                    )
                    .append($('<td>', {class: 'left titleNameEn'})
                        .append($('<input>', {type: 'text', id: 'titleNameEn-' + counter, name: 'titles[' + counter + '][titleNameEn]', class: 'red checkChange imeDisabled'}))
                    )
                    .append($('<td>', {class: 'left holding'})
                        .append($('<input>', {type: 'text', id: 'holding-' + counter, name: 'titles[' + counter + '][holding]', class: 'red checkChange imeDisabled', maxlength: 3}))
                    )
                    .append($('<td>', {class: 'left winner'})
                        .html('&nbsp;')
                    )
                    .append($('<td>', {class: 'left order'})
                        .append($('<input>', {type: 'text', id: 'order-' + counter, name: 'titles[' + counter + '][order]', class: 'red checkChange imeDisabled', maxlength: 2}))
                    )
                    .append($('<td>', {class: 'left groupFlag'})
                        .append($('<input>', {type: 'hidden', id: 'groupFlag-' + counter + '_', name: 'titles[' + counter + '][groupFlag]', value: 0}))
                        .append($('<input>', {type: 'checkbox', id: 'groupFlag-' + counter, name: 'titles[' + counter + '][groupFlag]', class: 'red checkChange'}))
                    )
                    .append($('<td>', {class: 'left htmlFileName'})
                        .append($('<input>', {type: 'text', id: 'htmlFileName-' + counter, name: 'titles[' + counter + '][htmlFileName]', class: 'red checkChange imeDisabled'}))
                    )
                    .append($('<td>', {class: 'left htmlModifyDate'})
                        .append($('<input>', {type: 'text', id: 'htmlModifyDate-' + counter, name: 'titles[' + counter + '][htmlModifyDate]', class: 'red checkChange datepicker'}))
                    )
                    .append($('<td>', {class: 'left deleteFlag'})
                        .append($('<input>', {type: 'hidden', id: 'deleteFlag-' + counter + '_', name: 'titles[' + counter + '][deleteFlag]', value: 0}))
                        .append($('<input>', {type: 'checkbox', id: 'deleteFlag-' + counter, name: 'titles[' + counter + '][deleteFlag]', class: 'red checkChange'}))
                    )
                    .append($('<td>', {class: 'center openRetain'})
                        .html('新規')
                    );

            // 日付ダイアログの設定
            tr.find('input.datepicker').datepicker(getDatepickerObject());

            // 一覧に要素を追加
            $('table.titles').append(tr);
        });

        // 一括更新ボタン押下時
        $('#save').click(function() {
            var tbody = $('table.titles tbody');
            if (!tbody.find('input[type=text]').hasClass('red')
                    && !tbody.find('input[type=checkbox]').hasClass('red')) {
                // 変更対象がないので更新しない
                var dialog = $("#dialog");
                dialog.html('変更された項目がありません！');
                dialog.click();
            } else {
                var rows = tbody.find('tr');
                var resultSize = rows.length;
                for (var i = 0; i < resultSize; i++) {
                    if (rows.eq(i).find('input').hasClass('red')) {
                        rows.find('#updateFlag-' + i).val(true);
                    }
                }
                var form = $('#mainForm');
                form.attr('action', '<?=$this->Url->build(['action' => 'save'])?>');
                submitForm(form);
            }
        });
    });
</script>