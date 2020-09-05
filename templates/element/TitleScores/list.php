<?php
/**
 * @var \Gotea\View\AppView $this
 * @var \Gotea\Model\Entity\Player $player
 * @var \Gotea\Model\Entity\TitleScore[]|\Cake\Collection\CollectionInterface $titleScores
 */
$isAdmin = $this->isAdmin();
?>
<ul class="table-header">
    <li class="table-row">
        <span class="table-column table-column_id">ID</span>
        <span class="table-column table-column_country">棋戦分類</span>
        <span class="table-column table-column_title">タイトル名</span>
        <span class="table-column table-column_date">対局日</span>
        <span class="table-column table-column_name">勝者</span>
        <span class="table-column table-column_name">敗者</span>
        <span class="table-column table-column_result">結果</span>
        <?php if (empty($player)) : ?>
        <span class="table-column table-column_operation">操作</span>
        <?php endif ?>
    </li>
</ul>
<?php if (!empty($titleScores) && $titleScores->count() > 0) : ?>
<ul class="table-body">
    <?php foreach ($titleScores as $titleScore) : ?>
        <li class="table-row<?= (!$titleScore->is_official ? ' table-row-unofficial' : '') ?>">
            <span class="table-column table-column_id">
                <?= h($titleScore->id) ?>
            </span>
            <span class="table-column table-column_country"><?= h($titleScore->country->name) ?></span>
            <span class="table-column table-column_title"><?= h($titleScore->name) ?></span>
            <span class="table-column table-column_date">
                <?php foreach ($titleScore->game_dates as $idx => $date) : ?>
                    <?php if ($idx > 0) :
                        ?><br />〜<?php
                    endif ?><?= h($date) ?>
                <?php endforeach ?>
            </span>
            <span class="table-column table-column_name">
                <?php if (!empty($player)) : ?>
                <span <?= $titleScore->isSelected($titleScore->winner, $player->id) ? 'class="selected"' : '' ?>>
                    <?= h($titleScore->winner_name) ?>
                </span>
                <?php else : ?>
                    <?php if (!empty($titleScore->winner)) : ?>
                    <a class="view-link" @click="openModal('<?= $this->Url->build(['_name' => 'view_player', $titleScore->winner->id]) ?>')">
                        <?= h($titleScore->winner_name) ?>
                    </a>
                    <?php else : ?>
                        <?= h($titleScore->winner_name) ?>
                    <?php endif ?>
                <?php endif ?>
            </span>
            <span class="table-column table-column_name">
                <?php if (!empty($player)) : ?>
                <span <?= $titleScore->isSelected($titleScore->loser, $player->id) ? 'class="selected"' : '' ?>>
                    <?= h($titleScore->loser_name) ?>
                </span>
                <?php else : ?>
                    <?php if (!empty($titleScore->loser)) : ?>
                    <a class="view-link" @click="openModal('<?= $this->Url->build(['_name' => 'view_player', $titleScore->loser->id]) ?>')">
                        <?= h($titleScore->loser_name) ?>
                    </a>
                    <?php else : ?>
                        <?= h($titleScore->loser_name) ?>
                    <?php endif ?>
                <?php endif ?>
            </span>
            <span class="table-column table-column_result">
                <?= h($titleScore->result) ?>
            </span>
            <?php if (empty($player)) : ?>
            <span class="table-column table-column_operation">
                <a class="view-link layout-button button-primary" @click="openModal('<?= $this->Url->build(['_name' => 'view_score', $titleScore->id]) ?>')">
                    <?= __($isAdmin ? 'Edit' : 'View') ?>
                </a>
                <?php if ($isAdmin) : ?>
                    <?= $this->Form->postButton(__('Delete'), [
                    '_name' => 'delete_score', $titleScore->id,
                ], [
                    'method' => 'delete',
                    'data' => [
                        'name' => $this->getRequest()->getQuery('name'),
                        'title_name' => $this->getRequest()->getQuery('title_name'),
                        'country_id' => $this->getRequest()->getQuery('country_id'),
                        'target_year' => $this->getRequest()->getQuery('target_year'),
                        'started' => $this->getRequest()->getQuery('started'),
                        'ended' => $this->getRequest()->getQuery('ended'),
                    ],
                    'confirm' => __('Are you sure you want to delete # {0}?', $titleScore->id),
                    'class' => 'button button-danger',
                ]) ?>
                <?php endif ?>
            </span>
            <?php endif ?>
        </li>
    <?php endforeach ?>
</ul>
<?php endif ?>
