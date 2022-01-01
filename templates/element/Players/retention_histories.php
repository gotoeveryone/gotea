<?php
/**
 * @var \Gotea\View\AppView $this
 * @var \Gotea\Model\Entity\Player $player
 */
?>
<section data-contentname="retention_histories" class="tab-contents">
    <div class="page-header">タイトル取得履歴<?= !$player->isNew() ? ' (ID: ' . h($player->id) . ')' : '' ?></div>
    <?php $histories = $player->groupByYearFromHistories(); ?>
    <?php foreach ($histories as $key => $countries) : ?>
        <?php
            $unOfficialCount = $countries->sumOf(function ($items) {
                return count(array_filter($items, function ($item) {
                    return !$item->is_official;
                }));
            });
            $titleCount = $countries->sumOf(function ($items) {
                return count($items);
            });
            $titleLabel = $titleCount . ($unOfficialCount > 0 ? " (非公式戦{$unOfficialCount})" : '');
        ?>
        <div class="label-row"><?= __('{0}年度: {1}', $key, $titleLabel) ?></div>
        <?php foreach ($countries as $country => $items) : ?>
            <div class="input-row">
                <div class="input-row-inner-box input-row-inner-box-2">
                    <?= h($country) . '棋戦' ?>
                </div>
                <div class="input-row-inner-box input-row-inner-box-10">
                    <?php
                        $label = implode(' / ', array_map(function ($history) {
                            $holding = h('第' . $history->holding . '期 ');
                            $acquiredMonth = h('(' . $this->Date->format($history->acquired, 'M月d日') . ')');

                            return $holding . $this->Html->link($history->name, ['_name' => 'view_title', $history->title_id])
                                . $acquiredMonth . (!$history->is_official ? '（非公式戦）' : '');
                        }, $items));
                    ?>
                    <span><?= $label ?></span>
                </div>
            </div>
        <?php endforeach ?>
    <?php endforeach ?>
</section>
