<?=$this->Form->create(null, [
    'id' => 'mainForm',
    'method' => 'post',
    'url' => ['action' => 'detail'],
    'templates' => [
        'inputContainer' => '{{content}}',
        'textFormGroup' => '{{input}}',
        'selectFormGroup' => '{{input}}'
    ]
])?>
<section id="detail">
    <section id="tabs">
        <section class="tab" name="player">棋士情報</section>
        <?php if ($player->id) : ?>
            <section class="tab" name="scores">成績情報</section>
            <?php if (!empty($player->retention_histories)) : ?>
            <section class="tab" name="titleRetains">タイトル取得情報</section>
            <?php endif ?>
        <?php endif ?>
    </section>
    <section id="scroll">
        <!-- 棋士成績 -->
        <section id="player" class="details">
            <!-- 連続作成フラグ -->
            <?=$this->Form->hidden('isContinue', ['id' => 'isContinue', 'value' => false])?>
            <section class="row category">
                <span>棋士情報<?=($player->id ? "（ID：{$player->id}）" : "")?></span>
                <?=$this->Form->hidden('selectId', ['value' => h($player->id)])?>
            </section>
            <hr>
            <section class="row">
                <section class="box3">
                    <section class="row key">所属国</section>
                    <section class="row value">
                        <?=h($player->country->name)?>
                        <?=$this->Form->hidden('selectCountry', ['value' => $player->country->id])?>
                    </section>
                </section>
                <section class="box3">
                    <section class="row key">所属組織</section>
                    <section class="row value">
                        <?=
                            $this->Form->input('organization', [
                                'options' => $organizations,
                                'class' => 'organization',
                                'value' => ($player->organization ? $player->organization->id : '1')
                            ]);
                        ?>
                    </section>
                </section>
                <section class="box3">
                    <section class="row key">引退フラグ</section>
                    <section class="row value">
                        <?=$this->Form->checkbox('retired', ['checked' => ($player->is_retired)])?>
                    </section>
                </section>
            </section>
            <section class="row">
                <section class="box">
                    <section class="row key">棋士名</section>
                </section>
                <section class="box2">
                    <section class="row value">
                        <?=
                            $this->Form->text('playerName', [
                                'value' => h($player->name),
                                'class' => 'playerName',
                                'maxlength' => 20
                            ]);
                        ?>
                    </section>
                </section>
                <section class="box2">
                    <section class="row value">
                        英語
                        <?=
                            $this->Form->text('playerNameEnglish', [
                                'value' => h($player->name_english),
                                'class' => 'imeDisabled playerName',
                                'maxlength' => 40
                            ]);
                        ?>
                    </section>
                    <section class="row value">
                        その他
                        <?=
                            $this->Form->text('playerNameOther', [
                                'value' => h($player->name_other),
                                'class' => 'playerName',
                                'maxlength' => 40
                            ]);
                        ?>
                    </section>
                </section>
            </section>
            <section class="row">
                <section class="box2">
                    <section class="row key">生年月日</section>
                    <section class="row value">
                        <?php
                            $birthday = $player->getBirthday();
                            $age = '不明';
                            if ($birthday) {
                                $age = intval(((date('Ymd') - $player->getBirthday('Ymd')) / 10000)).'歳';
                            }
                            echo $this->Form->text('birthday', [
                                'value' => $birthday,
                                'class' => 'imeDisabled datepicker birthday'
                            ]);
                        ?>
                        <span class="age">（<?=$age?>）</span>
                    </section>
                </section>
                <section class="box2">
                    <section class="row key">入段日</section>
                    <section class="row value">
                        <?php
                            if (!$player->id) {
                                echo $this->Form->text('joined', [
                                    'value' => $player->joined,
                                    'class' => 'imeDisabled datepicker'
                                ]);
                            } else {
                                echo $this->Date->formatJpValue($player->joined);
                                echo $this->Form->hidden('joined', ['value' => h($player->joined)]);
                            }
                        ?>
                    </section>
                </section>
            </section>
            <section class="row">
                <section class="box4">
                    <section class="row key">性別</section>
                    <section class="row value">
                        <?php
                            if (!$player->id) {
                                echo $this->Form->input('sexes', [
                                    'options' => array(
                                        '男性' => '男性',
                                        '女性' => '女性'
                                    ),
                                    'name' => 'sex',
                                    'class' => 'sex'
                                ]);
                            } else {
                                echo h($player->sex);
                                echo $this->Form->hidden('sex', ['value' => h($player->sex)]);
                            }
                        ?>
                    </section>
                </section>
                <section class="box4">
                    <section class="row key">段位</section>
                    <section class="row value">
                        <?=
                            $this->Form->input('rank', [
                                'options' => $ranks,
                                'class' => 'rank',
                                'value' => ($player->rank ? $player->rank->id : '1')
                            ]);
                        ?>
                    </section>
                </section>
                <section class="box2">
                    <section class="row key">更新日時</section>
                    <section class="row value">
                        <?=$this->Date->formatToDateTime($player->modified)?>
                        <?=
                            $this->Form->hidden('lastUpdatePlayer', [
                                'value' => $this->Date->format($player->modified, 'yyyyMMddHHmmss')
                            ])
                        ?>
                    </section>
                </section>
            </section>
            <section class="row">
                <section class="box">
                    <section class="row key">その他備考</section>
                    <section class="row value">
                        <?=
                            $this->Form->textarea('remarks', [
                                'cols' => 30,
                                'rows' => 3,
                                'class' => 'remarks',
                                'value' => h($player->remarks)
                            ])
                        ?>
                    </section>
                </section>
            </section>
            <section class="row button">
                <?php
                    echo $this->Form->button(($player->id ? '更新' : '登録'), [
                        'id' => 'save',
                        'type' => 'button'
                    ]);
                    if (!$player->id) {
                        echo $this->Form->button('連続作成', [
                            'id' => 'saveWithContinue',
                            'type' => 'button'
                        ]);
                    }
                ?>
            </section>
        </section>

        <!-- 棋士成績 -->
        <section id="scores" class="details">
            <!-- 選択した成績情報の年度 -->
            <?=$this->Form->hidden('selectScoreId', ['id' => 'selectScoreId'])?>
            <?=$this->Form->hidden('selectYear', ['id' => 'selectYear'])?>
            <?=$this->Form->hidden('selectWinPoint', ['id' => 'selectWinPoint'])?>
            <?=$this->Form->hidden('selectLosePoint', ['id' => 'selectLosePoint'])?>
            <?=$this->Form->hidden('selectDrawPoint', ['id' => 'selectDrawPoint'])?>
            <?=$this->Form->hidden('selectWinPointWr', ['id' => 'selectWinPointWr'])?>
            <?=$this->Form->hidden('selectLosePointWr', ['id' => 'selectLosePointWr'])?>
            <?=$this->Form->hidden('selectDrawPointWr', ['id' => 'selectDrawPointWr'])?>
            <section class="row category"><span>勝敗</span></section>
            <?php if (!empty($player->player_scores)) : ?>
            <?php foreach ($player->player_scores as $key=>$score) : ?>
            <hr>
            <section class="row group">
                <section class="row">
                    <section class="box">
                        <section class="row genre">
                            <?="{$score->target_year}年度"?>
                            <input type="hidden" name="year" value="<?=h($score->target_year)?>" id="year_<?=$key?>">
                        </section>
                    </section>
                </section>
                <section class="row">
                    <section class="box">
                        <section class="row key">段位</section>
                        <section class="row value">
                            <?=h($score->rank->name)?><br/>
                        </section>
                    </section>
                </section>
                <section class="row">
                    <section class="box2">
                        <section class="row key">勝敗（国内）</section>
                        <section class="row value">
                            <?=$this->Form->hidden("scoreId_{$key}", ['id' => "scoreId_{$key}", 'value' => h($score->id)])?>
                            <?=
                                $this->Form->input("winPoint_{$key}", [
                                    'id' => "winPoint_{$key}",
                                    'name' => 'winPoint',
                                    'value' => h($score->win_point),
                                    'class' => 'playerPoint imeDisabled'
                                ]).'勝';
                            ?>
                            <?=
                                $this->Form->input("losePoint_{$key}", [
                                    'id' => "losePoint_{$key}",
                                    'name' => 'losePoint',
                                    'value' => h($score->lose_point),
                                    'class' => 'playerPoint imeDisabled'
                                ]).'敗';
                            ?>
                            <?=
                                $this->Form->input("drawPoint_{$key}", [
                                    'id' => "drawPoint_{$key}",
                                    'name' => 'drawPoint',
                                    'value' => h($score->draw_point),
                                    'class' => 'playerPoint imeDisabled'
                                ]).'分';
                            ?>
                            （勝率<span class="winPercent" id="winPercent_<?=$key?>">&nbsp;</span>%）
                        </section>
                    </section>
                    <section class="box2">
                        <section class="row key">勝敗（国際）</section>
                        <section class="row value">
                            <?=
                                $this->Form->input("winPointWr_{$key}", [
                                    'id' => "winPointWr_{$key}",
                                    'name' => 'winPointWr',
                                    'value' => h($score->win_point_world),
                                    'class' => 'playerPoint imeDisabled'
                                ]).'勝';
                            ?>
                            <?=
                                $this->Form->input("losePointWr_{$key}", [
                                    'id' => "losePointWr_{$key}",
                                    'name' => 'losePointWr',
                                    'value' => h($score->lose_point_world),
                                    'class' => 'playerPoint imeDisabled'
                                ]).'敗';
                            ?>
                            <?=
                                $this->Form->input("drawPointWr_{$key}", [
                                    'id' => "drawPointWr_{$key}",
                                    'name' => 'drawPointWr',
                                    'value' => h($score->draw_point_world),
                                    'class' => 'playerPoint imeDisabled'
                                ]).'分';
                            ?>
                            （勝率<span class="winPercentWr" id="winPercentWr_<?=$key?>">&nbsp;</span>%）
                        </section>
                    </section>
                </section>
                <section class="row">
                    <section class="box">
                        <section class="row key">更新日時</section>
                    </section>
                    <section class="box2">
                        <section class="row value">
                            <?=$this->Date->formatToDateTime($score->modified)?>
                            <?=
                                $this->Form->hidden("lastUpdate_{$key}", [
                                    'id' => "lastUpdate_{$key}",
                                    'value' => $this->Date->format($score->modified, 'YmdHis')
                                ])
                            ?>
                        </section>
                    </section>
                    <section class="box2 button">
                        <?=
                            $this->Form->button('更新', [
                                'id' => "updateScore_{$key}",
                                'class' => 'updateScore',
                                'type' => 'button'
                            ]);
                        ?>
                    </section>
                </section>
            </section>
            <?php endforeach ?>
            <?php endif ?>
        </section>

        <!-- タイトル取得履歴 -->
        <section id="titleRetains" class="details">
            <section class="row category"><span>タイトル取得履歴</span></section>
            <?php if (!empty($player->retention_histories)) : ?>
            <?php $beforeYear = 0; ?>
            <?php foreach ($player->retention_histories as $retention_history) : ?>
                <?php if ($beforeYear !== $retention_history->target_year) : ?>
                <hr>
                <section class="row group">
                    <section class="row genre">
                        <?=h($retention_history->target_year).'年度'?>
                    </section>
                    <?php endif ?>
                    <section class="row">
                        <section class="box">
                            <section class="row value">
                                <?=h($retention_history->holding).'期'.h($retention_history->title->name)?>
                                <?="（{$retention_history->title->country->name}棋戦）"?>
                            </section>
                        </section>
                    </section>
                    <?php $beforeYear = $retention_history->target_year; ?>
                </section>
            <?php endforeach ?>
            <?php endif ?>
        </section>
    </section>
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
            confirm.html('棋士情報を' + $(this).text() + 'します。よろしいですか？');
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
