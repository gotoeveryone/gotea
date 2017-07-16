<section class="title-scores">
    <?=$this->Form->create($form, [
        'id' => 'mainForm',
        'type' => 'post',
        'url' => ['action' => 'index'],
        'templates' => [
            'inputContainer' => '{{content}}',
            'textFormGroup' => '{{input}}',
            'selectFormGroup' => '{{input}}'
        ]
    ])?>
        <?php if (!isset($isDialog)) : ?>
        <ul class="search-header">
            <li class="search-row">
                <label>棋士名：</label>
                <?=$this->Form->text('name', ['class' => 'name', 'maxlength' => 20]);?>
                <label>対象棋戦：</label>
                <?= $this->cell('Countries')->render() ?>
                <label>対局年：</label>
                <?=
                    $this->Form->select('target_year', $years, [
                        'class' => 'year', 'empty' => true,
                    ]);
                ?>
                <label>対局日：</label>
                <?=$this->Form->text('started', ['class' => 'date datepicker'])?>
                ～
                <?=$this->Form->text('ended', ['class' => 'date datepicker'])?>
                <div class="button-wrap">
                    <?=$this->Form->button('検索', ['type' => 'submit'])?>
                </div>
            </li>
        </ul>
        <?php endif ?>

        <div class="search-results<?=(isset($isDialog) ? ' modal' : '')?>">
            <?=$this->Form->hidden('change_id', ['value' => ''])?>
            <?=$this->Form->hidden('delete_id', ['value' => ''])?>
            <ul class="table-header">
                <li class="table-row">
                    <span class="country">対象国</span>
                    <span class="date">日付</span>
                    <span class="name">勝者</span>
                    <span class="name">敗者</span>
                    <?=!isset($isDialog) ? '<span class="operation">操作</span>' : ''?>
                </li>
            </ul>
            <?php if (isset($titleScores)) : ?>
            <ul class="table-body">
                <?php foreach ($titleScores as $titleScore): ?>
                <li class="table-row">
                    <?=$this->Form->hidden('id', ['value' => $titleScore->id]);?>
                    <span class="country"><?= h($titleScore->country->name.'棋戦') ?></span>
                    <span class="date"><?= h($titleScore->date) ?></span>
                    <span class="name"><?= $titleScore->getWinner($this->request->getData('player_id')) ?></span>
                    <span class="name"><?= $titleScore->getLoser($this->request->getData('player_id')) ?></span>
                    <?php if (!isset($isDialog)) : ?>
                    <span class="operation">
                        <?= $this->Form->button('勝敗変更', ['data-id' => $titleScore->id, 'class' => 'change']) ?>
                        <?= $this->Form->button('削除', ['type' => 'button', 'data-id' => $titleScore->id, 'class' => 'delete']) ?>
                    </span>
                    <?php endif ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif ?>
        </div>
        <?php if (isset($isDialog)) : ?>
        <div class="button-row">
            <?=$this->Form->button('戻る', ['type' => 'button', 'class' => 'back'])?>
        </div>
        <?php endif ?>
    <?=$this->Form->end()?>
</section>

<?php $this->MyHtml->scriptStart(['inline' => false, 'block' => 'script']); ?>
<script>
    $(function() {
        // 勝敗変更ボタン押下時
        $('.change').on('click', function() {
            var id = $(this).attr('data-id');
            $('[name=change_id]').val(id);
            $('#mainForm').attr('action', '<?=$this->Url->build(['action' => 'change'])?>').submit();
        });
        // 勝敗変更ボタン押下時
        $('.delete').on('click', function() {
            var id = $(this).attr('data-id');
            $('[name=delete_id]').val(id);
            $('#mainForm').attr('action', '<?=$this->Url->build(['action' => 'delete'])?>');
            openConfirm('タイトル成績情報を削除します。よろしいですか？');
        });
        <?php if (isset($isDialog)) : ?>
        $('.back').on('click', function() {
            $.blockUI();
            location.href = '<?=$this->Url->build(['controller' => 'players', 'action' => 'detail', $this->request->data('player_id')])?>';
        })
        <?php endif ?>
    });
</script>
<?php $this->MyHtml->scriptEnd(); ?>
