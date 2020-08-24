<?php
/**
 * @var \Gotea\View\AppView $this
 * @var \Gotea\Model\Entity\Notification[]|\Cake\Collection\CollectionInterface $notifications
 */
?>
<div class="notifications index large-9 medium-8 columns content">
    <ul class="search-header">
        <li class="search-row">
            <div class="search-box search-box-right">
                <?= $this->Html->link(__('Add'), [
                    '_name' => 'new_notification',
                ], [
                    'class' => 'layout-button button-secondary',
                ]) ?>
            </div>
        </li>
    </ul>
    <?php if (!empty($notifications)) : ?>
        <?= $this->element('Paginator/default', ['url' => ['_name' => 'notifications']]) ?>
    <?php endif ?>
    <div class="search-results">
        <ul class="table-header">
            <li class="table-row">
                <span class="table-column table-column_title"><?= __('Title') ?></span>
                <span class="table-column table-column_content"><?= __('Content') ?></span>
                <span class="table-column table-column_status"><?= __('Status') ?></span>
                <span class="table-column table-column_permanent"><?= __('Permanent') ?></span>
                <span class="table-column table-column_published"><?= __('Published') ?></span>
                <span class="table-column table-column_actions"><?= __('Actions') ?></span>
            </li>
        </ul>
        <?php if (!empty($notifications)) : ?>
            <ul class="table-body">
                <?php foreach ($notifications as $notification) : ?>
                    <li class="table-row">
                        <span class="table-column table-column_title" title="<?= h($notification->title) ?>">
                            <?= h($notification->title) ?>
                        </span>
                        <span class="table-column table-column_content" title="<?= h($notification->content) ?>">
                            <?= h($notification->content) ?>
                        </span>
                        <span class="table-column table-column_status">
                            <?= h($notification->status) ?>
                        </span>
                        <span class="table-column table-column_permanent">
                            <?= $notification->is_permanent ? __('Check') : '' ?>
                        </span>
                        <span class="table-column table-column_published">
                            <?= $this->Date->format($notification->published, 'yyyy-MM-dd HH:mm:ss') ?>
                        </span>
                        <span class="table-column table-column_actions">
                            <?= $this->Html->link(__('Edit'), [
                                '_name' => 'edit_notification', $notification->id,
                            ], [
                                'class' => 'layout-button button-secondary',
                            ]) ?>
                            <?= $this->Html->link(__('Copy'), [
                                '_name' => 'new_notification', '?' => ['from' => $notification->id],
                            ], [
                                'class' => 'layout-button button-secondary',
                            ]) ?>
                            <?= $this->Form->postLink(__('Delete'), [
                                '_name' => 'delete_notification', $notification->id,
                            ], [
                                'method' => 'delete',
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
