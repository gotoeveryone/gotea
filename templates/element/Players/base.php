<?php
/**
 * @var \Gotea\View\AppView $this
 * @var \Gotea\Model\Entity\Player $player
 */
?>
<section data-contentname="player" class="tab-contents">
    <?= $this->Form->create($player, ['class' => 'main-form', 'url' => $player->getSaveUrl()]) ?>
    <?= $this->Form->hidden('id', ['value' => $player->id]) ?>
    <?= $this->Form->hidden('country_id') ?>
    <div class="page-header">棋士情報<?= !$player->isNew() ? ' (ID: '. h($player->id) . ')' : '' ?></div>
    <ul class="detail_box">
        <li class="detail_box_item box-4">
            <?php
            echo $this->Form->control('name', [
                'label' => ['class' => 'label-row', 'text' => __d('model', 'name')],
                'class' => 'input-row name',
                'maxlength' => 20,
            ]);
            ?>
        </li>
        <li class="detail_box_item box-4">
            <?php
            echo $this->Form->control('name_english', [
                'label' => ['class' => 'label-row', 'text' => __d('model', 'name_english')],
                'class' => 'input-row name',
                'maxlength' => 40,
            ]);
            ?>
        </li>
        <li class="detail_box_item box-4">
            <?php
            echo $this->Form->control('name_other', [
                'label' => ['class' => 'label-row', 'text' => __d('model', 'name_other')],
                'class' => 'input-row name',
                'maxlength' => 20,
            ]);
            ?>
        </li>
        <li class="detail_box_item box-3">
            <div class="input">
                <div class="label-row"><?= __d('model', 'birthday') ?></div>
                <div class="input-row">
                    <?= $this->Form->text('birthday', [
                        'class' => 'birthday datepicker'
                    ]) ?>
                    <span class="age">
                        <?= h($player->age_text) ?>
                    </span>
                </div>
            </div>
        </li>
        <li class="detail_box_item box-3">
            <?php
            echo $this->Form->sexes([
                'label' => ['class' => 'label-row', 'text' => __d('model', 'sex')],
                'value' => $player->sex,
                'class' => 'input-row sex',
            ]);
            ?>
        </li>
        <li class="detail_box_item box-3">
            <?php
            echo $this->Form->control('rank_id', [
                'options' => $ranks,
                'label' => ['class' => 'label-row', 'text' => __d('model', 'rank_id')],
                'class' => 'input-row rank',
                'empty' => false,
            ]);
            ?>
        </li>
        <li class="detail_box_item box-3">
            <?php
            echo $this->Form->selectDate('input_joined', [
                'label' => ['class' => 'label-row', 'text' => __d('model', 'joined')],
                'class' => 'input-row',
                'empty' => [
                    'year' => false,
                    'month' => ['' => '-'],
                    'day' => ['' => '-'],
                ],
            ]);
            ?>
        </li>
        <li class="detail_box_item box-2">
            <?php
            echo $this->cell('Countries', [
                'hasTitleOnly' => true,
                [
                    'label' => ['class' => 'label-row', 'text' => __d('model', 'country_id')],
                    'empty' => false,
                    'value' => $player->country_id,
                    'class' => 'input-row',
                ],
            ])->render()
            ?>
        </li>
        <li class="detail_box_item box-3">
            <?php
            echo $this->cell('Organizations', [
                [
                    'label' => ['class' => 'label-row', 'text' => __d('model', 'organization_id')],
                    'empty' => false,
                    'value' => ($player->organization_id ? $player->organization_id : '1'),
                    'class' => 'input-row',
                ],
            ])->render()
            ?>
        </li>
        <li class="detail_box_item box-3">
            <div class="input">
                <div class="label-row">引退</div>
                <div class="input checkbox-with-text-field-row">
                    <?= $this->Form->control('is_retired', [
                        'label' => ['class' => 'checkbox-label', 'text' => '引退しました'],
                    ]) ?>
                    <?= $this->Form->text('retired', [
                        'class' => 'datepicker',
                        'disabled' => true,
                        'placeholder' => __d('model', 'retired'),
                    ]) ?>
                </div>
            </div>
        </li>
        <li class="detail_box_item box-4">
            <div class="input">
                <div class="label-row"><?= __d('model', 'modified') ?></div>
                <div class="label-field-row">
                    <span><?= h($this->Date->formatToDateTime($player->modified)) ?></span>
                    <?= $this->Form->hidden('modified') ?>
                </div>
            </div>
        </li>
        <li class="detail_box_item">
            <?= $this->Form->control('remarks', [
                'label' => ['class' => 'label-row', 'text' => __d('model', 'remarks')],
                'type' => 'textarea',
                'class' => 'input-row',
            ]) ?>
        </li>
        <li class="detail_box_item button-row">
            <?php  ?>
            <?php if ($player->isNew()) : ?>
                <?php
                echo $this->Form->control('is_continue', [
                    'label' => ['class' => 'checkbox-label', 'text' => '続けて登録'],
                    'checked' => true,
                ]);
                ?>
            <?php endif ?>
            <?= $this->Form->button('保存', ['class' => 'button button-primary']) ?>
        </li>
    </ul>
    <?= $this->Form->end() ?>
</section>
