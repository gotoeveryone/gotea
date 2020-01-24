<?php
/**
 * @var \Gotea\View\AppView $this
 * @var \Gotea\Model\Entity\Player $player
 */
?>
<section data-contentname="retention_histories" class="tab-contents">
    <div class="page-header">タイトル取得履歴<?= !$player->isNew() ? ' (ID: '. h($player->id) . ')' : '' ?></div>
    <?php $histories = $player->groupByYearFromHistories(); ?>
    <?php foreach ($histories as $key => $items) : ?>
        <div class="label-row"><?= __('{0}年度', $key) ?></div>
        <?php foreach ($items as $item) : ?>
            <div class="input-row">
                <span class="inner-column"><?= __('{0}期', $item->holding) ?></span>
                <span class="inner-column"><?= h($item->name) ?></span>
                <span class="inner-column"><?= h($item->title->country->label) ?></span>
            </div>
        <?php endforeach ?>
    <?php endforeach ?>
</section>
