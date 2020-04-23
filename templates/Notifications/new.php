<?php
/**
 * @var \Gotea\View\AppView $this
 * @var \Gotea\Model\Entity\Notification $notification
 */
?>
<?= $this->Html->css('view', ['block' => true]) ?>
<div class="notifications detail">
    <?= $this->Form->create($notification, [
        'url' => ['_name' => 'create_notification'],
        'class' => 'main-form',
    ]) ?>
    <div class="page-header">
        <span><?= __('新規登録') ?></span>
        <span>
            <?= $this->Html->link(__('一覧へ戻る'), [
                '_name' => 'notifications',
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
                'class' => 'input-row notification_content',
            ]) ?>
        </li>
        <li class="detail_box_item box-2">
            <div class="input">
                <div class="label-row"><?= __d('model', 'is_draft') ?></div>
                <?= $this->Form->control('is_draft', [
                    'label' => ['class' => 'input-row checkbox-label'],
                ]) ?>
            </div>
        </li>
        <li class="detail_box_item box-2">
            <div class="input">
                <div class="label-row"><?= __d('model', 'is_permanent') ?></div>
                <?= $this->Form->control('is_permanent', [
                    'label' => ['class' => 'input-row checkbox-label'],
                ]) ?>
            </div>
        </li>
        <li class="detail_box_item box-8">
            <?= $this->Form->control('published', [
                'type' => 'datetime',
                'label' => ['class' => 'label-row', 'text' => __d('model', 'published')],
                'class' => 'input-row dropdowns',
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
