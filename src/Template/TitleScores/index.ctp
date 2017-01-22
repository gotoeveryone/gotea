<section class="title-scores">
    <?=$this->Form->create(null, [
        'id' => 'mainForm',
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
                <label>棋士名：</label>
                <?=$this->Form->text('name', ['class' => 'name']);?>
                <label>所属国：</label>
                <?=
                    $this->Form->input('country_id', [
                        'options' => $countries,
                        'class' => 'country',
                        'empty' => true
                    ]);
                ?>
                <label>対局日：</label>
                <?=$this->Form->text('started', ['class' => 'date datepicker'])?>
                ～
                <?=$this->Form->text('ended', ['class' => 'date datepicker'])?>
                <div class="button-column">
                    <?=$this->Form->button('検索', ['type' => 'submit'])?>
                </div>
            </li>
        </ul>

        <div class="search-results">
            <ul class="table-header">
                <li class="table-row">
                    <span class="country">対象国</span>
                    <span class="date">日付</span>
                    <span class="name">勝者</span>
                    <span class="name">敗者</span>
                </li>
            </ul>
            <?php if (isset($titleScores)) : ?>
            <ul class="table-body">
                <?php foreach ($titleScores as $titleScore): ?>
                <li class="table-row">
                    <?=$this->Form->hidden('id', ['value' => $titleScore->id]);?>
                    <span class="country"><?= h($titleScore->country->name.'棋戦') ?></span>
                    <span class="date"><?= h($titleScore->date) ?></span>
                    <span class="name"><?= h($titleScore->win_detail->winner ? $titleScore->win_detail->winner->getNameWithRank() : '') ?></span>
                    <span class="name"><?= h($titleScore->lose_detail->loser ? $titleScore->lose_detail->loser->getNameWithRank() : '') ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif ?>
        </div>
    <?=$this->Form->end()?>
</section>
