<div class="detail-dialog">
    <!-- タブ -->
    <ul class="tabs" data-selecttab="<?= $this->request->getQuery('tab') ?>">
        <li class="tab" data-tabname="title">タイトル情報</li>
        <li class="tab" data-tabname="histories">保持履歴</li>
    </ul>

    <!-- 詳細 -->
    <div class="detail">
        <!-- マスタ -->
        <section data-contentname="title" class="tab-contents">
            <?=$this->Form->create($title, [
                'id' => 'mainForm',
                'class' => 'main-form',
                'type' => 'post',
                'url' => ['action' => 'save'],
                'novalidate' => 'novalidate',
                'templates' => [
                    'inputContainer' => '{{content}}',
                    'textFormGroup' => '{{input}}',
                    'selectFormGroup' => '{{input}}'
                ]
            ])?>
                <?=$this->Form->hidden('id', ['value' => $title->id])?>
                <?=$this->Form->hidden('optimistic_key', ['value' => $this->Date->format($title->modified, 'yyyyMMddHHmmss')])?>
                <div class="category-row"><?='タイトル情報（ID：'.h($title->id).'）'?></div>
                <ul class="boxes">
                    <li class="row">
                        <div class="box">
                            <div class="label-row">タイトル名</div>
                            <div class="input-row"><?=$this->Form->text('name', ['maxlength' => 20])?></div>
                        </div>
                        <div class="box">
                            <div class="label-row">タイトル名（英語）</div>
                            <div class="input-row"><?=$this->Form->text('name_english', ['maxlength' => 20])?></div>
                        </div>
                        <div class="box">
                            <div class="label-row">分類</div>
                            <div class="input-row"><?=($title->country->name).'棋戦'?></div>
                        </div>
                    </li>
                    <li class="row">
                        <div class="box">
                            <div class="label-row">期</div>
                            <div class="input-row"><?=$this->Form->text('holding', ['maxlength' => 3, 'class' => 'holding'])?></div>
                        </div>
                        <div class="box">
                            <div class="label-row">現在の保持者</div>
                            <div class="input-row"><?= h($title->getWinnerName(true)) ?></div>
                        </div>
                        <div class="box">
                            <div class="label-row">団体戦</div>
                            <div class="input-row">
                                <?= $this->Form->checkbox('is_team', ['id' => 'team']) ?>
                                <?= $this->Form->label('team', '団体戦', ['class' => 'checkbox-label']) ?>
                            </div>
                        </div>
                    </li>
                    <li class="row">
                        <div class="box">
                            <div class="label-row">HTMLファイル名</div>
                            <div class="input-row">
                                <?=$this->Form->text('html_file_name', ['maxlength' => 10])?>
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row">修正日</div>
                            <div class="input-row">
                                <?=$this->Form->text('html_file_modified', ['type' => 'date', 'class' => 'datepicker'])?>
                            </div>
                        </div>
                        <div class="box">
                            <div class="label-row">更新日時</div>
                            <div class="input-row"><?=$this->Date->formatToDateTime($title->modified)?></div>
                        </div>
                    </li>
                    <li class="row">
                        <div class="box">
                            <div class="label-row">その他備考</div>
                            <div class="input-row">
                                <?=$this->Form->textarea('remarks', ['class' => 'remarks'])?>
                            </div>
                        </div>
                    </li>
                    <li class="button-row">
                        <?= $this->Form->button('保存') ?>
                    </li>
                </ul>
            <?=$this->Form->end()?>
        </section>

        <!-- タイトル保持履歴 -->
        <section data-contentname="histories" class="tab-contents">
            <?=$this->Form->create(null, [
                'id' => 'addHistoryForm',
                'type' => 'post',
                'url' => ['controller' => 'retention-histories', 'action' => 'add'],
                'templates' => [
                    'inputContainer' => '{{content}}',
                    'textFormGroup' => '{{input}}',
                    'selectFormGroup' => '{{input}}'
                ]
            ])?>
                <?=$this->Form->hidden('title_id', ['value' => $title->id])?>
                <?=$this->Form->hidden('name', ['value' => $title->name])?>
                <?=$this->Form->hidden('is_team', ['value' => $title->is_team])?>
                <div class="category-row">保持情報</div>
                <ul class="boxes">
                    <add-history domain="<?= $this->Url->build('/') ?>" is-team="<?= $title->is_team ?>"></add-history>
                    <?php if (!empty(($title->retention_histories))) : ?>
                        <?php if (($retention = $title->now_retention)) : ?>
                            <li class="row">
                                <div class="box">
                                    <div class="label-row">現在の保持情報</div>
                                </div>
                            </li>
                            <li class="row">
                                <div class="box">
                                    <div class="input-row">
                                        <?=h(__("{$retention->target_year}年 {$retention->holding}期 {$retention->name} "))?>
                                        <?=h($retention->getWinnerName($title->is_team))?>
                                    </div>
                                </div>
                            </li>
                        <?php endif ?>
                        <li class="row">
                            <div class="box">
                                <div class="label-row">保持情報（履歴）</div>
                            </div>
                        </li>
                        <?php foreach ($title->histories as $history) : ?>
                            <li class="row">
                                <div class="box">
                                    <div class="input-row">
                                        <?=h("{$history->target_year}年 {$history->holding}期 {$history->name} ")?>
                                        <?=h($history->getWinnerName($title->is_team))?>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach ?>
                    <?php endif ?>
                </ul>
            </section>
        <?=$this->Form->end()?>
    </div>
</div>
