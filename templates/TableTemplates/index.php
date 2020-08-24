<?php
/**
 * @var \Gotea\View\AppView $this ビューオブジェクト
 * @var \Gotea\Model\Entity\TableTemplate[]|\Cake\Collection\CollectionInterface $tableTemplates 表テンプレートデータ
 */
?>
<div class="table-templates index large-9 medium-8 columns content">
    <ul class="search-header">
        <li class="search-row">
            <div class="search-box search-box-right">
                <?= $this->Html->link(__('Add'), [
                    '_name' => 'new_table_template',
                ], [
                    'class' => 'layout-button button-secondary',
                ]) ?>
            </div>
        </li>
    </ul>
    <?php if (!empty($tableTemplates)) : ?>
        <?= $this->element('Paginator/default', ['url' => ['_name' => 'table_templates']]) ?>
    <?php endif ?>
    <div class="search-results">
        <ul class="table-header">
            <li class="table-row">
                <span class="table-column table-column_title"><?= __('Title') ?></span>
                <span class="table-column table-column_created"><?= __('Created') ?></span>
                <span class="table-column table-column_modified"><?= __('Modified') ?></span>
                <span class="table-column table-column_actions"><?= __('Actions') ?></span>
            </li>
        </ul>
        <?php if (!empty($tableTemplates)) : ?>
            <ul class="table-body">
                <?php foreach ($tableTemplates as $tableTemplate) : ?>
                    <li class="table-row">
                        <span class="table-column table-column_title" title="<?= h($tableTemplate->title) ?>">
                            <?= h($tableTemplate->title) ?>
                        </span>
                        <span class="table-column table-column_created">
                            <?= $this->Date->formatToDateTime($tableTemplate->created) ?>
                        </span>
                        <span class="table-column table-column_modified">
                            <?= $this->Date->formatToDateTime($tableTemplate->modified) ?>
                        </span>
                        <span class="table-column table-column_actions">
                            <?= $this->Html->link(__('Edit'), [
                                '_name' => 'edit_table_template', $tableTemplate->id,
                            ], [
                                'class' => 'layout-button button-secondary',
                            ]) ?>
                            <?= $this->Form->postLink(__('Delete'), [
                                '_name' => 'delete_table_template', $tableTemplate->id,
                            ], [
                                'method' => 'delete',
                                'class' => 'layout-button button-danger',
                                'confirm' => __('Are you sure you want to delete # {0}?', $tableTemplate->id),
                            ]) ?>
                        </span>
                    </li>
                <?php endforeach ?>
            </ul>
        <?php endif ?>
    </div>
</div>
