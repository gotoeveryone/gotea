<section class="title-scores">
    <div>
        <?= $this->Form->create($form, ['class' => 'main-form', 'url' => ['_name' => 'find_scores']]) ?>
        <ul class="search-header">
            <li class="search-row">
                <?php
                    echo $this->Form->control('name', [
                        'label' => ['class' => 'search-row_label', 'text' => '棋士名'],
                        'class' => 'name',
                        'maxlength' => 20,
                    ]);
                    echo $this->cell('Countries', [
                        'hasTitleOnly' => false,
                        [
                            'label' => ['class' => 'search-row_label', 'text' => '棋戦分類'],
                        ],
                    ]);
                    echo $this->Form->years('target_year', [
                        'label' => ['class' => 'search-row_label', 'text' => '対局年'],
                        'class' => 'year',
                        'empty' => true,
                    ]);
                ?>
            </li>
            <li class="search-row">
                <div>
                    <?php
                        echo $this->Form->label('started', '対局日', ['class' => 'search-row_label']);
                        echo $this->Form->text('started', ['class' => 'started datepicker']);
                        echo $this->form->label('ended', '～');
                        echo $this->Form->text('ended', ['class' => 'ended datepicker']);
                    ?>
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
                <span class="table-column_country">棋戦分類</span>
                <span class="table-column_date">対局日</span>
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
