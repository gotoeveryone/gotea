<?php
/**
 * @var \Gotea\View\AppView $this ビューオブジェクト
 * @var \Gotea\Model\Entity\TableTemplate $tableTemplate 表テンプレートデータ
 */
?>
<?= $this->Html->css('view', ['block' => true]) ?>
<div class="table-templates detail">
    <?= $this->Form->create($tableTemplate, [
        'url' => ['_name' => 'create_table_template'],
        'class' => 'main-form',
    ]) ?>
    <div class="page-header">
        <span><?= __('Add') ?></span>
        <span>
            <?= $this->Html->link(__('Back to List'), [
                '_name' => 'table_templates',
            ], [
                'class' => 'layout-button',
            ]) ?>
        </span>
    </div>
    <ul class="detail_box">
        <li class="detail_box_item">
            <?= $this->Form->control('title', [
                'label' => ['class' => 'label-row', 'text' => __d('model', 'title')],
                'class' => 'input-row',
            ]) ?>
        </li>
        <li class="detail_box_item">
            <?= $this->Form->control('content', [
                'label' => ['class' => 'label-row', 'text' => __d('model', 'content')],
                'class' => 'input-row table-template_content',
            ]) ?>
        </li>
        <li class="detail_box_item button-row">
            <?= $this->Form->button(__('Save'), [
                'class' => 'button button-primary',
            ]) ?>
        </li>
    </ul>
    <?= $this->Form->end() ?>
</div>
