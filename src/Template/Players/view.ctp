<?php
/**
 * @var \Gotea\View\AppView $this
 * @var \Gotea\Model\Entity\Player $player
 */
?>
<?= $this->Html->css('view', ['block' => true]) ?>
<div class="detail-dialog">
    <!-- タブ -->
    <ul class="tabs" data-selecttab="<?= $this->getRequest()->getQuery('tab') ?>">
        <li class="tab" data-tabname="player">棋士情報</li>
        <?php if (!$player->isNew()) : ?>
            <li class="tab" data-tabname="ranks">昇段情報</li>
            <li class="tab" data-tabname="title_scores">成績情報</li>
            <?php if (!$player->retention_histories->isEmpty()) : ?>
                <li class="tab" data-tabname="retention_histories">タイトル取得情報</li>
            <?php endif ?>
        <?php endif ?>
    </ul>

    <!-- 詳細 -->
    <div class="detail">
        <!-- 棋士情報 -->
        <?= $this->element('Players/base') ?>

        <?php if (!$player->isNew()) : ?>
            <!-- 昇段情報 -->
            <?= $this->element('Players/ranks') ?>

            <!-- 成績情報 -->
            <?= $this->element('Players/title_scores') ?>

            <!-- タイトル取得履歴 -->
            <?php if (!$player->retention_histories->isEmpty()) : ?>
                <?= $this->element('Players/retention_histories') ?>
            <?php endif ?>
        <?php endif ?>
    </div>
</div>
