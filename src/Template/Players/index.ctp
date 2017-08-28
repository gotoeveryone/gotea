<section class="players">
    <?=$this->Form->create($form, [
        'id' => 'mainForm',
        'class' => 'mainForm',
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
                <label>所属国：</label>
                <?= $this->cell('Countries', ['hasTitle' => true,
                    'customOptions' => [
                        '@change' => 'changeCountry($event)',
                    ],
                ])->render() ?>
                <label>所属組織：</label>
                <?= $this->cell('Organizations', ['empty' => true])->render() ?>
                <label>段位：</label>
                <?= $this->cell('Ranks', ['empty' => true])->render() ?>
                <label>性別：</label>
                <?= $this->MyForm->sexes(['class' => 'sex', 'empty' => true]) ?>
                <label>引退者：</label>
                <?=
                    $this->Form->select('is_retired', [
                        '0' => '検索しない',
                        '1' => '検索する'
                    ], [
                        'class' => 'excluded'
                    ]);
                ?>
            </li>
            <li class="search-row">
                <label>棋士名：</label>
                <?=$this->Form->text('name', ['class' => 'name', 'maxlength' => 20])?>
                <label>（英語）：</label>
                <?=$this->Form->text('name_english', ['class' => 'name', 'maxlength' => 40]);?>
                <label>（その他）：</label>
                <?=$this->Form->text('name_other', ['class' => 'name', 'maxlength' => 20]);?>
                <label>入段年：</label>
                <?=$this->Form->text('joined_from', ['class' => 'joined', 'maxlength' => 4])?>
                ～
                <?=$this->Form->text('joined_to', ['class' => 'joined', 'maxlength' => 4])?>
            </li>
            <li class="search-row">
                <?php if (!empty($players) && count($players) > 0) { ?>
                <div class="result-count">
                    <?=count($players).'件のレコードが該当しました。'?>
                </div>
                <?php } ?>
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
                        <?=date('Y')?>年国内<br/>
                        <span class="point">勝</span>
                        <span class="point">敗</span>
                        <span class="point">分</span>
                    </span>
                    <span class="score">
                        <?=date('Y')?>年国際<br/>
                        <span class="point">勝</span>
                        <span class="point">敗</span>
                        <span class="point">分</span>
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
                        <a class="<?= ($player->sex === '女性' ? 'female' : 'blue') ?>"
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
                        <?= h($player->win($scores, null)); ?>
                    </span>
                    <span class="point">
                        <?= h($player->lose($scores, null)); ?>
                    </span>
                    <span class="point">
                        <?= h($player->draw($scores, null)); ?>
                    </span>
                    <span class="point">
                        <?= h($player->win($scores, null, true)); ?>
                    </span>
                    <span class="point">
                        <?= h($player->lose($scores, null, true)); ?>
                    </span>
                    <span class="point">
                        <?= h($player->draw($scores, null, true)); ?>
                    </span>
                </li>
                <?php endforeach ?>
            <?php endif ?>
            </ul>
        </div>
    <?=$this->Form->end()?>
</section>
