<?php
/**
 * @var \Gotea\View\AppView $this ビューオブジェクト
 * @var \Gotea\Model\Entity\Player[]|\Cake\Collection\CollectionInterface $players 棋士一覧データ
 */
$isAdmin = $this->isAdmin();
?>
<section class="players">
    <?= $this->Form->create($form, ['class' => 'main-form', 'type' => 'get', 'url' => ['_name' => 'find_players']]) ?>
    <ul class="search-header">
        <li class="search-row">
            <div class="search-box">
                <?= $this->Form->control('name', [
                    'label' => ['text' => __d('model', 'name')],
                    'class' => 'name',
                    'value' => $this->getRequest()->getQuery('name'),
                ]) ?>
            </div>
            <div class="search-box">
                <?= $this->Form->control('name_english', [
                    'label' => ['text' => __d('model', 'name_english')],
                    'class' => 'name',
                    'value' => $this->getRequest()->getQuery('name_english'),
                ]) ?>
            </div>
            <div class="search-box">
                <?= $this->Form->control('name_other', [
                    'label' => ['text' => __d('model', 'name_other')],
                    'class' => 'name',
                    'value' => $this->getRequest()->getQuery('name_other'),
                ]) ?>
            </div>
        </li>
        <li class="search-row">
            <div class="search-box">
                <?= $this->cell('Countries', [
                    'hasTitleOnly' => true,
                    [
                        'label' => ['text' => __d('model', 'country_id')],
                        'class' => 'country',
                        'empty' => true,
                        'value' => $this->getRequest()->getQuery('country_id'),
                        '@change' => 'changeCountry($event)',
                    ],
                ])->render() ?>
            </div>
            <div class="search-box">
                <?= $this->cell('Organizations', [
                    [
                        'label' => ['text' => __d('model', 'organization_id')],
                        'class' => 'organization',
                        'empty' => true,
                        'value' => $this->getRequest()->getQuery('organization_id'),
                    ],
                ])->render() ?>
            </div>
            <div class="search-box">
                <?= $this->Form->control('rank_id', [
                    'label' => ['text' => __d('model', 'rank_id')],
                    'options' => $ranks,
                    'class' => 'rank',
                    'empty' => true,
                    'value' => $this->getRequest()->getQuery('rank_id'),
                ]) ?>
            </div>
            <div class="search-box">
                <?= $this->Form->sexes([
                    'label' => ['text' => __d('model', 'sex')],
                    'class' => 'sex',
                    'empty' => true,
                    'value' => $this->getRequest()->getQuery('sex'),
                ]) ?>
            </div>
            <div class="search-box">
                <div>
                    <?php
                    echo $this->Form->label('joined_from', '入段年', ['class' => 'search-box_label']);
                    echo $this->Form->number('joined_from', [
                        'class' => 'joined joined-from',
                        'min' => 1,
                        'max' => 9999,
                        'value' => $this->getRequest()->getQuery('joined_from'),
                    ]);
                    echo $this->form->label('joined_to', '～', ['class' => 'between-label']);
                    echo $this->Form->number('joined_to', [
                        'class' => 'joined joined-to',
                        'min' => 1,
                        'max' => 9999,
                        'value' => $this->getRequest()->getQuery('joined_to'),
                    ]);
                    ?>
                </div>
            </div>
            <div class="search-box">
                <?= $this->Form->filters('is_retired', [
                    'label' => ['text' => '引退者'],
                    'class' => 'excluded',
                    'value' => $this->getRequest()->getQuery('is_retired'),
                ]) ?>
            </div>
            <div class="search-box search-box-right">
                <?php if ($isAdmin) : ?>
                <add-button :country-id="countryId" :changed="changed" :url="'<?= $this->Url->build(['_name' => 'new_player']) ?>'" :param-id="'<?= $this->getRequest()->getData('country_id') ?>'"></add-button>
                <?php endif ?>
                <?= $this->Form->button('検索', ['type' => 'submit', 'class' => 'button button-primary']) ?>
            </div>
        </li>
    </ul>

    <?php if (!empty($players)) : ?>
        <?= $this->element('Paginator/default', ['url' => ['_name' => 'find_players']]) ?>
    <?php endif ?>

    <div class="search-results">
        <ul class="players table-header">
            <li class="table-row">
                <span class="table-column table-column_id"><?= __d('model', 'id') ?></span>
                <span class="table-column table-column_name"><?= __d('model', 'name') ?></span>
                <span class="table-column table-column_name"><?= __d('model', 'name_english') ?></span>
                <span class="table-column table-column_enrollment"><?= __d('model', 'joined') ?></span>
                <span class="table-column table-column_country"><?= __d('model', 'country_id') ?></span>
                <span class="table-column table-column_organization"><?= __d('model', 'organization_id') ?></span>
                <span class="table-column table-column_rank"><?= __d('model', 'rank_id') ?></span>
                <span class="table-column table-column_sex"><?= __d('model', 'sex') ?></span>
                <span class="table-column table-column_score">
                    <div class="table-column table-column_score-summary"><?= date('Y') ?>年国内</div>
                    <span class="table-column table-column_score-point">勝</span>
                    <span class="table-column table-column_score-point">敗</span>
                    <span class="table-column table-column_score-point">分</span>
                </span>
                <span class="table-column table-column_score">
                    <div class="table-column table-column_score-summary"><?= date('Y') ?>年国際</div>
                    <span class="table-column table-column_score-point">勝</span>
                    <span class="table-column table-column_score-point">敗</span>
                    <span class="table-column table-column_score-point">分</span>
                </span>
            </li>
        </ul>
        <?php if (!empty($players)) : ?>
            <ul class="players table-body">
                <?php foreach ($players as $player) : ?>
                    <li class="table-row<?= ($player->is_retired ? ' table-row-retired' : '') ?>">
                        <span class="table-column table-column_id">
                            <?= h($player->id) ?>
                        </span>
                        <span class="table-column table-column_name">
                            <a class="view-link<?= ($player->isFemale() ? ' female' : '') ?>" @click="openModal('<?= $this->Url->build(['_name' => 'view_player', $player->id]) ?>')">
                                <?= h($player->name) ?>
                            </a>
                        </span>
                        <span class="table-column table-column_name">
                            <?= h($player->name_english) ?>
                        </span>
                        <span class="table-column table-column_enrollment">
                            <?= h($player->format_joined) ?>
                        </span>
                        <span class="table-column table-column_country">
                            <?= h($player->country->name) ?>
                        </span>
                        <span class="table-column table-column_organization">
                            <?= h($player->organization->name) ?>
                        </span>
                        <span class="table-column table-column_rank">
                            <?= h($player->rank->name) ?>
                        </span>
                        <span class="table-column table-column_sex">
                            <?= h($player->sex) ?>
                        </span>
                        <span class="table-column table-column_score">
                            <span class="table-column table-column_score-point">
                                <?= h($player->win(null)) ?>
                            </span>
                            <span class="table-column table-column_score-point">
                                <?= h($player->lose(null)) ?>
                            </span>
                            <span class="table-column table-column_score-point">
                                <?= h($player->draw(null)) ?>
                            </span>
                        </span>
                        <span class="table-column table-column_score">
                            <span class="table-column table-column_score-point">
                                <?= h($player->win(null, true)) ?>
                            </span>
                            <span class="table-column table-column_score-point">
                                <?= h($player->lose(null, true)) ?>
                            </span>
                            <span class="table-column table-column_score-point">
                                <?= h($player->draw(null, true)) ?>
                            </span>
                        </span>
                    </li>
                <?php endforeach ?>
            </ul>
        <?php endif ?>
    </div>
    <?= $this->Form->end() ?>
</section>
