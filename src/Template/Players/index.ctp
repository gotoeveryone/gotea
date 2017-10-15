<section class="players">
    <?=$this->Form->create($form, [
        'id' => 'mainForm',
        'class' => 'main-form',
        'type' => 'post',
        'url' => ['action' => 'index'],
        'templates' => [
            'inputContainer' => '{{content}}',
            'textFormGroup' => '{{input}}',
            'selectFormGroup' => '{{input}}'
        ]
    ])?>
        <ul class="search-header">
            <li class="search-row">
                <div>
                    <label>所属国：</label>
                    <?= $this->cell('Countries', ['hasTitle' => true,
                        'customOptions' => [
                            '@change' => 'changeCountry($event)',
                        ],
                    ])->render() ?>
                </div>
                <div>
                    <label>所属組織：</label>
                    <?= $this->cell('Organizations', ['empty' => true])->render() ?>
                </div>
                <div>
                    <label>段位：</label>
                    <?= $this->cell('Ranks', ['empty' => true])->render() ?>
                </div>
                <div>
                    <label>性別：</label>
                    <?= $this->MyForm->sexes(['class' => 'sex', 'empty' => true]) ?>
                </div>
                <div>
                    <label>入段年：</label>
                    <?=$this->Form->number('joined_from', ['class' => 'joined', 'maxlength' => 4])?>
                    ～
                    <?=$this->Form->number('joined_to', ['class' => 'joined', 'maxlength' => 4])?>
                </div>
            </li>
            <li class="search-row">
                <div>
                    <label>棋士名：</label>
                    <?=$this->Form->text('name', ['class' => 'name', 'maxlength' => 20])?>
                </div>
                <div>
                    <label>（英語）：</label>
                    <?=$this->Form->text('name_english', ['class' => 'name', 'maxlength' => 40]);?>
                </div>
                <div>
                    <label>（その他）：</label>
                    <?=$this->Form->text('name_other', ['class' => 'name', 'maxlength' => 20]);?>
                </div>
            </li>
            <li class="search-row">
                <div>
                    <label>引退者：</label>
                    <?=
                        $this->Form->select('is_retired', [
                            '0' => '検索しない',
                            '1' => '検索する'
                        ], [
                            'class' => 'excluded'
                        ]);
                    ?>
                </div>
                <?php if (!empty($players) && count($players) > 0) : ?>
                <div class="result-count">
                    <?=count($players).'件のレコードが該当しました。'?>
                </div>
                <?php endif ?>
                <div class="button-wrap">
                    <add-button :country-id="countryId" :changed="changed"
                        :url="'<?= $this->Url->build(['action' => 'new']) ?>'"
                        :param-id="'<?= $this->request->getData('country_id') ?>'"></add-button>
                    <?=$this->Form->button('検索', ['type' => 'submit'])?>
                </div>
            </li>
        </ul>

        <div class="search-results">
            <ul class="players table-header">
                <li class="table-row">
                    <span class="id">ID</span>
                    <span class="name">棋士名</span>
                    <span class="name">棋士名（英語）</span>
                    <span class="enrollment">入段日</span>
                    <span class="country">所属国</span>
                    <span class="organization">所属組織</span>
                    <span class="rank">段位</span>
                    <span class="sex">性別</span>
                    <span class="score">
                        <div><?= date('Y') ?>年国内</div>
                        <div class="score-point">
                            <span>勝</span>
                            <span>敗</span>
                            <span>分</span>
                        </div>
                    </span>
                    <span class="score">
                        <div><?= date('Y') ?>年国際</div>
                        <div class="score-point">
                            <span>勝</span>
                            <span>敗</span>
                            <span>分</span>
                        </div>
                    </span>
                </li>
            </ul>
            <?php if (!empty($players) && count($players) > 0) : ?>
            <ul class="players table-body">
                <?php foreach ($players as $player) : ?>
                <li class="table-row<?= ($player->is_retired ? ' retired' : '') ?>">
                    <span class="id">
                        <?=h($player->id)?>
                    </span>
                    <span class="name">
                        <a class="player-link<?= ($player->isFemale() ? ' female' : '') ?>"
                            @click="openModal('<?= $this->Url->build(['action' => 'detail', $player->id]) ?>')">
                            <?=h($player->name)?>
                        </a>
                    </span>
                    <span class="name">
                        <?=h($player->name_english); ?>
                    </span>
                    <span class="enrollment">
                        <?=$this->Date->formatJoinDelimiterValue($player->joined, '/'); ?>
                    </span>
                    <span class="country">
                        <?=h($player->country->name); ?>
                    </span>
                    <span class="organization">
                        <?=h($player->organization->name); ?>
                    </span>
                    <span class="rank">
                        <?=h($player->rank->name); ?>
                    </span>
                    <span class="sex">
                        <?=h($player->sex); ?>
                    </span>
                    <span class="point">
                        <?= h($player->win(null)); ?>
                    </span>
                    <span class="point">
                        <?= h($player->lose(null)); ?>
                    </span>
                    <span class="point">
                        <?= h($player->draw(null)); ?>
                    </span>
                    <span class="point">
                        <?= h($player->win(null, true)); ?>
                    </span>
                    <span class="point">
                        <?= h($player->lose(null, true)); ?>
                    </span>
                    <span class="point">
                        <?= h($player->draw(null, true)); ?>
                    </span>
                </li>
                <?php endforeach ?>
            </ul>
            <?php endif ?>
        </div>
    <?=$this->Form->end()?>
</section>
