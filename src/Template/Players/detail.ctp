<?=$this->Form->create(null, [
    'id' => 'mainForm',
    'method' => 'post',
    'action' => 'detail',
    'templates' => [
        'inputContainer' => '{{content}}',
        'textFormGroup' => '{{input}}',
        'selectFormGroup' => '{{input}}'
    ]
])?>
    <!-- 連続作成フラグ -->
    <?=$this->Form->hidden('isContinue', ['id' => 'isContinue', 'value' => false])?>

    <!-- 検索フラグ -->
    <?=$this->Form->hidden('searchFlag', ['value' => (!$this->request->data('searchFlag') ? 'false' : $this->request->data('searchFlag'))])?>

    <!-- 選択した成績情報の年度 -->
    <?=$this->Form->hidden('selectScoreId', ['id' => 'selectScoreId'])?>
    <?=$this->Form->hidden('selectYear', ['id' => 'selectYear'])?>
    <?=$this->Form->hidden('selectWinPoint', ['id' => 'selectWinPoint'])?>
    <?=$this->Form->hidden('selectLosePoint', ['id' => 'selectLosePoint'])?>
    <?=$this->Form->hidden('selectDrawPoint', ['id' => 'selectDrawPoint'])?>
    <?=$this->Form->hidden('selectWinPointWr', ['id' => 'selectWinPointWr'])?>
    <?=$this->Form->hidden('selectLosePointWr', ['id' => 'selectLosePointWr'])?>
    <?=$this->Form->hidden('selectDrawPointWr', ['id' => 'selectDrawPointWr'])?>

    <section id="detail">
        <!-- 棋士マスタ情報 -->
        <table class="detail">
            <tr class="headerRow1">
                <td colspan="4">
                    棋士情報<?=($player->ID ? '（ID：'.h($player->ID).'）' : '')?>
                    <?=$this->Form->hidden('selectPlayerId', ['value' => h($player->ID)])?>
                </td>
            </tr>
            <tr>
                <td class="right detailColumn1">所属国：</td>
                <td class="detailColumn2">
                    <?=h($player->country->NAME)?>
                    <?=$this->Form->hidden('selectCountry', ['value' => $player->country->ID])?>
                </td>
                <td class="right detailColumn1">所属組織：</td>
                <td class="detailColumn2">
                    <?=
                        $this->Form->text('affiliation', [
                            'value' => h($player->AFFILIATION),
                            'class' => 'affiliation',
                            'maxlength' => 10
                        ]);
                    ?>
                </td>
            </tr>
            <tr>
                <td class="right detailColumn1">棋士名：</td>
                <td class="detailColumn2">
                    <?=
                        $this->Form->text('playerName', [
                            'value' => h($player->NAME),
                            'class' => 'playerName',
                            'maxlength' => 20
                        ]);
                    ?>
                </td>
                <td class="right detailColumn1">（英語）：</td>
                <td class="detailColumn2">
                    <?=
                        $this->Form->text('playerNameEn', [
                            'value' => h($player->NAME_ENGLISH),
                            'class' => 'imeDisabled playerName',
                            'maxlength' => 40
                        ]);
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
                <td class="right detailColumn1">（その他）：</td>
                <td class="detailColumn2">
                    <?=
                        $this->Form->text('playerNameOther', [
                            'value' => h($player->NAME_OTHER),
                            'class' => 'playerName',
                            'maxlength' => 40
                        ]);
                    ?>
                </td>
            </tr>
            <tr>
                <td class="right detailColumn1">生年月日：</td>
                <td class="detailColumn2">
                    <?php
                        $birthday = '';
                        $age = '不明';
                        if ($player->BIRTHDAY) {
                            $birthday = $this->Date->format($player->BIRTHDAY, 'YYYY/MM/dd');
                            $age = intval(((date('Ymd') - $this->Date->format($player->BIRTHDAY, 'YYYYMMdd')) / 10000)).'歳';
                        }
                        echo $this->Form->text('birthday', [
                            'value' => $birthday,
                            'class' => 'imeDisabled datepicker birthday',
                            'readonly' => true
                        ]);
                    ?>
                    <span class="age">（<?=$age?>）</span>
                </td>
                <td class="right detailColumn1">性別：</td>
                <td class="detailColumn2">
                    <?php
                        if (!$player->ID) {
                            echo $this->Form->input('sexes', [
                                'options' => array(
                                    '男性' => '男性',
                                    '女性' => '女性'
                                ),
                                'name' => 'sex',
                                'class' => 'sex'
                            ]);
                        } else {
                            echo h($player->SEX);
                            echo $this->Form->hidden('sex', ['value' => h($player->SEX)]);
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <td class="right detailColumn1">入段日：</td>
                <td class="detailColumn2">
                    <?php
                        if (!$player->ID) {
                            echo $this->Form->text('enrollment', [
                                'value' => '',
                                'class' => 'imeDisabled'
                            ]);
                        } else {
                            echo $this->Date->formatJpValue($player->ENROLLMENT);
                            echo $this->Form->hidden('enrollment', ['value' => h($player->ENROLLMENT)]);
                        }
                    ?>
                </td>
                <td class="right detailColumn1">段位：</td>
                <td class="detailColumn2">
                    <?=
                        $this->Form->input('rank', [
                            'options' => $ranks,
                            'class' => 'rank',
                            'value' => ($player->RANK_ID ? $player->RANK_ID : '1')
                        ]);
                    ?>
                </td>
            </tr>
            <tr>
                <td class="right detailColumn1">引退フラグ：</td>
                <td colspan="3">
                    <?=$this->Form->checkbox('retireFlag', ['checked' => ($player->DELETE_FLAG)])?>
                </td>
            </tr>
            <tr>
                <td class="right detailColumn1">更新日時：</td>
                <td colspan="2">
                    <?=$this->Date->formatToDateTime($player->MODIFIED)?>
                    <?=$this->Form->hidden('lastUpdatePlayer', ['value' => $this->Date->format($player->MODIFIED, 'yyyyMMddHHmmss')])?>
                </td>
                <td class="right">
                    <?php
                        echo $this->Form->button(($player->ID ? '更新' : '登録'), [
                            'id' => 'save',
                            'type' => 'button'
                        ]);
                        if (!$player->ID) {
                            echo $this->Form->button('連続作成', [
                                'id' => 'saveWithContinue',
                                'type' => 'button'
                            ]);
                        }
                    ?>
                </td>
            </tr>
        </table>

        <!-- 棋士成績情報 -->
        <table class="detail">
            <tr class="headerRow1">
                <td colspan="4">成績情報</td>
            </tr>
            <?php if (!empty($player->player_scores)) { ?>
            <?php foreach ($player->player_scores as $key=>$score) :?>
                <tr class="headerRow2">
                    <td colspan="4">
                        <?=h($score->TARGET_YEAR).'年度'?>
                        <input type="hidden" name="year" value="<?=h($score->TARGET_YEAR)?>" id="year_<?=$key?>">
                    </td>
                </tr>
                <tr>
                    <td class="right detailColumn1">段位：</td>
                    <td colspan="3">
                        <?=h($score->rank->NAME)?><br/>
                    </td>
                </tr>
                <tr>
                    <td class="right detailColumn1">勝敗（国内）：</td>
                    <td class="detailColumn2">
                        <?=$this->Form->hidden('scoreId_'.$key, ['id' => 'scoreId_'.$key, 'value' => h($score->ID)])?>
                        <?=
                            $this->Form->input('winPoint_'.$key, [
                                'id' => 'winPoint_'.$key,
                                'name' => 'winPoint',
                                'value' => h($score->WIN_POINT),
                                'class' => 'playerPoint imeDisabled'
                            ]).'勝';
                        ?>
                        <?=
                            $this->Form->input('losePoint_'.$key, [
                                'id' => 'losePoint_'.$key,
                                'name' => 'losePoint',
                                'value' => h($score->LOSE_POINT),
                                'class' => 'playerPoint imeDisabled'
                            ]).'敗';
                        ?>
                        <?=
                            $this->Form->input('drawPoint_'.$key, [
                                'id' => 'drawPoint_'.$key,
                                'name' => 'drawPoint',
                                'value' => h($score->DRAW_POINT),
                                'class' => 'playerPoint imeDisabled'
                            ]).'分';
                        ?>
                        （勝率<span class="winPercent" id="winPercent_<?=$key?>">65</span>%）
                    </td>
                    <td class="right detailColumn1">（国際）：</td>
                    <td class="detailColumn2">
                        <?=
                            $this->Form->input('winPointWr_'.$key, [
                                'id' => 'winPointWr_'.$key,
                                'name' => 'winPointWr',
                                'value' => h($score->WIN_POINT_WORLD),
                                'class' => 'playerPoint imeDisabled'
                            ]).'勝';
                        ?>
                        <?=
                            $this->Form->input('losePointWr_'.$key, [
                                'id' => 'losePointWr_'.$key,
                                'name' => 'losePointWr',
                                'value' => h($score->LOSE_POINT_WORLD),
                                'class' => 'playerPoint imeDisabled'
                            ]).'敗';
                        ?>
                        <?=
                            $this->Form->input('drawPointWr_'.$key, [
                                'id' => 'drawPointWr_'.$key,
                                'name' => 'drawPointWr',
                                'value' => h($score->DRAW_POINT_WORLD),
                                'class' => 'playerPoint imeDisabled'
                            ]).'分';
                        ?>
                        （勝率<span class="winPercentWr" id="winPercentWr_<?=$key?>">0</span>%）
                    </td>
                </tr>
                <tr>
                    <td class="right detailColumn1">更新日時：</td>
                    <td colspan="2">
                        <?=$this->Date->formatToDateTime($score->MODIFIED)?>
                        <?=$this->Form->hidden('lastUpdate_'.$key, ['id' => 'lastUpdate_'.$key, 'value' => $this->Date->format($score->MODIFIED, 'YmdHis')])?>
                    </td>
                    <td class="right">
                        <?=
                            $this->Form->button('更新', [
                                'id' => 'updateScore_'.$key,
                                'class' => 'updateScore',
                                'type' => 'button'
                            ]);
                        ?>
                    </td>
                </tr>
            <?php endforeach ?>
            <?php }?>
        </table>

        <!-- タイトル保持情報 -->
        <table class="detail">
            <tr class="headerRow1">
                <td colspan="6">タイトル保持情報</td>
            </tr>
            <?php if (!empty($player->title_retains)) { ?>
            <?php $beforeYear = ''; ?>
            <?php foreach ($player->title_retains as $key=>$titleRetain) : ?>
                <?php if ($beforeYear != $titleRetain->TARGET_YEAR) { ?>
                <tr class="headerRow2">
                    <td colspan="6">
                        <?=h($titleRetain->TARGET_YEAR).'年度'?>
                    </td>
                </tr>
                <?php $beforeYear = $titleRetain->TARGET_YEAR; ?>
                <?php } ?>
                <tr>
                    <td class="detailColumn1 right">
                        <?=h($titleRetain->title->NAME).'<br/>（'.h($titleRetain->title->country->NAME).'棋戦）'?>
                    </td>
                    <td class="detailColumn2">
                        <?=h($titleRetain->HOLDING).'期'?>
                    </td>
                    <td colspan="3" class="right detailColumn1">更新日時：</td>
                    <td>
                        <?=$this->Date->formatToDateTime($titleRetain->MODIFIED)?><br/>
                    </td>
                </tr>
            <?php endforeach ?>
            <?php }?>
        </table>
    </section>
<?=$this->Form->end()?>
<script type="text/javascript">
    $(function() {
        // 国内棋戦、国際棋戦の勝率を設定
        $('.winPercent').each(function () {
            var index = $(this).attr('id').split('_')[1];
            setPercent(index);
        });
        $('.winPercentWr').each(function () {
            var index = $(this).attr('id').split('_')[1];
            setPercentWr(index);
        });

        // 勝数、敗数変更時に勝率を再設定
        $('[name=winPoint], [name=losePoint]').blur(function() {
            var index = $(this).attr('id').split('_')[1];
            setPercent(index);
        });
        $('[name=winPointWr], [name=losePointWr]').blur(function() {
            var index = $(this).attr('id').split('_')[1];
            setPercentWr(index);
        });

        // 登録・更新ボタン押下時
        $('#save, #saveWithContinue').click(function() {
            $('#mainForm').attr('action', '<?=$this->Url->build(['action' => 'save'])?>');
            if ($(this).attr('id') === 'saveWithContinue') {
                $('#isContinue').val(true);
            }
            var confirm = $("#confirm");
            confirm.html('棋士マスタを' + $(this).text() + 'します。よろしいですか？');
            confirm.click();
        });

        // 棋士成績情報更新ボタン押下時
        $('.updateScore').click(function() {
            var index = $(this).attr('id').split('_')[1];

            // 更新処理
            $('#selectScoreId').val($('#scoreId_' + index).val());
            $('#selectYear').val($('#year_' + index).val());
            $('#selectWinPoint').val($('#winPoint_' + index).val());
            $('#selectLosePoint').val($('#losePoint_' + index).val());
            $('#selectDrawPoint').val($('#drawPoint_' + index).val());
            $('#selectWinPointWr').val($('#winPointWr_' + index).val());
            $('#selectLosePointWr').val($('#losePointWr_' + index).val());
            $('#selectDrawPointWr').val($('#drawPointWr_' + index).val());

            $('#mainForm').attr('action', '<?=$this->Url->build(['action' => 'update-score'])?>');
            var confirm = $("#confirm");
            confirm.html('棋士成績情報を更新します。よろしいですか？');
            confirm.click();
        });
    });

    // 国内棋戦の勝率を設定
    function setPercent(index) {
        // 国内棋戦
        var totalPoint = Number($('#winPoint_' + index).val()) + Number($('#losePoint_' + index).val());
        var winPercent = totalPoint === 0 ? 0 : Math.round(Number($('#winPoint_' + index).val() / totalPoint) * 100);
        $('#winPercent_' + index).html(winPercent);
    }

    // 国際棋戦の勝率を設定
    function setPercentWr(index) {
        // 国際棋戦
        var totalPointWr = Number($('#winPointWr_' + index).val()) + Number($('#losePointWr_' + index).val());
        var winPercentWr = totalPointWr === 0 ? 0 : Math.round(Number($('#winPointWr_' + index).val() / totalPointWr) * 100);
        $('#winPercentWr_' + index).html(winPercentWr);
    }
</script>
