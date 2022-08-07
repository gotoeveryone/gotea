<?php
declare(strict_types=1);

/**
 * @var \Cake\Collection\CollectionInterface $recents
 */
?>
<?php if (!$recents->isEmpty()) : ?>
<div class="recent-ranks-title">
    <p class="recent-ranks-title-text">最近の昇段者</p>
</div>
<ul class="recent-ranks-data">
    <?php foreach ($recents as $promoted => $recent) : ?>
    <li>
        <ul>
            <li class="recent-ranks-data-header">
                <p><?= h($promoted) ?></p>
            </li>
            <?php foreach ($recent as $row) : ?>
            <li class="recent-ranks-data-row">
                <a class="recent-ranks-data-row-text view-link<?= ($row->player->isFemale() ? ' female' : '') ?>"
                    @click="openModal('<?= $this->Url->build(['_name' => 'view_player', $row->player->id]) ?>')">
                    <?= h($row->player->name_with_country) ?>
                </a>
                <span class="recent-ranks-data-row-text right">
                    <?= h($row->rank->name) ?>に昇段
                </span>
            </li>
            <?php endforeach ?>
        </ul>
    </li>
    <?php endforeach ?>
</ul>
<?php endif ?>
