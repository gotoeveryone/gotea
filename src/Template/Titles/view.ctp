<?php
/**
 * @var \Gotea\View\AppView $this
 * @var \Gotea\Model\Entity\Title $title
 */
?>
<?= $this->Html->css('view', ['block' => true]) ?>
<div class="detail-dialog">
    <!-- タブ -->
    <ul class="tabs" data-selecttab="<?= $this->getRequest()->getQuery('tab') ?>">
        <li class="tab" data-tabname="title">タイトル情報</li>
        <li class="tab" data-tabname="retention_histories">保持履歴</li>
    </ul>

    <!-- 詳細 -->
    <div class="detail">
        <!-- タイトル情報 -->
        <?= $this->element('Titles/base') ?>

        <!-- 保持履歴 -->
        <?= $this->element('Titles/retention_histories') ?>
    </div>
</div>
