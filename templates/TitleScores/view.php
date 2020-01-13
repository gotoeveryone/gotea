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
        <!-- マスタ -->
        <section data-contentname="score" class="tab-contents">
            <?= $this->Form->create($score, ['class' => 'main-form', 'url' => ['_name' => 'update_score', $score->id]]) ?>
            <?= $this->Form->control('id') ?>
            <div class="page-header"><?= __('Score Detail') ?></div>
            <ul class="detail_box">
                <li class="detail_box_item box-3">
                    <?php
                    echo $this->Form->control('started', [
                        'label' => ['class' => 'label-row', 'text' => __d('model', 'started')],
                        'type' => 'text',
                        'class' => 'input-row datepicker',
                    ]);
                    ?>
                </li>
                <li class="detail_box_item box-3">
                    <?php
                    echo $this->Form->control('ended', [
                        'label' => ['class' => 'label-row', 'text' => __d('model', 'ended')],
                        'type' => 'text',
                        'class' => 'input-row datepicker',
                    ]);
                    ?>
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
                    ]);
                    ?>
                </li>
                <li class="detail_box_item box-5">
                    <?php
                    echo $this->Form->control('name', [
                        'label' => ['class' => 'label-row', 'text' => __d('model', 'title_name')],
                        'class' => 'input-row',
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
                <li class="detail_box_item box-6">
                    <div class="input">
                        <div class="label-row"><?= __('Player of GO') ?></div>
                        <div class="input-row">
                            <?= h($score->players_name) ?>
                        </div>
                    </div>
                </li>
                <li class="detail_box_item box-6">
                    <div class="input">
                        <div class="label-row"><?= __d('model', 'winner') ?></div>
                        <div class="input-row">
                            <?= h($score->getWinnerName()) ?>
                        </div>
                    </div>
                </li>
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
            </ul>
            <?= $this->Form->end() ?>
        </section>
    </div>
</div>
