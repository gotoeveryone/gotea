<section class="title-scores">
    <?php if (!isset($isDialog)) : ?>
    <div>
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
                        <label>棋士名：</label>
                        <?=$this->Form->text('name', ['class' => 'name', 'maxlength' => 20]);?>
                    </div>
                    <div>
                        <label>対象棋戦：</label>
                        <?= $this->cell('Countries')->render() ?>
                    </div>
                    <div>
                        <label>対局年：</label>
                        <?=
                            $this->MyForm->years('target_year', [
                                'class' => 'year', 'empty' => true,
                            ]);
                        ?>
                    </div>
                    <div>
                        <label>対局日：</label>
                        <?=$this->Form->text('started', ['class' => 'date datepicker'])?>
                        <span>～</span>
                        <?=$this->Form->text('ended', ['class' => 'date datepicker'])?>
                    </div>
                    <div class="button-wrap">
                        <?=$this->Form->button('検索', ['type' => 'submit'])?>
                    </div>
                </li>
            </ul>
        <?=$this->Form->end()?>
    </div>
    <?php endif ?>

    <div class="search-results<?=(isset($isDialog) ? ' modal' : '')?>">
        <ul class="table-header">
            <li class="table-row">
                <span class="id">ID</span>
                <span class="country">対象国</span>
                <span class="date">日付</span>
                <span class="name">勝者</span>
                <span class="name">敗者</span>
                <?=!isset($isDialog) ? '<span class="operation">操作</span>' : ''?>
            </li>
        </ul>
        <?php if (isset($titleScores)) : ?>
        <ul class="table-body">
            <?php foreach ($titleScores as $titleScore) : ?>
            <li class="table-row">
                <span class="id"><?= h($titleScore->id) ?></span>
                <span class="country"><?= h($titleScore->country->name.'棋戦') ?></span>
                <span class="date"><?= h($titleScore->date) ?></span>
                <span class="name"><?= $titleScore->getWinner($this->request->getData('player_id')) ?></span>
                <span class="name"><?= $titleScore->getLoser($this->request->getData('player_id')) ?></span>
                <?php if (!isset($isDialog)) : ?>
                <span class="operation">
                    <?= $this->Form->postButton('勝敗変更', [
                        'action' => 'change',
                    ], [
                        'data' => [
                            'change_id' => $titleScore->id,
                            'name' => $this->request->getData('name'),
                            'country_id' => $this->request->getData('country_id'),
                            'target_year' => $this->request->getData('target_year'),
                            'started' => $this->request->getData('started'),
                            'ended' => $this->request->getData('ended'),
                        ],
                    ]) ?>
                    <?= $this->Form->postButton('削除', [
                        'action' => 'delete',
                    ], [
                        'data' => [
                            'delete_id' => $titleScore->id,
                            'name' => $this->request->getData('name'),
                            'country_id' => $this->request->getData('country_id'),
                            'target_year' => $this->request->getData('target_year'),
                            'started' => $this->request->getData('started'),
                            'ended' => $this->request->getData('ended'),
                        ],
                        'onclick' => "return confirm('タイトル成績情報を削除します。よろしいですか？')",
                    ]) ?>
                </span>
                <?php endif ?>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif ?>
    </div>
    <?php if (isset($isDialog)) : ?>
    <div class="button-row">
        <?=$this->Html->link('戻る', [
            'controller' => 'players', 'action' => 'detail',
            '?' => ['tab' => 'scores'], $this->request->data('player_id')
        ])?>
    </div>
    <?php endif ?>
</section>
