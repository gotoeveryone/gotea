<?php
/**
 * @var \Gotea\View\AppView $this
 * @var \Gotea\Model\Entity\Notification[]|\Cake\Collection\CollectionInterface $notifications
 */
?>
<div class="notifications index large-9 medium-8 columns content">
    <ul class="search-header">
        <li class="search-row">
            <fieldset class="search-box search-box-right">
                <?= $this->Html->link(__('新規登録'), [
                    '_name' => 'new_notification',
                ], [
                    'class' => 'layout-button button-secondary',
                ]) ?>
            </fieldset>
        </li>
    </ul>
    <?php if (!empty($notifications)) : ?>
        <?= $this->element('Paginator/default', ['url' => ['_name' => 'notifications']]) ?>
    <?php endif ?>
    <div class="search-results">
        <ul class="table-header">
            <li class="table-row">
                <span class="table-column table-column_title"><?= __('タイトル') ?></span>
                <span class="table-column table-column_content"><?= __('本文') ?></span>
                <span class="table-column table-column_status"><?= __('状態') ?></span>
                <span class="table-column table-column_published"><?= __('公開日時') ?></span>
                <span class="table-column table-column_actions"><?= __('操作') ?></span>
            </li>
        </ul>
        <?php if (!empty($notifications)) : ?>
            <ul class="table-body">
                <?php foreach ($notifications as $notification) : ?>
                    <li class="table-row">
                        <span class="table-column table-column_title" title="<?= $notification->title ?>">
                            <?= h($notification->title) ?>
                        </span>
                        <span class="table-column table-column_content" title="<?= $notification->content ?>">
                            <?= h($notification->content) ?>
                        </span>
                        <span class="table-column table-column_status">
                            <?= ($notification->is_draft ? '下書き' : '公開') ?>
                        </span>
                        <span class="table-column table-column_published">
                            <?= $this->Date->formatToDateTime($notification->published) ?>
                        </span>
                        <span class="table-column table-column_actions">
                            <?= $this->Html->link(__('編集'), [
                                '_name' => 'edit_notification', $notification->id,
                            ], [
                                'class' => 'layout-button button-secondary',
                            ]) ?>
                            <?= $this->Html->link(__('コピー'), [
                                '_name' => 'new_notification', 'from' => $notification->id,
                            ], [
                                'class' => 'layout-button button-secondary',
                            ]) ?>
                            <?= $this->Form->postLink(__('削除'), [
                                '_name' => 'delete_notification', $notification->id,
                            ], [
                                'class' => 'layout-button button-danger',
                                'confirm' => __('Are you sure you want to delete # {0}?', $notification->id),
                            ]) ?>
                        </span>
                    </li>
                <?php endforeach ?>
            </ul>
        <?php endif ?>
    </div>
</div>
