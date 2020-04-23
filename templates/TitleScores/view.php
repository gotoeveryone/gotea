<?php
/**
 * @var \Gotea\View\AppView $this
 * @var \Gotea\Model\Entity\TitleScore $score
 */
?>
<?= $this->Html->css('view', ['block' => true]) ?>
<div class="detail-dialog">
    <!-- タブ -->
    <ul class="tabs" data-selecttab="<?= $this->getRequest()->getQuery('tab') ?>">
        <li class="tab" data-tabname="score"><?= __('Score Detail') ?></li>
    </ul>

    <!-- 詳細 -->
    <div class="detail">
        <?= $this->element('TitleScores/base') ?>
    </div>
</div>
