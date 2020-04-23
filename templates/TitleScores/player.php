<?php
/**
 * @var \Gotea\View\AppView $this
 * @var \Gotea\Model\Entity\Player $player
 * @var \Gotea\Model\Entity\TitleScoreDetail|null $detail
 * @var \Gotea\Model\Entity\TitleScore[]|\Cake\Collection\CollectionInterface $titleScores
 */
?>
<section class="title-scores">
    <div class="player-scores-header">
        <div class="score-detail">
            <div><?= h($player->name_with_rank) ?>の<?= h($year) ?>年成績</div>
            <div>
                <?= h($detail->win_point) ?>勝<?= h($detail->lose_point) ?>敗<?= h($detail->draw_point) ?>分
                 (非公式含: <?= h($detail->win_point_all) ?>勝<?= h($detail->lose_point_all) ?>敗<?= h($detail->draw_point_all) ?>分)
            </div>
        </div>
        <div class="back-detail">
            <?= $this->Html->link('<< 戻る', [
                '_name' => 'view_player', '?' => ['tab' => 'title_scores'], $player->id,
            ], [
                'class' => 'layout-button',
            ]) ?>
        </div>
    </div>
    <div class="player-scores-results">
        <?= $this->element('TitleScores/list') ?>
    </div>
</section>
