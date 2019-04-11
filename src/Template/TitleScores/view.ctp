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
            <div class="category-row"><?= __('Score Detail') . h(" (ID：{$score->id})") ?></div>
            <ul class="boxes">
                <li class="detail-row">
                    <fieldset class="detail-box box1">
                        <?php
                        echo $this->Form->control('started', [
                            'label' => ['class' => 'label-row', 'text' => __d('validation', 'started')],
                            'type' => 'text',
                            'class' => 'input-row datepicker',
                        ]);
                        ?>
                    </fieldset>
                    <fieldset class="detail-box box1">
                        <?php
                        echo $this->Form->control('ended', [
                            'label' => ['class' => 'label-row', 'text' => __d('validation', 'ended')],
                            'type' => 'text',
                            'class' => 'input-row datepicker',
                        ]);
                        ?>
                    </fieldset>
                    <fieldset class="detail-box box1">
                        <?php
                        echo $this->cell('Countries', [
                            'hasTitleOnly' => false,
                            [
                                'label' => ['class' => 'label-row', 'text' => __d('validation', 'country_id')],
                                'empty' => false,
                                'value' => $score->country_id,
                                'class' => 'input-row',
                            ],
                        ])->render()
                        ?>
                    </fieldset>
                    <fieldset class="detail-box box1">
                        <div class="input">
                            <?php
                            echo $this->Form->label('is_world', __d('validation', 'is_world'), ['class' => 'label-row']);
                            echo $this->Form->control('is_world', [
                                'label' => false,
                                'class' => 'input-row',
                            ]);
                            ?>
                        </div>
                    </fieldset>
                </li>
                <li class="detail-row">
                    <fieldset class="detail-box box1">
                        <?php
                        echo $this->Form->control('title_id', [
                            'options' => $activeTitles,
                            'label' => ['class' => 'label-row', 'text' => __d('validation', 'title_id')],
                            'empty' => true,
                            'value' => $score->title_id,
                            'class' => 'input-row',
                        ]);
                        ?>
                    </fieldset>
                    <fieldset class="detail-box box2">
                        <?php
                        echo $this->Form->control('name', [
                            'label' => ['class' => 'label-row', 'text' => __d('validation', 'title_name')],
                            'class' => 'input-row',
                        ]);
                        ?>
                    </fieldset>
                    <fieldset class="detail-box box1">
                        <div class="input">
                            <div class="label-row"><?= __d('validation', 'modified') ?></div>
                            <div class="input-row">
                                <?= h($this->Date->formatToDateTime($score->modified)) ?>
                                <?= $this->Form->hidden('modified') ?>
                            </div>
                        </div>
                    </fieldset>
                </li>
                <li class="detail-row">
                    <fieldset class="detail-box box1">
                        <div class="input">
                            <div class="label-row"><?= __('Player of GO') ?></div>
                            <div class="input-row">
                                <?= h($score->players_name) ?>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="detail-box box1">
                        <div class="input">
                            <div class="label-row"><?= __d('validation', 'winner') ?></div>
                            <div class="input-row">
                                <?= h($score->getWinnerName()) ?>
                            </div>
                        </div>
                    </fieldset>
                </li>
                <li class="button-row">
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
