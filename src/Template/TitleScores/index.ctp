<section class="title-scores">
    <div>
        <?= $this->Form->create($form, ['class' => 'main-form', 'type' => 'get', 'url' => ['_name' => 'find_scores']]) ?>
        <ul class="search-header">
            <li class="search-row">
                <fieldset class="search-box">
                    <?= $this->Form->control('name', [
                        'label' => ['class' => 'search-box_label', 'text' => '棋士名'],
                        'class' => 'name',
                        'value' => $this->request->getQuery('name'),
                    ]) ?>
                </fieldset>
                <fieldset class="search-box">
                    <?= $this->Form->control('title_name', [
                        'label' => ['class' => 'search-box_label', 'text' => 'タイトル名'],
                        'class' => 'title_name',
                        'value' => $this->request->getQuery('title_name'),
                    ]) ?>
                </fieldset>
            </li>
            <li class="search-row">
                <fieldset class="search-box">
                    <?= $this->cell('Countries', [
                        'hasTitleOnly' => false,
                        [
                            'label' => [
                                'class' => 'search-box_label',
                                'text' => '棋戦分類',
                            ],
                            'value' => $this->request->getQuery('country_id'),
                        ],
                    ]) ?>
                </fieldset>
                <fieldset class="search-box">
                    <?= $this->Form->years('target_year', [
                        'label' => ['class' => 'search-box_label', 'text' => '対局年'],
                        'class' => 'year',
                        'empty' => true,
                        'value' => $this->request->getQuery('target_year'),
                    ]) ?>
                </fieldset>
                <fieldset class="search-box">
                    <?php
                        echo $this->Form->label('started', '対局日', ['class' => 'search-box_label']);
                        echo $this->Form->text('started', [
                            'class' => 'started datepicker',
                            'value' => $this->request->getQuery('started'),
                        ]);
                        echo $this->form->label('ended', '～');
                        echo $this->Form->text('ended', [
                            'class' => 'ended datepicker',
                            'value' => $this->request->getQuery('ended'),
                        ]);
                    ?>
                </fieldset>
                <fieldset class="search-box search-box-right">
                    <?= $this->Form->button('検索', ['type' => 'submit', 'class' => 'button button-primary']) ?>
                </fieldset>
            </li>
        </ul>
        <?=$this->Form->end()?>
    </div>

    <?php if (!empty($titleScores)) : ?>
    <?= $this->element('Paginator/default', ['url' => ['_name' => 'find_scores']]) ?>
    <?php endif ?>

    <div class="search-results">
        <ul class="table-header">
            <li class="table-row">
                <span class="table-column table-column_id">ID</span>
                <span class="table-column table-column_country">棋戦分類</span>
                <span class="table-column table-column_title">タイトル名</span>
                <span class="table-column table-column_date">対局日</span>
                <span class="table-column table-column_name">勝者</span>
                <span class="table-column table-column_name">敗者</span>
                <span class="table-column table-column_operation">操作</span>
            </li>
        </ul>
        <?php if (!empty($titleScores) && $titleScores->count() > 0) : ?>
        <ul class="table-body">
            <?php foreach ($titleScores as $titleScore) : ?>
            <li class="table-row">
                <span class="table-column table-column_id"><?= h($titleScore->id) ?></span>
                <span class="table-column table-column_country"><?= h($titleScore->country->name.'棋戦') ?></span>
                <span class="table-column table-column_title"><?= h($titleScore->name) ?></span>
                <span class="table-column table-column_date">
                    <?php foreach ($titleScore->dates as $idx => $date) : ?>
                    <?php if ($idx > 0) : ?><br/>〜<?php endif ?><?= h($date) ?>
                    <?php endforeach ?>
                </span>
                <span class="table-column table-column_name"><?= h($titleScore->getWinnerName()) ?></span>
                <span class="table-column table-column_name"><?= h($titleScore->getLoserName()) ?></span>
                <span class="table-column table-column_operation">
                    <?= $this->Form->postButton('勝敗変更', [
                        '_name' => 'update_scores', $titleScore->id,
                    ], [
                        'method' => 'put',
                        'data' => [
                            'name' => $this->request->getQuery('name'),
                            'title_name' => $this->request->getQuery('title_name'),
                            'country_id' => $this->request->getQuery('country_id'),
                            'target_year' => $this->request->getQuery('target_year'),
                            'started' => $this->request->getQuery('started'),
                            'ended' => $this->request->getQuery('ended'),
                        ],
                        'class' => 'button button-primary',
                    ]) ?>
                    <?= $this->Form->postButton('削除', [
                        '_name' => 'update_scores', $titleScore->id,
                    ], [
                        'method' => 'delete',
                        'data' => [
                            'name' => $this->request->getQuery('name'),
                            'title_name' => $this->request->getQuery('title_name'),
                            'country_id' => $this->request->getQuery('country_id'),
                            'target_year' => $this->request->getQuery('target_year'),
                            'started' => $this->request->getQuery('started'),
                            'ended' => $this->request->getQuery('ended'),
                        ],
                        'onclick' => "return confirm('タイトル成績情報を削除します。よろしいですか？')",
                        'class' => 'button button-danger',
                    ]) ?>
                </span>
            </li>
            <?php endforeach ?>
        </ul>
        <?php endif ?>
    </div>
</section>
