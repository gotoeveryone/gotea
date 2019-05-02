<?php

/**
 * @var \Gotea\View\AppView $this
 * @var \Gotea\Model\Entity\Notification $notification
 */
?>
<?= $this->Html->css('view', ['block' => true]) ?>
<div class="notifications detail">
    <?= $this->Form->create($notification, [
        'url' => ['_name' => 'update_notification', $notification->id],
        'class' => 'main-form',
    ]) ?>
    <div class="page-header">
        <span><?= __('編集') ?></span>
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
                    'label' => ['class' => 'input-row checkbox-label', 'text' => false],
                ]) ?>
            </div>
        </li>
        <li class="detail_box_item box-10">
            <?= $this->Form->control('published', [
                'label' => ['class' => 'label-row', 'text' => __d('model', 'published')],
                'year' => [
                    'class' => 'input-row dropdowns',
                ],
                'month' => [
                    'class' => 'input-row dropdowns',
                ],
                'day' => [
                    'class' => 'input-row dropdowns',
                ],
                'hour' => [
                    'class' => 'input-row dropdowns',
                ],
                'minute' => [
                    'class' => 'input-row dropdowns',
                ],
                'second' => [
                    'class' => 'input-row dropdowns',
                ],
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
