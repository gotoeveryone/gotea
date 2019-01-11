<?php

/**
 * @var \Gotea\View\AppView $this
 * @var \Gotea\Model\Entity\Notification $notification
 */
?>
<?= $this->Html->css('view', ['block' => true]) ?>
<div class="notifications detail">
    <?= $this->Form->create($notification, ['url' => ['_name' => 'create_notification']]) ?>
    <div class="category-row">
        <span class="category-box"><?= __('新規登録') ?></span>
        <span class="category-box-right">
            <?= $this->Html->link(__('一覧へ戻る'), [
                '_name' => 'notifications',
            ], [
                'class' => 'layout-button',
            ]) ?>
        </span>
    </div>
    <ul class="boxes">
        <li class="detail-row">
            <fieldset class="detail-box box1">
                <?= $this->Form->control('title', [
                    'label' => ['class' => 'label-row', 'text' => __('タイトル')],
                    'class' => 'input-row',
                ]) ?>
            </fieldset>
        </li>
        <li class="detail-row">
            <fieldset class="detail-box box1">
                <?= $this->Form->control('content', [
                    'label' => ['class' => 'label-row', 'text' => __('本文')],
                    'class' => 'input-row notification_content',
                ]) ?>
            </fieldset>
        </li>
        <li class="detail-row">
            <fieldset class="detail-box box1">
                <div class="input checkbox">
                    <div class="label-row"><?= __('下書き') ?></div>
                    <?= $this->Form->control('is_draft', [
                        'label' => ['class' => 'input-row checkbox-label', 'text' => false],
                        'templates' => ['inputContainer' => '{{content}}'],
                    ]) ?>
                </div>
            </fieldset>
            <fieldset class="detail-box box1">
                <?= $this->Form->control('published', [
                    'label' => ['class' => 'label-row', 'text' => __('公開日時')],
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
            </fieldset>
        </li>
        <li class="button-row">
            <?= $this->Form->button(__('登録'), [
                'class' => 'button button-primary',
            ]) ?>
        </li>
    </ul>
    <?= $this->Form->end() ?>
</div>
