<article class="update-points">
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
        <?=$this->Form->hidden('searchFlag', ['id' => 'searchFlag', 'value' => (empty($searchFlag) ? 'false' : var_export($searchFlag, TRUE))]);?>
        <section class="search-header">
            <section class="row">
                <section class="label">対象年：</section>
                <section>
                    <?=
                        $this->Form->input('searchYear', [
                            'options' => $years,
                            'value' => (isset($searchYear) ? $searchYear : ''),
                            'class' => 'year'
                        ]);
                    ?>
                </section>
                <section class="button-column">
                    <?=$this->Form->button('検索', ['type' => 'submit'])?>
                    <?=
                        $this->Form->button('一括更新', [
                            'id' => 'save',
                            'type' => 'button'
                        ]);
                    ?>
                </section>
            </section>
        </section>

        <?php if (!empty($updatedPoints)) : ?>
        <section class="search-results">
            <?php foreach ($updatedPoints as $key => $point) : ?>
                <section class="group">
                    <section class="label-row">
                        <span><?=h($point->country->name)?></span>
                    </section>
                    <section class="input-row">
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
                </section>
            <?php endforeach ?>
        </section>
        <?php endif ?>
    <?=$this->Form->end()?>
</article>

<?php $this->Html->scriptStart(['inline' => false, 'block' => 'script']); ?>
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
<?php $this->Html->scriptEnd(); ?>
