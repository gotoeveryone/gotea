<?=$this->Form->create(null, [
    'id' => 'mainForm',
    'method' => 'post',
    'action' => 'search',
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

    <section id="scoreUpdates">
        <table class="scoreUpdates">
            <?php if (!empty($scoreUpdates) && count($scoreUpdates) > 0) { ?>
            <tbody>
            <?php foreach ($scoreUpdates as $key => $scoreUpdate) : ?>
                <tr class="headerRow2">
                    <td colspan="4">
                        <?=h($scoreUpdate->country->COUNTRY_NAME)?>
                        <?=$this->Form->hidden('scoreUpdates['.$key.'][countryCd]', ['value' => h($scoreUpdate->COUNTRY_CD)])?>
                    </td>
                </tr>
                <tr class="row">
                    <?=
                        $this->Form->hidden('scoreUpdates['.$key.'][updateFlag]', [
                            'id' => 'updateFlag-'.$key,
                            'value' => 'false'
                        ])
                    ?>
                    <?=$this->Form->hidden('scoreUpdates['.$key.'][scoreId]', ['value' => h($scoreUpdate->ID)])?>
                    <td class="right detailColumn1">
                        成績更新日：
                    </td>
                    <td class="detailColumn2">
                        <?=
                            $this->Form->text('scoreUpdates['.$key.'][scoreUpdateDate]', [
                                'id' => 'scoreUpdateDate-'.$key,
                                'value' => $this->Date->format($scoreUpdate->SCORE_UPDATE_DATE, 'YYYY/MM/dd'),
                                'readonly' >= true,
                                'class' => 'checkChange datepicker'
                            ]);
                        ?>
                        <?=
                            $this->Form->hidden('scoreUpdates['.$key.'][bean-scoreUpdateDate]', [
                                'id' => 'bean-scoreUpdateDate-'.$key,
                                'value' => $this->Date->format($scoreUpdate->SCORE_UPDATE_DATE, 'YYYY/MM/dd')
                            ])
                        ?>
                    </td>
                    <td class="right detailColumn1">
                        更新日時：
                    </td>
                    <td class="detailColumn2">
                        <?=$this->Date->formatToDateTime($scoreUpdate->MODIFIED)?>
                        <?=
                            $this->Form->hidden('scoreUpdates['.$key.'][modified]', [
                                'value' => $this->Date->format($scoreUpdate->MODIFIED, 'YYYYMMddHHiiss')
                            ])
                        ?>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
            <?php } ?>
        </table>
    </section>
<?=$this->Form->end()?>
<script type="text/javascript">
    $(function() {
        $('#save').attr('disabled', ($('#searchFlag').val() === 'false'));

        // 登録・更新ボタン押下時
        $('#save').click(function() {
            var tbody = $('table.scoreUpdates tbody');
            if (!tbody.find('input[type=text]').hasClass('red')) {
                // 変更対象がないので更新しない
                var dialog = $("#dialog");
                dialog.html('変更された項目がありません！');
                dialog.click();
            } else {
                var rows = tbody.find('tr.row');
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