<div class="detail-dialog">
    <!-- タブ -->
    <ul id="tabs" class="tabs">
        <li class="tab" name="player">棋士情報</li>
        <?php if ($player->id) : ?>
            <li class="tab" name="scores">成績情報</li>
            <?php if (!empty($player->retention_histories)) : ?>
            <li class="tab" name="titleRetains">タイトル取得情報</li>
            <?php endif ?>
        <?php endif ?>
    </ul>

    <!-- 詳細 -->
    <div class="detail">
        <!-- 棋士成績 -->
        <section id="player">
            <?=$this->Form->create($player, [
                'id' => 'mainForm',
                'type' => 'post',
                'url' => ['action' => 'save', $player->id],
                'templates' => [
                    'inputContainer' => '{{content}}',
                    'textFormGroup' => '{{input}}',
                    'selectFormGroup' => '{{input}}'
                ]
            ])?>
                <?=$this->Form->hidden('is_continue', ['id' => 'isContinue', 'value' => false])?>
                <?=$this->Form->hidden('id', ['value' => $player->id])?>
                <?=$this->Form->hidden('optimistic_key', ['value' => $this->Date->format($player->modified, 'YYYYMMddHHmmss')])?>
                <?=$this->Form->hidden('country_id')?>
                <div class="category-row">棋士情報<?=($player->id ? '（ID：'.h($player->id).'）' : "")?></div>
                <ul class="boxes">
                    <li class="row">
                        <div class="box">
                            <div class="label-row">所属国</div>
                            <div class="input-row"><?=h($player->country->name)?></div>
                        </div>
                        <div class="box">
                            <div class="label-row"><span>所属組織</span></div>
                            <div class="input-row">
                                <span>
                                    <?=
                                        $this->Form->select('organization_id', $organizations, [
                                            'class' => 'organization',
                                            'value' => ($player->organization_id ? $player->organization_id : '1')
                                        ]);
                                    ?>
                                </span>
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row">引退フラグ</div>
                            <div class="input-row">
                                <?=$this->Form->checkbox('is_retired', ['id' => 'retired'])?>
                                <label for="retired">引退しました</label>
                            </div>
                        </div>
                    </li>
                    <li class="row">
                        <div class="box">
                            <div class="label-row">棋士名</div>
                            <div class="input-row">
                                <?=
                                    $this->Form->text('name', [
                                        'class' => 'playerName',
                                        'maxlength' => 20
                                    ]);
                                ?>
                                英語
                                <?=
                                    $this->Form->text('name_english', [
                                        'class' => 'playerName',
                                        'maxlength' => 40
                                    ]);
                                ?>
                                その他
                                <?=
                                    $this->Form->text('name_other', [
                                        'class' => 'playerName',
                                        'maxlength' => 20
                                    ]);
                                ?>
                            </div>
                        </div>
                    </li>
                    <li class="row">
                        <div class="box">
                            <div class="label-row">生年月日</div>
                            <div class="input-row">
                                <?php
                                    if (is_numeric($age = $player->getAge())) {
                                        $age .= '歳';
                                    }
                                    echo $this->Form->text('birthday', [
                                        'class' => 'imeDisabled datepicker birthday'
                                    ]);
                                ?>
                                <span class="age">（<?=($age ? $age : '不明')?>）
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row">入段日</div>
                            <div class="input-row">
                                <?php
                                    if (!$player->id) {
                                        echo $this->Form->text('joined', [
                                            'class' => 'imeDisabled datepicker'
                                        ]);
                                    } else {
                                        echo ''.$this->Date->formatJpValue($player->joined).'';
                                        echo $this->Form->hidden('joined');
                                    }
                                ?>
                            </div>
                        </div>
                    </li>
                    <li class="row">
                        <div class="box">
                            <div class="label-row">性別</div>
                            <div class="input-row">
                                <?php
                                    if (!$player->id) {
                                        echo $this->Form->select('sex', [
                                            '男性' => '男性',
                                            '女性' => '女性'
                                        ], [
                                            'class' => 'sex'
                                        ]);
                                    } else {
                                        echo ''.h($player->sex).'';
                                        echo $this->Form->hidden('sex');
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row">段位</div>
                            <div class="input-row">
                                <?=
                                    $this->Form->select('rank_id', $ranks, [
                                        'class' => 'rank',
                                        'value' => ($player->rank_id ? $player->rank_id : '1')
                                    ]);
                                ?>
                            </div>
                        </div>
                        <div class="box2">
                            <div class="label-row">更新日時</div>
                            <div class="input-row">
                                <?=$player->modified ? $this->Date->formatToDateTime($player->modified) : ''?>
                                <?=
                                    $this->Form->hidden('modified', ['value' => $this->Date->format($player->modified, 'yyyyMMddHHmmss')])
                                ?>
                            </div>
                        </div>
                    </li>
                    <li class="row">
                        <div class="box">
                            <div class="label-row">その他備考</div>
                            <div class="input-row">
                                <?=$this->Form->textarea('remarks', ['class' => 'remarks'])?>
                            </div>
                        </div>
                    </li>
                    <li class="button-row">
                        <?php
                            echo $this->Form->button(($player->id ? '更新' : '登録'), [
                                'data-button-type' => 'player',
                                'type' => 'button',
                                'value' => 'save'
                            ]);
                            if (!$player->id) {
                                echo $this->Form->button('連続作成', [
                                    'data-button-type' => 'player',
                                    'type' => 'button',
                                    'value' => 'saveWithContinue'
                                ]);
                            }
                        ?>
                    </li>
                </ul>
            <?=$this->Form->end()?>
            <?php $this->MyHtml->scriptStart(['inline' => false, 'block' => 'script']); ?>
            <script>
                $(function() {
                    // 登録・更新ボタン押下時
                    $("[data-button-type=player]").click(function() {
                        if ($(this).val() === 'saveWithContinue') {
                            $('#isContinue').val(true);
                        }
                        openConfirm('棋士情報を' + $(this).text() + 'します。よろしいですか？');
                    });
                });
            </script>
            <?php $this->MyHtml->scriptEnd(); ?>

        </section>

        <!-- 棋士成績 -->
        <?php if ($player->id) : ?>
        <section id="scores">
            <div class="category-row">勝敗</div>

            <?php // 2017年以降 ?>
            <?php foreach ($player->years() as $year) : ?>
                <ul class="boxes">
                    <li class="genre-row"><?=h($year).'年度'?></li>
                    <li class="row">
                        <div class="box">
                            <div class="label-row">勝敗（国内）</div>
                            <div class="input-row">
                                <?php
                                    $win = $player->win($year);
                                    $lose = $player->lose($year);
                                ?>
                                <?=$win?>勝<?=$lose?>敗<?=$player->draw($year)?>分
                                <span class="percent">（勝率<strong><?=$this->MyForm->percent($win, $lose)?></strong>%）
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row">勝敗（国際）</div>
                            <div class="input-row">
                                <?php
                                    $winWr = $player->win($year, true);
                                    $loseWr = $player->lose($year, true);
                                ?>
                                <?=$winWr?>勝<?=$loseWr?>敗<?=$player->draw($year, true)?>分
                                <span class="percent">（勝率<strong><?=$this->MyForm->percent($winWr, $loseWr)?></strong>%）
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row"></div>
                            <div class="input-row">
                                <div class="button-wrap">
                                    <?=$this->Form->button('タイトル成績へ', [
                                        'data-button-type' => 'title-scores', 'data-year' => $year, 'type' => 'button'
                                    ])?>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            <?php endforeach ?>

            <?php // 2016年以前 ?>
            <?php foreach ($player->player_scores as $key=>$score) : ?>
                <ul class="boxes">
                    <li class="genre-row"><?=h($score->target_year).'年度'?></li>
                    <li class="row">
                        <div class="box">
                            <div class="label-row">勝敗（国内）</div>
                            <div class="input-row">
                                <?=h($score->win_point)?>勝<?=h($score->lose_point)?>敗<?=h($score->draw_point)?>分
                                <span class="percent">（勝率<strong><?=$this->MyForm->percent($score->win_point, $score->lose_point)?></strong>%）
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row">勝敗（国際）</div>
                            <div class="input-row">
                                <?=$score->win_point_world?>勝<?=$score->lose_point_world?>敗<?=$score->draw_point_world?>分
                                <span class="percent">（勝率<strong><?=$this->MyForm->percent($score->win_point_world, $score->lose_point_world)?></strong>%）
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row">段位</div>
                            <div class="input-row"><?=h($score->rank->name)?></div>
                        </div>
                    </li>
                </ul>
            <?php endforeach ?>

            <?=$this->Form->create(null, [
                'id' => 'titleScoreForm',
                'type' => 'post',
                'url' => ['controller' => 'title-scores', 'action' => 'modal-search'],
                'templates' => [
                    'inputContainer' => '{{content}}',
                    'textFormGroup' => '{{input}}',
                    'selectFormGroup' => '{{input}}'
                ]
            ])?>
                <?=$this->Form->hidden('player_id', ['value' => $player->id])?>
                <?=$this->Form->hidden('target_year', ['value' => ''])?>
            <?=$this->Form->end()?>
        </section>

        <!-- タイトル取得履歴 -->
        <section id="titleRetains">
            <div class="category-row">タイトル取得履歴</div>

            <?php if (!empty($player->retention_histories)) : ?>
            <?php $beforeYear = 0; ?>
            <?php foreach ($player->retention_histories as $retention_history) : ?>
                <?php if ($beforeYear !== $retention_history->target_year) : ?>
                <div class="genre-row"><?=h($retention_history->target_year).'年度'?></div>
                <?php endif ?>
                <div class="input-row">
                    <?=h($retention_history->holding).'期'.h($retention_history->title->name)?>
                    <?="（{$retention_history->title->country->name}棋戦）"?>
                </div>
                <?php $beforeYear = $retention_history->target_year; ?>
            <?php endforeach ?>
            <?php endif ?>
        </section>
        <?php endif ?>
    </div>
</div>

<?php $this->MyHtml->scriptStart(['inline' => false, 'block' => 'script']); ?>
<script>
    $(function() {
        // タブ選択
        selectTab('<?=($tab ?? '')?>');
        // タイトル成績へボタン押下時
        $("[data-button-type=title-scores]").click(function() {
            var form = $('#titleScoreForm');
            form.find('[name=target_year]').val($(this).data('year'));
            submitForm(form);
        });
    });
</script>
<?php $this->MyHtml->scriptEnd(); ?>
