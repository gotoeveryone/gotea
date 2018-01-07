<section class="title-scores">
    <div>
        <?=$this->Form->create($form, [
            'class' => 'main-form',
            'url' => ['_name' => 'find_scores'],
            'templates' => [
                'inputContainer' => '{{content}}',
                'textFormGroup' => '{{input}}',
                'selectFormGroup' => '{{input}}'
            ]
        ])?>
            <ul class="search-header">
                <li class="search-row">
                    <div>
                        <label class="search-row_label">棋士名：</label>
                        <?=$this->Form->text('name', ['class' => 'name', 'maxlength' => 20]);?>
                    </div>
                    <div>
                        <label class="search-row_label">対象棋戦：</label>
                        <?= $this->cell('Countries')->render() ?>
                    </div>
                    <div>
                        <label class="search-row_label">対局年：</label>
                        <?=
                            $this->Form->years('target_year', [
                                'class' => 'year', 'empty' => true,
                            ]);
                        ?>
                    </div>
                    <div>
                        <label class="search-row_label">対局日：</label>
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

    <div class="search-results">
        <ul class="table-header">
            <li class="table-row">
                <span class="table-column_id">ID</span>
                <span class="table-column_country">対象国</span>
                <span class="table-column_date">日付</span>
                <span class="table-column_name">勝者</span>
                <span class="table-column_name">敗者</span>
                <span class="table-column_operation">操作</span>
            </li>
        </ul>
        <?php if (!empty($titleScores) && $titleScores->count() > 0) : ?>
        <ul class="table-body">
            <?php foreach ($titleScores as $titleScore) : ?>
            <li class="table-row">
                <span class="table-column_id"><?= h($titleScore->id) ?></span>
                <span class="table-column_country"><?= h($titleScore->country->name.'棋戦') ?></span>
                <span class="table-column_date"><?= h($titleScore->date) ?></span>
                <span class="table-column_name"><?= h($titleScore->getWinnerName()) ?></span>
                <span class="table-column_name"><?= h($titleScore->getLoserName()) ?></span>
                <span class="table-column_operation">
                    <?= $this->Form->postButton('勝敗変更', [
                        '_name' => 'update_scores', $titleScore->id,
                    ], [
                        'method' => 'put',
                        'data' => [
                            'name' => $this->request->getData('name'),
                            'country_id' => $this->request->getData('country_id'),
                            'target_year' => $this->request->getData('target_year'),
                            'started' => $this->request->getData('started'),
                            'ended' => $this->request->getData('ended'),
                        ],
                    ]) ?>
                    <?= $this->Form->postButton('削除', [
                        '_name' => 'update_scores', $titleScore->id,
                    ], [
                        'method' => 'delete',
                        'data' => [
                            'name' => $this->request->getData('name'),
                            'country_id' => $this->request->getData('country_id'),
                            'target_year' => $this->request->getData('target_year'),
                            'started' => $this->request->getData('started'),
                            'ended' => $this->request->getData('ended'),
                        ],
                        'onclick' => "return confirm('タイトル成績情報を削除します。よろしいですか？')",
                    ]) ?>
                </span>
            </li>
            <?php endforeach ?>
        </ul>
        <?php endif ?>
    </div>
</section>
