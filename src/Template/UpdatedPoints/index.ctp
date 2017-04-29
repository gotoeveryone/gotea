<section class="update-points">
    <?=$this->Form->create(null, [
        'id' => 'mainForm',
        'type' => 'post',
        'url' => ['action' => 'search'],
        'templates' => [
            'inputContainer' => '{{content}}',
            'textFormGroup' => '{{input}}',
            'selectFormGroup' => '{{input}}'
        ]
    ])?>
        <ul class="search-header">
            <li class="search-row">
                <label>対象年：</label>
                <?=$this->Form->select('year', $years, ['class' => 'year'])?>
                <div class="button-wrap">
                    <?=$this->Form->button('検索', ['type' => 'submit'])?>
                    <?=$this->Form->button('一括更新', ['id' => 'save', 'type' => 'button'])?>
                </div>
            </li>
        </ul>

        <?php if (!empty($updatedPoints)) : ?>
        <div class="search-results">
            <ul class="table-body">
            <?php foreach ($updatedPoints as $key => $point) : ?>
                <?=$this->Form->hidden('results['.$key.'][id]', ['value' => $point->id])?>
                <?=$this->Form->hidden('results['.$key.'][country_id]', ['value' => $point->country_id])?>
                <?=
                    $this->Form->hidden('results['.$key.'][modified]', [
                        'value' => $this->Date->format($point->modified, 'YYYYMMddHHmmss')
                    ])
                ?>
                <?=
                    $this->Form->hidden('results['.$key.'][update_flag]', [
                        'id' => 'updateFlag-'.$key,
                        'value' => $point->update_flag
                    ])
                ?>
                <li class="label-row">
                    <span><?=h($point->country->name)?></span>
                </li>
                <li class="input-row">
                    <span>
                    成績更新日：
                    <?=
                        $this->Form->text('results['.$key.'][score_updated]', [
                            'id' => 'scoreUpdateDate-'.$key,
                            'value' => $this->Date->format($point->score_updated, 'YYYY/MM/dd'),
                            'readonly' >= true,
                            'class' => 'checkChange datepicker'
                        ]);
                    ?>
                    <?=
                        $this->Form->hidden('results['.$key.'][bean_score_updated]', [
                            'value' => $this->Date->format($point->score_updated, 'YYYY/MM/dd')
                        ])
                    ?>
                    </span>
                    <span>
                        更新日時：<?=$this->Date->formatToDateTime($point->modified)?>
                    </span>
                </li>
            <?php endforeach ?>
            </ul>
        </div>
        <?php endif ?>
    <?=$this->Form->end()?>
</section>

<?php $this->MyHtml->scriptStart(['inline' => false, 'block' => 'script']); ?>
<script>
    $(function() {
        $('#save').attr('disabled', ($('#searchFlag').val() === 'false'));

        // 登録・更新ボタン押下時
        $('#save').click(function() {
            var section = $('.search-results');
            if (!section.find('input[type=text]').hasClass('red')) {
                // 変更対象がないので更新しない
                var dialog = $("#dialog");
                dialog.html('変更された項目がありません！');
                dialog.click();
            } else {
                var rows = section.find('.group');
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
<?php $this->MyHtml->scriptEnd(); ?>
