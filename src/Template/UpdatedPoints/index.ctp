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
    <?=
        $this->Form->hidden('searchFlag', [
            'id' => 'searchFlag',
            'value' => (empty($searchFlag) ? 'false' : var_export($searchFlag, TRUE))
        ]);
    ?>
    <table class="scoreUpdatesHeader">
        <tr>
            <td class="searchColumn1">対象年：</td>
            <td>
                <?=
                    $this->Form->input('searchYear', [
                        'options' => $years,
                        'value' => h(empty($searchYear) ? '' : $searchYear),
                        'class' => 'year'
                    ]);
                ?>
            </td>
            <td class="right">
                <?=$this->Form->button('検索', ['type' => 'submit'])?>
                <?=
                    $this->Form->button('一括更新', [
                        'id' => 'save',
                        'type' => 'button'
                    ]);
                ?>
            </td>
        </tr>
    </table>

    <?php if (!empty($updatedPoints)) : ?>
    <section id="scoreUpdates">
        <?php foreach ($updatedPoints as $key => $point) : ?>
            <section class="headerRow">
                <?=h($point->country->name)?>
            </section>
            <section class="detail">
                <?=$this->Form->hidden('scoreUpdates['.$key.'][countryCd]', ['value' => h($point->id)])?>
                <?=
                    $this->Form->hidden('scoreUpdates['.$key.'][updateFlag]', [
                        'id' => 'updateFlag-'.$key,
                        'value' => 'false'
                    ])
                ?>
                <span>
                <?=$this->Form->hidden('scoreUpdates['.$key.'][scoreId]', ['value' => h($point->id)])?>
                成績更新日：
                <?=
                    $this->Form->text('scoreUpdates['.$key.'][scoreUpdateDate]', [
                        'id' => 'scoreUpdateDate-'.$key,
                        'value' => $this->Date->format($point->score_updated, 'YYYY/MM/dd'),
                        'readonly' >= true,
                        'class' => 'checkChange datepicker'
                    ]);
                ?>
                </span>
                <span>
                <?=
                    $this->Form->hidden('scoreUpdates['.$key.'][bean-scoreUpdateDate]', [
                        'id' => 'bean-scoreUpdateDate-'.$key,
                        'value' => $this->Date->format($point->score_updated, 'YYYY/MM/dd')
                    ])
                ?>
                更新日時：
                <?=$this->Date->formatToDateTime($point->modified)?>
                <?=
                    $this->Form->hidden('scoreUpdates['.$key.'][modified]', [
                        'value' => $this->Date->format($point->modified, 'YYYYMMddHHiiss')
                    ])
                ?>
                </span>
            </section>
        <?php endforeach ?>
    </section>
    <?php endif ?>
    </section>
<?=$this->Form->end()?>
<script type="text/javascript">
    $(function() {
        $('#save').attr('disabled', ($('#searchFlag').val() === 'false'));

        // 登録・更新ボタン押下時
        $('#save').click(function() {
            var section = $('section#scoreUpdates');
            if (!section.find('input[type=text]').hasClass('red')) {
                // 変更対象がないので更新しない
                var dialog = $("#dialog");
                dialog.html('変更された項目がありません！');
                dialog.click();
            } else {
                var rows = section.find('section.detail');
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