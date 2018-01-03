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
        <p>
            <a class="view-link<?= ($row->player->isFemale() ? ' female' : '') ?>"
                @click="openModal('<?= $this->Url->build(['_name' => 'view_player', $row->player->id]) ?>')">
                <?= h($row->player->name) ?>
            </a>
            <span class="recent-ranks-data-row-text">
                <?= h($row->rank->name) ?>に昇段
            </span>
        </p>
    </li>
    <?php endforeach ?>
    <?php endforeach ?>
</ul>
<?php endif ?>
