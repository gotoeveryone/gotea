<section class="title-scores">
    <div class="detail-point">
        <div><?= h($player->name_with_rank) ?>の成績</div>
        <div><?= h($year) ?>年: <?= h($detail->win_point) ?>勝<?= h($detail->lose_point) ?>敗</div>
    </div>
    <div class="search-results modal">
        <ul class="table-header">
            <li class="table-row">
                <span class="table-column_id">ID</span>
                <span class="table-column_country">対象国</span>
                <span class="table-column_title">タイトル名</span>
                <span class="table-column_date">日付</span>
                <span class="table-column_name">勝者</span>
                <span class="table-column_name">敗者</span>
            </li>
        </ul>
        <?php if (!empty($titleScores) && $titleScores->count() > 0) : ?>
        <ul class="table-body">
            <?php foreach ($titleScores as $titleScore) : ?>
            <li class="table-row">
                <span class="table-column_id"><?= h($titleScore->id) ?></span>
                <span class="table-column_country"><?= h($titleScore->country->name.'棋戦') ?></span>
                <span class="table-column_title"><?= h($titleScore->name) ?></span>
                <span class="table-column_date">
                    <?php foreach ($titleScore->dates as $idx => $date) : ?>
                    <?php if ($idx > 0) : ?><br/>〜<?php endif ?><?= h($date) ?>
                    <?php endforeach ?>
                </span>
                <span class="table-column_name">
                    <span <?= $titleScore->isSelected($titleScore->winner, $player->id) ? 'class="selected"' : '' ?>>
                        <?= h($titleScore->getWinnerName()) ?>
                    </span>
                </span>
                <span class="table-column_name">
                    <span <?= $titleScore->isSelected($titleScore->loser, $player->id) ? 'class="selected"' : '' ?>>
                        <?= h($titleScore->getLoserName()) ?>
                    </span>
                </span>
            </li>
            <?php endforeach ?>
        </ul>
        <?php endif ?>
    </div>
    <div class="button-row">
        <?= $this->Html->link('戻る', [
            '_name' => 'view_player', '?' => ['tab' => 'scores'], $player->id,
        ], [
            'class' => 'layout-button',
        ]) ?>
    </div>
</section>
