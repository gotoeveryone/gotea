<?php
/**
 * @var \Gotea\View\AppView $this
 * @var \Gotea\Model\Entity\TitleScore $score
 */
$isAdmin = $this->isAdmin();
?>
<section data-contentname="score" class="tab-contents">
    <?= $this->Form->create($score, ['class' => 'main-form', 'url' => ['_name' => 'update_score', $score->id]]) ?>
    <?= $this->Form->control('id') ?>
    <div class="page-header"><?= __('Score Detail') ?> (ID: <?= h($score->id) ?>)</div>
    <ul class="detail_box">
        <li class="detail_box_item box-3">
            <?php
            echo $this->Form->control('started', [
                'label' => ['class' => 'label-row', 'text' => __d('model', 'started')],
                'type' => 'text',
                'class' => 'input-row datepicker',
                'autocomplete' => 'off',
                'disabled' => !$isAdmin,
            ]);
            ?>
        </li>
        <li class="detail_box_item box-3">
            <div class="input">
                <div class="label-row"><?= h(__d('model', 'ended')) ?></div>
                <div class="input checkbox-with-text-field-row">
                    <?php
                    echo $this->Form->control('is_same_started', [
                        'type' => 'checkbox',
                        'data-checked' => 'ended',
                        'data-is-check' => 'disabled',
                        'label' => ['class' => 'checkbox-label', 'text' => '開始日と同じ'],
                    ]);
                    echo $this->Form->text('ended', [
                        'data-target' => 'ended',
                        'type' => 'text',
                        'disabled' => true,
                        'class' => 'datepicker',
                        'autocomplete' => 'off',
                        'disabled' => !$isAdmin,
                    ]);
                    ?>
                </div>
            </div>
        </li>
        <li class="detail_box_item box-4">
            <?php
            echo $this->cell('Countries', [
                'hasTitleOnly' => false,
                [
                    'label' => ['class' => 'label-row', 'text' => __d('model', 'country_id')],
                    'empty' => false,
                    'value' => $score->country_id,
                    'class' => 'input-row',
                    'disabled' => !$isAdmin,
                ],
            ])->render()
            ?>
        </li>
        <li class="detail_box_item box-1">
            <div class="input">
                <?php
                echo $this->Form->label('is_world', __d('model', 'is_world'), ['class' => 'label-row']);
                echo $this->Form->control('is_world', [
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
        <li class="detail_box_item box-3">
            <?php
            echo $this->Form->control('title_id', [
                'options' => $activeTitles,
                'label' => ['class' => 'label-row', 'text' => __d('model', 'title_id')],
                'empty' => true,
                'value' => $score->title_id,
                'class' => 'input-row',
                'disabled' => !$isAdmin,
            ]);
            ?>
        </li>
        <li class="detail_box_item box-5">
            <?php
            echo $this->Form->control('name', [
                'label' => ['class' => 'label-row', 'text' => __d('model', 'title_name')],
                'class' => 'input-row',
                'disabled' => !$isAdmin,
            ]);
            ?>
        </li>
        <li class="detail_box_item box-4">
            <div class="input">
                <div class="label-row"><?= __d('model', 'modified') ?></div>
                <div class="input-row">
                    <?= h($this->Date->formatToDateTime($score->modified)) ?>
                    <?= $this->Form->hidden('modified') ?>
                </div>
            </div>
        </li>
        <li class="detail_box_item box-3">
            <div class="input">
                <div class="label-row"><?= __d('model', 'winner') ?></div>
                <div class="input-row">
                    <?= h($score->winner_name) ?>
                </div>
            </div>
        </li>
        <li class="detail_box_item box-3">
            <div class="input">
                <div class="label-row"><?= __d('model', 'loser') ?></div>
                <div class="input-row">
                    <?= h($score->loser_name) ?>
                </div>
            </div>
        </li>
        <li class="detail_box_item box-6">
            <?php
            echo $this->Form->control('result', [
                'label' => ['class' => 'label-row', 'text' => __d('model', 'result')],
                'class' => 'input-row',
                'disabled' => !$isAdmin,
            ]);
            ?>
        </li>
        <?php if ($isAdmin) : ?>
        <li class="detail_box_item button-row">
            <?= $this->Form->button(__('Save'), [
                'name' => 'action',
                'value' => 'save',
                'class' => 'button button-primary',
            ]) ?>
            <?= $this->Form->button(__('Switch Division'), [
                'name' => 'action',
                'value' => 'switchDivision',
                'class' => 'button button-secondary',
            ]) ?>
        </li>
        <?php endif ?>
    </ul>
    <?= $this->Form->end() ?>
</section>
