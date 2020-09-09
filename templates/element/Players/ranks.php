<?php
/**
 * @var \Gotea\View\AppView $this
 * @var \Gotea\Model\Entity\Player $player
 */
$isAdmin = $this->isAdmin();
?>
<section data-contentname="ranks" class="tab-contents">
    <div class="page-header">昇段情報<?= !$player->isNew() ? ' (ID: ' . h($player->id) . ')' : '' ?></div>
    <ul class="boxes">
        <?php if ($isAdmin) : ?>
        <li class="label-row">新規登録</li>
        <li>
            <?= $this->Form->create($player, [
                'class' => 'rank-form detail_box',
                'type' => 'post',
                'url' => ['_name' => 'create_ranks', $player->id],
            ]) ?>
            <div class="detail_box_item box-6">
                <?= $this->Form->control('rank_id', [
                    'label' => ['text' => __d('model', 'rank_id')],
                    'options' => $ranks,
                    'class' => 'rank',
                    'empty' => false,
                ]) ?>
            </div>
            <div class="detail_box_item box-3">
                <?= $this->Form->control('promoted', [
                    'label' => ['text' => __d('model', 'promoted')],
                    'type' => 'text',
                    'class' => 'datepicker',
                    'autocomplete' => 'off',
                ]) ?>
            </div>
            <div class="detail_box_item detail_box_item-buttons box-3">
                <?= $this->Form->control('newest', [
                    'label' => ['class' => 'checkbox-label', 'text' => '最新として登録'],
                    'checked' => true,
                ]) ?>
                <?= $this->Form->button('登録', ['class' => 'button button-primary']) ?>
            </div>
            <?= $this->Form->end() ?>
        </li>
        <?php endif ?>
        <li class="label-row">昇段履歴</li>
        <?php foreach ($player->player_ranks as $player_rank) : ?>
            <li>
                <?= $this->Form->create($player_rank, [
                    'class' => 'rank-form detail_box',
                    'type' => 'put',
                    'url' => ['_name' => 'update_ranks', $player_rank->player_id, $player_rank->id],
                ]) ?>
                <div class="detail_box_item box-6">
                    <?= $this->Form->control('rank_id', [
                        'label' => ['text' => __d('model', 'rank_id')],
                        'options' => $ranks,
                        'class' => 'rank',
                        'empty' => false,
                        'disabled' => !$isAdmin,
                    ]) ?>
                </div>
                <div class="detail_box_item box-3">
                    <?= $this->Form->control('promoted', [
                        'label' => ['text' => __d('model', 'promoted')],
                        'type' => 'text',
                        'class' => 'datepicker',
                        'autocomplete' => 'off',
                        'disabled' => !$isAdmin,
                    ]) ?>
                </div>
                <?php if ($isAdmin) : ?>
                <div class="detail_box_item detail_box_item-buttons box-3">
                    <?= $this->Form->button('更新', ['class' => 'button button-primary']) ?>
                </div>
                <?php endif ?>
                <?= $this->Form->end() ?>
            </li>
        <?php endforeach ?>
    </ul>
</section>
