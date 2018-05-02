<section class="players">
    <?= $this->Form->create($form, ['class' => 'main-form', 'url' => ['_name' => 'find_players']]) ?>
        <ul class="search-header">
            <li class="search-row">
                <?php
                    echo $this->cell('Countries', [
                        'hasTitleOnly' => true,
                        [
                            'label' => ['class' => 'search-row_label', 'text' => '所属国'],
                            'class' => 'country',
                            'empty' => true,
                            '@change' => 'changeCountry($event)',
                        ],
                    ])->render();
                    echo $this->cell('Organizations', [
                        [
                            'label' => ['class' => 'search-row_label', 'text' => '所属組織'],
                            'class' => 'organization',
                            'empty' => true,
                        ]
                    ])->render();
                    echo $this->Form->control('rank_id', [
                        'label' => ['class' => 'search-row_label', 'text' => '段位'],
                        'options' => $ranks,
                        'class' => 'rank',
                        'empty' => true,
                    ]);
                    echo $this->Form->sexes([
                        'label' => ['class' => 'search-row_label', 'text' => '性別'],
                        'class' => 'sex',
                        'empty' => true,
                    ]);
                ?>
                <div>
                    <?php
                        echo $this->Form->label('joined_from', '入段年', ['class' => 'search-row_label']);
                        echo $this->Form->number('joined_from', [
                            'class' => 'joined joined-from',
                            'min' => 1,
                            'max' => 9999,
                        ]);
                        echo $this->form->label('joined_to', '～');
                        echo $this->Form->number('joined_to', [
                            'class' => 'joined joined-to',
                            'min' => 1,
                            'max' => 9999,
                        ]);
                    ?>
                </div>
            </li>
            <li class="search-row">
                <?php
                    echo $this->Form->control('name', [
                        'label' => ['class' => 'search-row_label', 'text' => '棋士名'],
                        'class' => 'name',
                    ]);
                    echo $this->Form->control('name_english', [
                        'label' => ['class' => 'search-row_label', 'text' => '（英語）'],
                        'class' => 'name',
                    ]);
                    echo $this->Form->control('name_other', [
                        'label' => ['class' => 'search-row_label', 'text' => '（その他）'],
                        'class' => 'name',
                    ]);
                ?>
            </li>
            <li class="search-row">
                <?php
                    echo $this->Form->filters('is_retired', [
                        'label' => ['class' => 'search-row_label', 'text' => '引退者'],
                        'class' => 'excluded',
                    ]);
                ?>
                <?php if (!empty($players)) : ?>
                <div class="result-count">
                    <?= h("{$players->count()}件のレコードが該当しました。") ?>
                </div>
                <?php endif ?>
                <div class="button-wrap">
                    <add-button :country-id="countryId" :changed="changed"
                        :url="'<?= $this->Url->build(['_name' => 'new_player']) ?>'"
                        :param-id="'<?= $this->request->getData('country_id') ?>'"></add-button>
                    <?=$this->Form->button('検索', ['type' => 'submit'])?>
                </div>
            </li>
        </ul>

        <div class="search-results">
            <ul class="players table-header">
                <li class="table-row">
                    <span class="table-column_id">ID</span>
                    <span class="table-column_name">棋士名</span>
                    <span class="table-column_name">棋士名（英語）</span>
                    <span class="table-column_enrollment">入段日</span>
                    <span class="table-column_country">所属国</span>
                    <span class="table-column_organization">所属組織</span>
                    <span class="table-column_rank">段位</span>
                    <span class="table-column_sex">性別</span>
                    <span class="table-column_score">
                        <div><?= date('Y') ?>年国内</div>
                        <div class="table-column_score-point">
                            <span>勝</span>
                            <span>敗</span>
                            <span>分</span>
                        </div>
                    </span>
                    <span class="table-column_score">
                        <div><?= date('Y') ?>年国際</div>
                        <div class="table-column_score-point">
                            <span>勝</span>
                            <span>敗</span>
                            <span>分</span>
                        </div>
                    </span>
                </li>
            </ul>
            <?php if (!empty($players)) : ?>
            <ul class="players table-body">
                <?php foreach ($players as $player) : ?>
                <li class="table-row<?= ($player->is_retired ? ' table-row-retired' : '') ?>">
                    <span class="table-column_id">
                        <?= h($player->id) ?>
                    </span>
                    <span class="table-column_name">
                        <a class="view-link<?= ($player->isFemale() ? ' female' : '') ?>"
                            @click="openModal('<?= $this->Url->build(['_name' => 'view_player', $player->id]) ?>')">
                            <?= h($player->name) ?>
                        </a>
                    </span>
                    <span class="table-column_name">
                        <?= h($player->name_english) ?>
                    </span>
                    <span class="table-column_enrollment">
                        <?= h($player->format_joined) ?>
                    </span>
                    <span class="table-column_country">
                        <?= h($player->country->name) ?>
                    </span>
                    <span class="table-column_organization">
                        <?= h($player->organization->name) ?>
                    </span>
                    <span class="table-column_rank">
                        <?= h($player->rank->name) ?>
                    </span>
                    <span class="table-column_sex">
                        <?= h($player->sex) ?>
                    </span>
                    <span class="table-column_point">
                        <?= h($player->win(null)) ?>
                    </span>
                    <span class="table-column_point">
                        <?= h($player->lose(null)) ?>
                    </span>
                    <span class="table-column_point">
                        <?= h($player->draw(null)) ?>
                    </span>
                    <span class="table-column_point">
                        <?= h($player->win(null, true)) ?>
                    </span>
                    <span class="table-column_point">
                        <?= h($player->lose(null, true)) ?>
                    </span>
                    <span class="table-column_point">
                        <?= h($player->draw(null, true)) ?>
                    </span>
                </li>
                <?php endforeach ?>
            </ul>
            <?php endif ?>
        </div>
    <?=$this->Form->end()?>
</section>
