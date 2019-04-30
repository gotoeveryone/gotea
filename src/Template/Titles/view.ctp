<?= $this->Html->css('view', ['block' => true]) ?>
<div class="detail-dialog">
    <!-- タブ -->
    <ul class="tabs" data-selecttab="<?= $this->getRequest()->getQuery('tab') ?>">
        <li class="tab" data-tabname="title">タイトル情報</li>
        <li class="tab" data-tabname="histories">保持履歴</li>
    </ul>

    <!-- 詳細 -->
    <div class="detail">
        <!-- マスタ -->
        <section data-contentname="title" class="tab-contents">
            <?= $this->Form->create($title, ['class' => 'main-form', 'url' => ['_name' => 'update_title', $title->id]]) ?>
            <?= $this->Form->control('id') ?>
            <div class="category-row"><?= 'タイトル情報（ID：' . h($title->id) . '）' ?></div>
            <ul class="detail_box">
                <li class="detail_box_item box-4">
                    <?php
                    echo $this->Form->control('name', [
                        'label' => ['class' => 'label-row', 'text' => __d('model', 'name')],
                        'class' => 'input-row',
                        'maxlength' => 30,
                    ]);
                    ?>
                </li>
                <li class="detail_box_item box-4">
                    <?php
                    echo $this->Form->control('name_english', [
                        'label' => ['class' => 'label-row', 'text' => __d('model', 'name_english')],
                        'class' => 'input-row',
                        'maxlength' => 30,
                    ]);
                    ?>
                </li>
                <li class="detail_box_item box-1">
                    <div class="input">
                        <div class="label-row">分類</div>
                        <div class="label-field-row">
                            <?= h($title->country->name . '棋戦') ?>
                        </div>
                    </div>
                </li>
                <li class="detail_box_item box-1">
                    <div class="input">
                        <?php
                        echo $this->Form->label('is_team', __d('model', 'is_team'), ['class' => 'label-row']);
                        echo $this->Form->control('is_team', [
                            'label' => false,
                            'class' => 'input-row',
                        ]);
                        ?>
                    </div>
                </li>
                <li class="detail_box_item box-1">
                    <div class="input">
                        <?php
                        echo $this->Form->label('is_closed', __d('model', 'is_closed'), ['class' => 'label-row']);
                        echo $this->Form->control('is_closed', [
                            'label' => false,
                            'class' => 'input-row',
                        ]);
                        ?>
                    </div>
                </li>
                <li class="detail_box_item box-1">
                    <div class="input">
                        <?php
                        echo $this->Form->label('is_output', __d('model', 'is_output'), ['class' => 'label-row']);
                        echo $this->Form->control('is_output', [
                            'label' => false,
                            'class' => 'input-row',
                        ]);
                        ?>
                    </div>
                </li>
                <li class="detail_box_item box-2">
                    <?php
                    echo $this->Form->control('holding', [
                        'label' => ['class' => 'label-row', 'text' => __d('model', 'holding')],
                        'class' => 'input-row input-short',
                        'maxlength' => 3,
                    ]);
                    ?>
                </li>
                <li class="detail_box_item box-2">
                    <div class="input">
                        <div class="label-row">現在の保持者</div>
                        <div class="label-field-row"><?= h($title->getWinnerName(true)) ?></div>
                    </div>
                </li>
                <li class="detail_box_item box-2">
                    <?php
                    echo $this->Form->control('html_file_name', [
                        'label' => ['class' => 'label-row', 'text' => __d('model', 'html_file_name')],
                        'class' => 'input-row',
                        'maxlength' => 10,
                    ]);
                    ?>
                </li>
                <li class="detail_box_item box-2">
                    <?php
                    echo $this->Form->control('html_file_modified', [
                        'label' => ['class' => 'label-row', 'text' => __d('model', 'html_file_modified')],
                        'type' => 'text',
                        'class' => 'input-row datepicker',
                    ]);
                    ?>
                </li>
                <li class="detail_box_item box-4">
                    <div class="input">
                        <div class="label-row"><?= __d('model', 'modified') ?></div>
                        <div class="label-field-row">
                            <?= h($this->Date->formatToDateTime($title->modified)) ?>
                            <?= $this->Form->hidden('modified') ?>
                        </div>
                    </div>
                </li>
                <li class="detail_box_item">
                    <?= $this->Form->control('remarks', [
                        'label' => ['class' => 'label-row', 'text' => __d('model', 'remarks')],
                        'type' => 'textarea',
                        'class' => 'input-row',
                    ]) ?>
                </li>
                <li class="button-row">
                    <?= $this->Form->button(__('Save'), ['class' => 'button button-primary']) ?>
                </li>
            </ul>
            <?= $this->Form->end() ?>
        </section>

        <!-- タイトル取得履歴 -->
        <section data-contentname="histories" class="tab-contents">
            <?= $this->Form->create(null, [
                'class' => 'add-condition-form',
                'type' => 'post',
                'url' => ['_name' => 'save_histories', $title->id],
            ]) ?>
            <?= $this->Form->hidden('name', ['value' => $title->name]) ?>
            <div class="category-row">保持情報</div>
            <add-history :history-id="historyId" :is-team="<?= $title->is_team ? 'true' : 'false' ?>" @cleared="clearHistory()"></add-history>
            <ul class="boxes">
                <?php if (!empty(($title->retention_histories))) : ?>
                    <?php if (($retention = $title->now_retention)) : ?>
                        <li class="label-row">現在の保持情報</li>
                        <li class="detail_box">
                            <div class="detail_box_item box-10">
                                <span class="inner-column"><?= h($retention->target_year) . '年' ?></span>
                                <span class="inner-column"><?= h($retention->holding) . '期' ?></span>
                                <span class="inner-column"><span>タイトル名：</span><?= h($retention->name) ?></span>
                                <span class="inner-column"><?= h($retention->team_label) ?></span>
                                <span class="inner-column"><span>優勝者：</span><?= h($retention->winner_name) ?></span>
                                <?php if ($retention->isRecent()) : ?>
                                    <span class="inner-column"><span class="mark-new">NEW!</span></span>
                                <?php endif ?>
                            </div>
                            <div class="detail_box_item detail_box_item-buttons box-2">
                                <button type="button" class="button button-secondary" value="edit" @click="select('<?= $retention->id ?>')">編集</button>
                            </div>
                        </li>
                    <?php endif ?>
                    <li class="label-row">保持情報（履歴）</li>
                    <?php foreach ($title->histories as $history) : ?>
                        <li class="detail_box">
                            <div class="detail_box_item box-10">
                                <span class="inner-column"><?= h($history->target_year) . '年' ?></span>
                                <span class="inner-column"><?= h($history->holding) . '期' ?></span>
                                <span class="inner-column"><span>タイトル名：</span><?= h($history->name) ?></span>
                                <span class="inner-column"><?= h($history->team_label) ?></span>
                                <span class="inner-column"><span>優勝者：</span><?= h($history->winner_name) ?></span>
                            </div>
                            <div class="detail_box_item detail_box_item-buttons box-2">
                                <button type="button" class="button button-secondary" value="edit" @click="select('<?= $history->id ?>')">編集</button>
                            </div>
                        </li>
                    <?php endforeach ?>
                <?php endif ?>
            </ul>
            <?= $this->Form->end() ?>
        </section>
    </div>
</div>
