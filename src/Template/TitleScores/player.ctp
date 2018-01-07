<section class="title-scores">
    <div class="search-results modal">
        <ul class="table-header">
            <li class="table-row">
                <span class="table-column_id">ID</span>
                <span class="table-column_country">対象国</span>
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
                <span class="table-column_date"><?= h($titleScore->date) ?></span>
                <span class="table-column_name">
                    <span <?= $titleScore->isSelected($titleScore->winner, $id) ? 'class="selected"' : '' ?>>
                        <?= h($titleScore->getWinnerName()) ?>
                    </span>
                </span>
                <span class="table-column_name">
                    <span <?= $titleScore->isSelected($titleScore->loser, $id) ? 'class="selected"' : '' ?>>
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
            '_name' => 'view_player', '?' => ['tab' => 'scores'], $id,
        ], [
            'class' => 'layout-button',
        ]) ?>
    </div>
</section>
