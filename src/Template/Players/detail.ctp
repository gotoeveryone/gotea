<div class="detail-dialog">
    <!-- タブ -->
    <ul id="tabs" class="tabs">
        <li class="tab" name="player">棋士情報</li>
        <?php if ($player->id) : ?>
            <li class="tab" name="ranks">昇段情報</li>
            <li class="tab" name="scores">成績情報</li>
            <?php if (!$player->retention_histories) : ?>
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
                'class' => 'mainForm',
                'type' => 'post',
                'url' => ['action' => 'save'],
                'templates' => [
                    'inputContainer' => '{{content}}',
                    'textFormGroup' => '{{input}}',
                    'selectFormGroup' => '{{input}}'
                ]
            ])?>
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
                                <?=
                                    $this->cell('Organizations', [
                                        'empty' => false,
                                        'value' => ($player->organization_id ? $player->organization_id : '1'),
                                    ])->render()
                                ?>
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row">引退フラグ</div>
                            <div class="input-row">
                                <?=$this->Form->checkbox('is_retired', ['id' => 'retired'])?>
                                <?= $this->Form->label('retired', '引退しました') ?>
                                <?= $this->Form->text('retired', ['class' => 'datepicker']) ?>
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
                                <?=
                                    $this->Form->text('birthday', [
                                        'class' => 'imeDisabled datepicker birthday'
                                    ]);
                                ?>
                                <span class="age">（<?=(is_numeric($player->age) ? $player->age.'歳' : '不明')?>）</span>
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
                                    echo $this->Date->formatJpValue($player->joined);
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
                                    echo $this->MyForm->sexes(['class' => 'sex']);
                                } else {
                                    echo h($player->sex);
                                    echo $this->Form->hidden('sex');
                                }
                                ?>
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row">段位</div>
                            <div class="input-row">
                                <?= $this->cell('Ranks', [
                                    'empty' => false,
                                    'value' => ($player->rank_id ? $player->rank_id : '1'),
                                ])->render() ?>
                            </div>
                        </div>
                        <div class="box">
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
                        <?php // 新規登録時は続けて登録チェックボックス表示 ?>
                        <?php if (!$player->id) : ?>
                            <?= $this->Form->checkbox('is_continue', ['id' => 'continue']) ?>
                            <?= $this->Form->label('continue', '続けて登録') ?>
                        <?php endif ?>
                        <?= $this->Form->button(($player->id ? '更新' : '登録'), [
                            'data-button-type' => 'player',
                            'type' => 'button',
                            'value' => 'save'
                        ]) ?>
                    </li>
                </ul>
            <?=$this->Form->end()?>
            <?php $this->MyHtml->scriptStart(['inline' => false, 'block' => 'script']); ?>
            <script>
                $(function() {
                    // 登録・更新ボタン押下時
                    $("[data-button-type=player]").click(function() {
                        openConfirm('棋士情報を' + $(this).text() + 'します。よろしいですか？');
                    });
                });
            </script>
            <?php $this->MyHtml->scriptEnd(); ?>

        </section>

        <!-- 昇段情報 -->
        <section id="ranks">
            <div class="category-row">昇段情報</div>
            <?= $this->Form->create($player, [
                'class' => 'rank-form',
                'type' => 'post',
                'url' => ['controller' => 'player-ranks', 'action' => 'add'],
                'templates' => [
                    'inputContainer' => '{{content}}',
                    'textFormGroup' => '{{input}}',
                    'selectFormGroup' => '{{input}}',
                ]
            ]) ?>
                <?=$this->Form->hidden('player_id', ['value' => $player->id])?>
                <ul class="boxes">
                    <li class="row">
                        <div class="box">
                            <div class="label-row">新規登録</div>
                            <div class="input-row">
                                <?= $this->cell('Ranks', ['empty' => false, 'value' => $player->rank_id])->render() ?>
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row"></div>
                            <div class="input-row">
                                昇段日：<?= $this->Form->text('promoted', ['class' => 'datepicker']) ?>
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row"></div>
                            <div class="input-row">
                                <?= $this->Form->checkbox('newest', ['id' => 'newest']) ?>
                                <?= $this->Form->label('newest', '最新として登録') ?>
                                <div class="button-wrap">
                                    <?= $this->Form->button('登録', ['class' => 'add-ranks']) ?>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="row">
                        <?php if (!empty($player->player_ranks)) : ?>
                        <div class="box">
                            <div class="label-row">昇段履歴</div>
                            <?php foreach ($player->player_ranks as $player_rank) : ?>
                                <div class="input-row">
                                    <?= h($player_rank->rank->name) ?>
                                    <?= h($player_rank->promoted) ?>
                                </div>
                            <?php endforeach ?>
                        </div>
                        <?php endif ?>
                    </li>
                </ul>
            <?= $this->Form->end() ?>
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
                                    $win = $player->win($scores, $year);
                                    $lose = $player->lose($scores, $year);
                                ?>
                                <?=$win?>勝<?=$lose?>敗<?=$player->draw($scores, $year)?>分
                                <span class="percent">（勝率<strong><?=$this->MyForm->percent($win, $lose)?></strong>%）</span>
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row">勝敗（国際）</div>
                            <div class="input-row">
                                <?php
                                    $winWr = $player->win($scores, $year, true);
                                    $loseWr = $player->lose($scores, $year, true);
                                ?>
                                <?=$winWr?>勝<?=$loseWr?>敗<?=$player->draw($scores, $year, true)?>分
                                <span class="percent">（勝率<strong><?=$this->MyForm->percent($winWr, $loseWr)?></strong>%）</span>
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row"></div>
                            <div class="input-row">
                                <div class="button-wrap">
                                    <?= $this->Form->postButton('タイトル成績へ', [
                                        'controller' => 'TitleScores', 'action' => 'index',
                                    ], [
                                        'data' => [
                                            'player_id' => $player->id,
                                            'target_year' => $year,
                                            'modal' => true,
                                        ],
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            <?php endforeach ?>

            <?php // 2016年以前 ?>
            <?php foreach ($player->old_scores as $key => $score) : ?>
                <ul class="boxes">
                    <li class="genre-row"><?=h($score->target_year).'年度'?></li>
                    <li class="row">
                        <div class="box">
                            <div class="label-row">勝敗（国内）</div>
                            <div class="input-row">
                                <?=h($score->win_point)?>勝<?=h($score->lose_point)?>敗<?=h($score->draw_point)?>分
                                <span class="percent">（勝率<strong><?=$this->MyForm->percent($score->win_point, $score->lose_point)?></strong>%）</span>
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row">勝敗（国際）</div>
                            <div class="input-row">
                                <?=$score->win_point_world?>勝<?=$score->lose_point_world?>敗<?=$score->draw_point_world?>分
                                <span class="percent">（勝率<strong><?=$this->MyForm->percent($score->win_point_world, $score->lose_point_world)?></strong>%）</span>
                            </div>
                        </div>
                        <div class="box">
                                <div class="button-wrap">
                                    <?= $this->Form->postButton('タイトル成績へ', [
                                        'controller' => 'TitleScores', 'action' => 'index',
                                    ], [
                                        'data' => [
                                            'player_id' => $player->id,
                                            'target_year' => $score->target_year,
                                            'modal' => true,
                                        ],
                                    ]) ?>
                                </div>
                            <div class="label-row">段位</div>
                            <div class="input-row"><?=h($score->rank->name)?></div>
                        </div>
                    </li>
                </ul>
            <?php endforeach ?>
        </section>

        <!-- タイトル取得履歴 -->
        <section id="titleRetains">
            <div class="category-row">タイトル取得履歴</div>

            <?php foreach ($player->groupByYearFromHistories() as $key => $items) : ?>
                <div class="genre-row"><?=h($key).'年度'?></div>
                <?php foreach ($items as $item) : ?>
                <div class="input-row">
                    <?=h($item->holding).'期'.h($item->title->name)?>
                    <?="（{$item->title->country->name}棋戦）"?>
                </div>
                <?php endforeach ?>
            <?php endforeach ?>
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

        // 引退フラグにチェックされていれば引退日の入力欄を設定可能に
        var setRetired = function() {
            if ($('#retired').prop('checked')) {
                $('[name=retired]').removeAttr('disabled');
            } else {
                $('[name=retired]').attr('disabled', true).val('');
            }
        };

        $('#retired').on('click', function() {
            setRetired();
        });
        setRetired();
    });
</script>
<?php $this->MyHtml->scriptEnd(); ?>
