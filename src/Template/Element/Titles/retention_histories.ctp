
<?php
/**
 * @var \Gotea\View\AppView $this
 * @var \Gotea\Model\Entity\Title $title
 */
?>
<section data-contentname="retention_histories" class="tab-contents">
    <?= $this->Form->create(null, [
        'class' => 'add-condition-form',
        'type' => 'post',
        'url' => ['_name' => 'save_histories', $title->id],
    ]) ?>
    <?= $this->Form->hidden('name', ['value' => $title->name]) ?>
    <div class="page-header">保持履歴 (ID: <?= h($title->id) ?>)</div>
    <add-history :history-id="historyId" :is-team="<?= $title->is_team ? 'true' : 'false' ?>" @cleared="clearHistory()"></add-history>
    <ul class="boxes">
        <?php if (!empty(($title->retention_histories))) : ?>
            <?php if (($retention = $title->now_retention)) : ?>
                <li class="label-row">現在の保持情報</li>
                <li class="detail_box">
                    <div class="detail_box_item box-10">
                        <span class="inner-column"><?= h($retention->target_year) . '年' ?></span>
                        <span class="inner-column"><?= h($retention->holding) . '期' ?></span>
                        <span class="inner-column"><span>タイトル名：</span><?= h($retention->name) ?></span>
                        <span class="inner-column"><?= h($retention->team_label) ?></span>
                        <span class="inner-column"><span>優勝者：</span><?= h($retention->winner_name) ?></span>
                        <?php if ($title->country->isWorlds() && !$retention->is_team) : ?>
                        <span class="inner-column"><span>出場国：</span><?= h($retention->country->name) ?></span>
                        <?php endif ?>
                        <?php if (!$retention->is_official) : ?>
                            <span class="inner-column">（非公式戦）</span>
                        <?php endif ?>
                        <?php if ($retention->isRecent()) : ?>
                            <span class="inner-column"><span class="mark-new">NEW!</span></span>
                        <?php endif ?>
                    </div>
                    <div class="detail_box_item detail_box_item-buttons box-2">
                        <button type="button" class="button button-secondary" value="edit" @click="select('<?= $retention->id ?>')">編集</button>
                    </div>
                </li>
            <?php endif ?>
            <li class="label-row">保持情報（履歴）</li>
            <?php foreach ($title->histories as $history) : ?>
                <li class="detail_box">
                    <div class="detail_box_item box-10">
                        <span class="inner-column"><?= h($history->target_year) . '年' ?></span>
                        <span class="inner-column"><?= h($history->holding) . '期' ?></span>
                        <span class="inner-column"><span>タイトル名：</span><?= h($history->name) ?></span>
                        <span class="inner-column"><?= h($history->team_label) ?></span>
                        <span class="inner-column"><span>優勝者：</span><?= h($history->winner_name) ?></span>
                        <?php if ($title->country->isWorlds() && !$history->is_team) : ?>
                        <span class="inner-column"><span>出場国：</span><?= h($history->country->name) ?></span>
                        <?php endif ?>
                        <?php if (!$history->is_official) : ?>
                            <span class="inner-column">（非公式戦）</span>
                        <?php endif ?>
                    </div>
                    <div class="detail_box_item detail_box_item-buttons box-2">
                        <button type="button" class="button button-secondary" value="edit" @click="select('<?= $history->id ?>')">編集</button>
                    </div>
                </li>
            <?php endforeach ?>
        <?php endif ?>
    </ul>
    <?= $this->Form->end() ?>
</section>
