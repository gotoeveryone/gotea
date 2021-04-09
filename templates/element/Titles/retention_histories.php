
<?php
/**
 * @var \Gotea\View\AppView $this
 * @var \Gotea\Model\Entity\Title $title
 */
$isAdmin = $this->isAdmin();
?>
<section data-contentname="retention_histories" class="tab-contents">
    <?= $this->Form->create(null, [
        'class' => 'add-condition-form',
        'type' => 'post',
        'url' => ['_name' => 'save_histories', $title->id],
    ]) ?>
    <?= $this->Form->hidden('name', ['value' => $title->name]) ?>
    <div class="page-header">保持履歴 (ID: <?= h($title->id) ?>)</div>
    <?php if ($isAdmin) : ?>
    <add-history :history-id="historyId" :is-team="<?= $title->is_team ? 'true' : 'false' ?>" @cleared="clearHistory()"></add-history>
    <?php endif ?>
    <ul class="boxes">
        <?php if (!empty($title->retention_histories)) : ?>
            <?php
                $retention = $title->now_retention;
            ?>
            <?php if ($retention) : ?>
                <li class="label-row">現在の保持情報</li>
                <?= $this->element('Titles/retention_history_item', ['isAdmin' => $isAdmin, 'retention' => $retention]) ?>
            <?php endif ?>
            <li class="label-row">保持情報（履歴）</li>
            <?php foreach ($title->histories as $history) : ?>
                <?= $this->element('Titles/retention_history_item', ['isAdmin' => $isAdmin, 'retention' => $history]) ?>
            <?php endforeach ?>
        <?php endif ?>
    </ul>
    <?= $this->Form->end() ?>
</section>
