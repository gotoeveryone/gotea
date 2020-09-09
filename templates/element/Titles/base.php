<?php
/**
 * @var \Gotea\View\AppView $this
 * @var \Gotea\Model\Entity\Title $title
 */
$isAdmin = $this->isAdmin();
?>
<section data-contentname="title" class="tab-contents">
    <?= $this->Form->create($title, ['class' => 'main-form', 'url' => ['_name' => 'update_title', $title->id]]) ?>
    <?= $this->Form->control('id') ?>
    <div class="page-header">タイトル情報 (ID: <?= h($title->id) ?>)</div>
    <ul class="detail_box">
        <li class="detail_box_item box-3">
            <?php
            echo $this->Form->control('name', [
                'label' => ['class' => 'label-row', 'text' => __d('model', 'name')],
                'class' => 'input-row',
                'maxlength' => 30,
                'disabled' => !$isAdmin,
            ]);
            ?>
        </li>
        <li class="detail_box_item box-4">
            <?php
            echo $this->Form->control('name_english', [
                'label' => ['class' => 'label-row', 'text' => __d('model', 'name_english')],
                'class' => 'input-row',
                'maxlength' => 30,
                'disabled' => !$isAdmin,
            ]);
            ?>
        </li>
        <li class="detail_box_item box-1">
            <div class="input">
                <div class="label-row">分類</div>
                <div class="label-field-row">
                    <?= h($title->country->name . '棋戦') ?>
                </div>
            </div>
        </li>
        <li class="detail_box_item box-1">
            <div class="input">
                <?php
                echo $this->Form->label('is_team', __d('model', 'is_team'), ['class' => 'label-row']);
                echo $this->Form->control('is_team', [
                    'label' => false,
                    'class' => 'input-row',
                    'disabled' => !$isAdmin,
                ]);
                ?>
            </div>
        </li>
        <li class="detail_box_item box-1">
            <div class="input">
                <?php
                echo $this->Form->label('is_closed', __d('model', 'is_closed'), ['class' => 'label-row']);
                echo $this->Form->control('is_closed', [
                    'label' => false,
                    'class' => 'input-row',
                    'disabled' => !$isAdmin,
                ]);
                ?>
            </div>
        </li>
        <li class="detail_box_item box-1">
            <div class="input">
                <?php
                echo $this->Form->label('is_output', __d('model', 'is_output'), ['class' => 'label-row']);
                echo $this->Form->control('is_output', [
                    'label' => false,
                    'class' => 'input-row',
                    'disabled' => !$isAdmin,
                ]);
                ?>
            </div>
        </li>
        <li class="detail_box_item box-1">
            <div class="input">
                <?php
                echo $this->Form->label('is_official', __d('model', 'is_official'), ['class' => 'label-row']);
                echo $this->Form->control('is_official', [
                    'label' => false,
                    'class' => 'input-row',
                    'disabled' => !$isAdmin,
                ]);
                ?>
            </div>
        </li>
        <li class="detail_box_item box-2">
            <?php
            echo $this->Form->control('holding', [
                'label' => ['class' => 'label-row', 'text' => __d('model', 'holding')],
                'class' => 'input-row input-short',
                'maxlength' => 3,
                'disabled' => !$isAdmin,
            ]);
            ?>
        </li>
        <li class="detail_box_item box-2">
            <div class="input">
                <div class="label-row">現在の保持者</div>
                <div class="label-field-row"><?= h($title->getWinnerName(true)) ?></div>
            </div>
        </li>
        <li class="detail_box_item box-2">
            <?php
            echo $this->Form->control('html_file_name', [
                'label' => ['class' => 'label-row', 'text' => __d('model', 'html_file_name')],
                'class' => 'input-row',
                'maxlength' => 10,
                'disabled' => !$isAdmin,
            ]);
            ?>
        </li>
        <li class="detail_box_item box-2">
            <?php
            echo $this->Form->control('html_file_holding', [
                'label' => ['class' => 'label-row', 'text' => __d('model', 'html_file_holding')],
                'class' => 'input-row input-short',
                'maxlength' => 3,
                'disabled' => !$isAdmin,
            ]);
            ?>
        </li>
        <li class="detail_box_item box-2">
            <?php
            echo $this->Form->control('html_file_modified', [
                'label' => ['class' => 'label-row', 'text' => __d('model', 'html_file_modified')],
                'type' => 'text',
                'class' => 'input-row datepicker',
                'autocomplete' => 'off',
                'disabled' => !$isAdmin,
            ]);
            ?>
        </li>
        <li class="detail_box_item box-2">
            <div class="input">
                <div class="label-row"><?= __d('model', 'modified') ?></div>
                <div class="label-field-row">
                    <?= h($this->Date->formatToDateTime($title->modified)) ?>
                    <?= $this->Form->hidden('modified') ?>
                </div>
            </div>
        </li>
        <li class="detail_box_item">
            <?= $this->Form->control('remarks', [
                'label' => ['class' => 'label-row', 'text' => __d('model', 'remarks')],
                'type' => 'textarea',
                'class' => 'input-row',
                'disabled' => !$isAdmin,
            ]) ?>
        </li>
        <?php if ($isAdmin) : ?>
        <li class="detail_box_item button-row">
            <?= $this->Form->button(__('Save'), ['class' => 'button button-primary']) ?>
        </li>
        <?php endif ?>
    </ul>
    <?= $this->Form->end() ?>
</section>
