<?php
/**
 * @var \Gotea\View\AppView $this
 * @var \Gotea\Model\Entity\Player $player
 */
?>
<section data-contentname="title_scores" class="tab-contents">
    <div class="page-header">成績情報<?= !$player->isNew() ? ' (ID: '. h($player->id) . ')' : '' ?></div>
    <?php if (!empty($player->title_score_details)) : ?>
        <?php foreach ($player->title_score_details as $score) : ?>
            <ul class="boxes">
                <li class="label-row"><?= __('{0}年度', $score->target_year) ?></li>
                <li class="detail_box">
                    <div class="detail_box_item box-3">
                        <div class="input">
                            国内：<?= h($score->win_point) ?>勝<?= h($score->lose_point) ?>敗<?= h($score->draw_point) ?>分
                            <span class="percent">
                                （勝率<strong><?= $this->Form->percent($score->win_point, $score->lose_point) ?></strong>）
                            </span>
                        </div>
                    </div>
                    <div class="detail_box_item box-3">
                        <div class="input">
                            国際：<?= h($score->win_point_world) ?>勝<?= h($score->lose_point_world) ?>敗<?= h($score->draw_point_world) ?>分
                            <span class="percent">
                                （勝率<strong><?= $this->Form->percent($score->win_point_world, $score->lose_point_world) ?></strong>）
                            </span>
                        </div>
                    </div>
                    <div class="detail_box_item box-3">
                        <div class="input">
                            合計（非公式戦含む）：<?= h($score->win_point_all) ?>勝<?= h($score->lose_point_all) ?>敗<?= h($score->draw_point_all) ?>分
                            <span class="percent">
                                （勝率<strong><?= $this->Form->percent($score->win_point_all, $score->lose_point_all) ?></strong>）
                            </span>
                        </div>
                    </div>
                    <div class="detail_box_item detail_box_item-buttons box-3">
                        <?= $this->Html->link('タイトル成績へ', [
                            '_name' => 'find_player_scores', $player->id, $score->target_year,
                        ], [
                            'class' => 'layout-button',
                        ]) ?>
                    </div>
                </li>
            </ul>
        <?php endforeach ?>
    <?php endif ?>

    <?php foreach ($player->old_scores as $key => $score) : ?>
        <ul class="boxes">
            <li class="label-row"><?= __('{0}年度', $score->target_year) ?></li>
            <li class="detail_box">
                <div class="detail_box_item box-5">
                    <div class="input">
                        国内：<?= h($score->win_point) ?>勝<?= h($score->lose_point) ?>敗<?= h($score->draw_point) ?>分
                        <span class="percent">
                            （勝率<strong><?= $this->Form->percent($score->win_point, $score->lose_point) ?></strong>）
                        </span>
                    </div>
                </div>
                <div class="detail_box_item box-5">
                    <div class="input">
                        国際：<?= h($score->win_point_world) ?>勝<?= h($score->lose_point_world) ?>敗<?= h($score->draw_point_world) ?>分
                        <span class="percent">
                            （勝率<strong><?= $this->Form->percent($score->win_point_world, $score->lose_point_world) ?></strong>）
                        </span>
                    </div>
                </div>
                <div class="detail_box_item box-2">
                    <div class="input">
                        段位：<?= h($score->rank->name) ?>
                    </div>
                </div>
            </li>
        </ul>
    <?php endforeach ?>
</section>
