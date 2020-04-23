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
        <?php $unOfficialCount = count(array_filter($items, function ($item) {
            return !$item->is_official;
        })); ?>
        <?php $titleCount = count($items) . ($unOfficialCount > 0 ? " (非公式{$unOfficialCount})" : '') ?>
        <div class="label-row"><?= __('{0}年度: {1}', $key, $titleCount) ?></div>
        <?php foreach ($items as $item) : ?>
            <div class="input-row">
                <span class="inner-column"><?= __('{0}期', $item->holding) ?></span>
                <span class="inner-column"><?= h($item->name) ?></span>
                <span class="inner-column"><?= h($item->title->country->label) ?></span>
                <?php if (!$item->is_official) : ?>
                    <span class="inner-column">（非公式戦）</span>
                <?php endif ?>
            </div>
        <?php endforeach ?>
    <?php endforeach ?>
</section>
