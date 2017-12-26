<?php if (!$recents->isEmpty()) : ?>
<div class="recent-ranks-title">
    <p class="recent-ranks-title-text">最近の昇段者</p>
</div>
<ul class="recent-ranks-data">
    <?php foreach ($recents as $promoted => $recent) : ?>
    <li class="recent-ranks-data-header">
        <p><?= h($promoted) ?></p>
    </li>
    <?php foreach ($recent as $row) : ?>
    <li class="recent-ranks-data-row">
        <p><?= h($row->player->name) ?> <?= h($row->rank->name) ?>に昇段</p>
    </li>
    <?php endforeach ?>
    <?php endforeach ?>
</ul>
<?php endif ?>
