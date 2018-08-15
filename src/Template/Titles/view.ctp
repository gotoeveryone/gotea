<?= $this->Html->css('view', ['block' => true]) ?>
<div class="detail-dialog">
    <!-- タブ -->
    <ul class="tabs" data-selecttab="<?= $this->request->getQuery('tab') ?>">
        <li class="tab" data-tabname="title">タイトル情報</li>
        <li class="tab" data-tabname="histories">保持履歴</li>
    </ul>

    <!-- 詳細 -->
    <fieldset class="detail">
        <!-- マスタ -->
        <section data-contentname="title" class="tab-contents">
            <?= $this->Form->create($title, ['class' => 'main-form', 'url' => ['_name' => 'update_title', $title->id]]) ?>
                <?= $this->Form->control('id') ?>
                <div class="category-row"><?='タイトル情報（ID：'.h($title->id).'）'?></div>
                <ul class="boxes">
                    <li class="detail-row">
                        <fieldset class="detail-box box1">
                            <?php
                                echo $this->Form->control('name', [
                                    'label' => ['class' => 'label-row', 'text' => 'タイトル名'],
                                    'class' => 'input-row',
                                    'maxlength' => 30,
                                ]);
                            ?>
                        </fieldset>
                        <fieldset class="detail-box box1">
                            <?php
                                echo $this->Form->control('name_english', [
                                    'label' => ['class' => 'label-row', 'text' => 'タイトル名（英語）'],
                                    'class' => 'input-row',
                                    'maxlength' => 30,
                                ]);
                            ?>
                        </fieldset>
                        <fieldset class="detail-box box1">
                            <div class="input">
                                <div class="label-row">分類</div>
                                <div class="input-row">
                                    <?=  h($title->country->name . '棋戦') ?>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="detail-box box1">
                            <div class="input">
                                <?php
                                    echo $this->Form->label('is_team', '団体戦', ['class' => 'label-row']);
                                    echo $this->Form->control('is_team', [
                                        'label' => false,
                                        'class' => 'input-row',
                                    ]);
                                ?>
                            </div>
                        </fieldset>
                    </li>
                    <li class="detail-row">
                        <fieldset class="detail-box box1">
                            <?php
                                echo $this->Form->control('holding', [
                                    'label' => ['class' => 'label-row', 'text' => '期'],
                                    'class' => 'input-row input-short',
                                    'maxlength' => 3,
                                ]);
                            ?>
                        </fieldset>
                        <fieldset class="detail-box box1">
                            <div class="input">
                                <div class="label-row">現在の保持者</div>
                                <div class="input-row"><?= h($title->getWinnerName(true)) ?></div>
                            </div>
                        </fieldset>
                        <fieldset class="detail-box box1">
                            <?php
                                echo $this->Form->control('html_file_name', [
                                    'label' => ['class' => 'label-row', 'text' => 'HTMLファイル名'],
                                    'class' => 'input-row',
                                    'maxlength' => 10,
                                ]);
                            ?>
                        </fieldset>
                        <fieldset class="detail-box box1">
                            <?php
                                echo $this->Form->control('html_file_modified', [
                                    'label' => ['class' => 'label-row', 'text' => '修正日'],
                                    'type' => 'text',
                                    'class' => 'input-row datepicker',
                                ]);
                            ?>
                        </fieldset>
                    </li>
                    <li class="detail-row">
                        <fieldset class="detail-box box1">
                            <div class="input">
                                <div class="label-row">最終更新日時</div>
                                <div class="input-row">
                                    <?= h($this->Date->formatToDateTime($title->modified)) ?>
                                    <?= $this->Form->hidden('modified') ?>
                                </div>
                            </div>
                        </fieldset>
                    </li>
                    <li class="detail-row">
                        <fieldset class="detail-box box1">
                            <?= $this->Form->control('remarks', [
                                'label' => ['class' => 'label-row', 'text' => 'その他備考'],
                                'type' => 'textarea',
                                'class' => 'input-row',
                            ]) ?>
                        </fieldset>
                    </li>
                    <li class="button-row">
                        <?= $this->Form->button('保存', ['class' => 'button button-primary']) ?>
                    </li>
                </ul>
            <?=$this->Form->end()?>
        </section>

        <!-- タイトル取得履歴 -->
        <section data-contentname="histories" class="tab-contents">
            <?=$this->Form->create(null, [
                'class' => 'add-condition-form',
                'type' => 'post',
                'url' => ['_name' => 'save_histories', $title->id],
            ])?>
                <?=$this->Form->hidden('name', ['value' => $title->name])?>
                <div class="category-row">保持情報</div>
                <add-history domain="<?= $this->Url->build('/') ?>" :history-id="historyId"
                    is-team="<?= $title->is_team ?>" @cleared="clearHistory()"></add-history>
                <ul class="boxes">
                    <?php if (!empty(($title->retention_histories))) : ?>
                        <?php if (($retention = $title->now_retention)) : ?>
                        <li class="label-row">現在の保持情報</li>
                        <li class="detail-row detail-row-result">
                            <fieldset class="detail-box box1">
                                <span class="inner-column"><?= h($retention->target_year).'年' ?></span>
                                <span class="inner-column"><?= h($retention->holding).'期' ?></span>
                                <span class="inner-column"><label>タイトル名：</label><?= h($retention->name) ?></span>
                                <span class="inner-column"><?= h($retention->team_label) ?></span>
                                <span class="inner-column"><label>優勝者：</label><?= h($retention->winner_name) ?></span>
                            </fieldset>
                            <fieldset class="detail-box detail-box-right">
                                <button type="button" class="button button-secondary" value="edit" @click="select('<?= $retention->id ?>')">編集</button>
                            </fieldset>
                        </li>
                        <?php endif ?>
                        <li class="label-row">保持情報（履歴）</li>
                        <?php foreach ($title->histories as $history) : ?>
                        <li class="detail-row detail-row-result">
                            <fieldset class="detail-box box1">
                                <span class="inner-column"><?= h($history->target_year).'年' ?></span>
                                <span class="inner-column"><?= h($history->holding).'期' ?></span>
                                <span class="inner-column"><label>タイトル名：</label><?= h($history->name) ?></span>
                                <span class="inner-column"><?= h($history->team_label) ?></span>
                                <span class="inner-column"><label>優勝者：</label><?= h($history->winner_name) ?></span>
                            </fieldset>
                            <fieldset class="detail-box detail-box-right">
                                <button type="button" class="button button-secondary" value="edit" @click="select('<?= $history->id ?>')">編集</button>
                            </fieldset>
                        </li>
                        <?php endforeach ?>
                    <?php endif ?>
                </ul>
            </section>
        <?=$this->Form->end()?>
    </div>
</div>
