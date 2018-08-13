<?= $this->Html->css('view', ['block' => true]) ?>
<div class="detail-dialog">
    <!-- タブ -->
    <ul class="tabs" data-selecttab="<?= $this->request->getQuery('tab') ?>">
        <li class="tab" data-tabname="player">棋士情報</li>
        <?php if (!$player->isNew()) : ?>
            <li class="tab" data-tabname="ranks">昇段情報</li>
            <li class="tab" data-tabname="scores">成績情報</li>
            <?php if (!$player->retention_histories->isEmpty()) : ?>
            <li class="tab" data-tabname="titleRetains">タイトル取得情報</li>
            <?php endif ?>
        <?php endif ?>
    </ul>

    <!-- 詳細 -->
    <div class="detail">
        <!-- 棋士成績 -->
        <section data-contentname="player" class="tab-contents">
            <?= $this->Form->create($player, ['class' => 'main-form', 'url' => $player->getSaveUrl()]) ?>
                <?=$this->Form->hidden('id', ['value' => $player->id])?>
                <?=$this->Form->hidden('country_id')?>
                <div class="category-row">棋士情報<?=($player->id ? '（ID：'.h($player->id).'）' : "")?></div>
                <ul class="boxes">
                    <li class="detail-row">
                        <div class="box">
                            <?php
                                echo $this->cell('Countries', [
                                    'hasTitleOnly' => true,
                                    [
                                        'label' => ['class' => 'label-row', 'text' => '所属国'],
                                        'empty' => false,
                                        'value' => $player->country_id,
                                    ],
                                ])->render()
                            ?>
                        </div>
                        <div class="box">
                            <?php
                                echo $this->cell('Organizations', [
                                    [
                                        'label' => ['class' => 'label-row', 'text' => '所属組織'],
                                        'empty' => false,
                                        'value' => ($player->organization_id ? $player->organization_id : '1'),
                                    ],
                                ])->render()
                            ?>
                        </div>
                        <div class="box">
                            <div class="label-row">引退フラグ</div>
                            <div class="input-row">
                                <?php
                                    echo $this->Form->control('is_retired', [
                                        'label' => ['class' => 'checkbox-label', 'text' => '引退しました'],
                                    ]);
                                    echo $this->Form->control('retired', [
                                        'label' => false,
                                        'type' => 'text',
                                        'class' => 'datepicker',
                                        'disabled' => true,
                                        'placeholder' => '引退日',
                                    ])
                                ?>
                            </div>
                        </div>
                    </li>
                    <li class="detail-row">
                        <div class="box">
                            <?php
                                echo $this->Form->control('name', [
                                    'label' => ['class' => 'label-row', 'text' => '棋士名'],
                                    'class' => 'input-row name',
                                    'maxlength' => 20,
                                ]);
                            ?>
                        </div>
                        <div class="box">
                            <?php
                                echo $this->Form->control('name_english', [
                                    'label' => ['class' => 'label-row', 'text' => '棋士名（英語）'],
                                    'class' => 'input-row name',
                                    'maxlength' => 40,
                                ]);
                            ?>
                        </div>
                        <div class="box">
                            <?php
                                echo $this->Form->control('name_other', [
                                    'label' => ['class' => 'label-row', 'text' => '棋士名（その他）'],
                                    'class' => 'input-row name',
                                    'maxlength' => 20,
                                ]);
                            ?>
                        </div>
                    </li>
                    <li class="detail-row">
                        <div class="box">
                            <div class="label-row">生年月日</div>
                            <div class="input-row">
                                <?=
                                    $this->Form->text('birthday', [
                                        'class' => 'datepicker birthday'
                                    ]);
                                ?>
                                <span class="age">（<?=(is_numeric($player->age) ? $player->age.'歳' : '不明')?>）</span>
                            </div>
                        </div>
                        <div class="box">
                            <?php
                                echo $this->Form->selectDate('input_joined', [
                                    'label' => ['class' => 'label-row', 'text' => '入段日'],
                                    'class' => 'input-row',
                                    'empty' => [
                                        'year' => false,
                                        'month' => ['' => '-'],
                                        'day' => ['' => '-'],
                                    ],
                                ]);
                            ?>
                        </div>
                        <div class="box">
                            <?php
                                echo $this->Form->sexes([
                                    'label' => ['class' => 'label-row', 'text' => '性別'],
                                    'value' => $player->sex,
                                    'class' => 'input-row sex',
                                ]);
                            ?>
                        </div>
                        <div class="box">
                            <?php
                                echo $this->Form->control('rank_id', [
                                    'options' => $ranks,
                                    'label' => ['class' => 'label-row', 'text' => '段位'],
                                    'class' => 'input-row rank',
                                    'empty' => false,
                                ]);
                            ?>
                        </div>
                        <div class="box">
                            <div class="label-row">最終更新日時</div>
                            <div class="input-row">
                                <?= h($this->Date->formatToDateTime($player->modified)) ?>
                                <?= $this->Form->hidden('modified') ?>
                            </div>
                        </div>
                    </li>
                    <li class="detail-row">
                        <div class="box">
                            <?= $this->Form->control('remarks', [
                                'label' => ['class' => 'label-row', 'text' => 'その他備考'],
                                'type' => 'textarea',
                                'class' => 'input-row',
                            ]) ?>
                        </div>
                    </li>
                    <li class="button-row">
                        <?php // 新規登録時は続けて登録チェックボックス表示 ?>
                        <?php if ($player->isNew()) : ?>
                            <?php
                                echo $this->Form->control('is_continue', [
                                    'label' => ['class' => 'checkbox-label', 'text' => '続けて登録'],
                                    'checked' => true,
                                ]);
                            ?>
                        <?php endif ?>
                        <?= $this->Form->button('保存', ['class' => 'button button-primary']) ?>
                    </li>
                </ul>
            <?=$this->Form->end()?>
        </section>

        <?php if (!$player->isNew()) : ?>
            <!-- 昇段情報 -->
            <section data-contentname="ranks" class="tab-contents">
                <div class="category-row">昇段情報</div>
                <ul class="boxes">
                    <li class="label-row">新規登録</li>
                    <li class="detail-row">
                        <?= $this->Form->create($player, [
                            'class' => 'rank-form',
                            'type' => 'post',
                            'url' => ['_name' => 'create_ranks', $player->id],
                        ]) ?>
                        <div class="input-row">
                            <div class="box box-start">
                                <?php
                                    echo $this->Form->control('rank_id', [
                                        'label' => ['class' => 'label', 'text' => '段位'],
                                        'options' => $ranks,
                                        'class' => 'rank',
                                        'empty' => false,
                                    ]);
                                    echo $this->Form->control('promoted',[
                                        'label' => ['class' => 'label', 'text' => '昇段日'],
                                        'type' => 'text',
                                        'class' => 'datepicker',
                                    ]);
                                ?>
                            </div>
                            <div class="box box-end">
                                <?php
                                    echo $this->Form->control('newest', [
                                        'label' => ['class' => 'checkbox-label', 'text' => '最新として登録'],
                                        'checked' => true,
                                        'class' => 'checkbox-with-label',
                                    ]);
                                    echo $this->Form->button('登録', ['class' => 'add-ranks']);
                                ?>
                            </div>
                        </div>
                        <?= $this->Form->end() ?>
                    </li>
                    <li class="label-row">昇段履歴</li>
                    <?php foreach ($player->player_ranks as $player_rank) : ?>
                    <li class="detail-row">
                        <?= $this->Form->create($player_rank, [
                            'class' => 'rank-form',
                            'type' => 'put',
                            'url' => ['_name' => 'update_ranks', $player_rank->player_id, $player_rank->id],
                        ]) ?>
                            <div class="input-row">
                                <div class="box box-start">
                                    <?php
                                        echo $this->Form->control('rank_id', [
                                            'label' => ['class' => 'label', 'text' => '段位'],
                                            'options' => $ranks,
                                            'class' => 'rank',
                                            'empty' => false,
                                        ]);
                                        echo $this->Form->control('promoted',[
                                            'label' => ['class' => 'label', 'text' => '昇段日'],
                                            'type' => 'text',
                                            'class' => 'datepicker',
                                        ]);
                                    ?>
                                </div>
                                <div class="box box-end">
                                    <?= $this->Form->button('更新', ['class' => 'add-ranks']) ?>
                                </div>
                            </div>
                        <?= $this->Form->end() ?>
                    </li>
                    <?php endforeach ?>
                </ul>
            </section>

            <!-- 棋士成績 -->
            <section data-contentname="scores" class="tab-contents">
                <div class="category-row">勝敗</div>

                <?php // 2017年以降 ?>
                <?php if (!empty($player->title_score_details)) : ?>
                <?php foreach ($player->title_score_details as $score) : ?>
                <ul class="boxes">
                    <li class="genre-row"><?=h($score->target_year).'年度'?></li>
                    <li class="detail-row">
                        <div class="box">
                            <div class="label-row">勝敗（国内）</div>
                            <div class="input-row">
                                <?= $score->win_point ?>勝<?= $score->lose_point ?>敗<?= $score->draw_point ?>分
                                <span class="percent">
                                    （勝率<strong>
                                        <?= $this->Form->percent($score->win_point, $score->lose_point)?>
                                    </strong>）
                                </span>
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row">勝敗（国際）</div>
                            <div class="input-row">
                                <?= $score->win_point_world ?>勝
                                <?= $score->lose_point_world ?>敗
                                <?= $score->draw_point_world ?>分
                                <span class="percent">
                                    （勝率<strong>
                                        <?= $this->Form->percent($score->win_point_world, $score->lose_point_world) ?>
                                    </strong>）
                                </span>
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row"></div>
                            <div class="input-row button-wrap">
                                <?= $this->Html->link('タイトル成績へ', [
                                    '_name' => 'find_player_scores', $player->id, $score->target_year,
                                ], [
                                    'class' => 'layout-button',
                                ]) ?>
                            </div>
                        </div>
                    </li>
                </ul>
                <?php endforeach ?>
                <?php endif ?>

                <?php // 2016年以前 ?>
                <?php foreach ($player->old_scores as $key => $score) : ?>
                <ul class="boxes">
                    <li class="genre-row"><?=h($score->target_year).'年度'?></li>
                    <li class="detail-row">
                        <div class="box">
                            <div class="label-row">勝敗（国内）</div>
                            <div class="input-row">
                                <?=h($score->win_point)?>勝<?=h($score->lose_point)?>敗<?=h($score->draw_point)?>分
                                <span class="percent">（勝率<strong><?=$this->Form->percent($score->win_point, $score->lose_point)?></strong>）</span>
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row">勝敗（国際）</div>
                            <div class="input-row">
                                <?=$score->win_point_world?>勝<?=$score->lose_point_world?>敗<?=$score->draw_point_world?>分
                                <span class="percent">（勝率<strong><?=$this->Form->percent($score->win_point_world, $score->lose_point_world)?></strong>）</span>
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row">段位</div>
                            <div class="input-row"><?=h($score->rank->name)?></div>
                        </div>
                    </li>
                </ul>
                <?php endforeach ?>
            </section>

            <!-- タイトル取得履歴 -->
            <?php if (!$player->retention_histories->isEmpty()) : ?>
            <section data-contentname="titleRetains" class="tab-contents">
                <div class="category-row">タイトル取得履歴</div>
                <?php $histories = $player->groupByYearFromHistories(); ?>
                <?php foreach ($histories as $key => $items) : ?>
                    <div class="genre-row"><?=h($key).'年度'?></div>
                    <?php foreach ($items as $item) : ?>
                    <div class="input-row">
                        <span class="inner-column"><?= h($item->target_year).'年' ?></span>
                        <span class="inner-column"><?= h($item->holding).'期' ?></span>
                        <span class="inner-column"><?= h($item->name) ?></span>
                        <span class="inner-column"><?= h($item->title->country->label) ?></span>
                    </div>
                    <?php endforeach ?>
                <?php endforeach ?>
            </section>
            <?php endif ?>
        <?php endif ?>
    </div>
</div>
