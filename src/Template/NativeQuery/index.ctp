<section class="update-score">
    <?= $this->Form->create(null, ['class' => 'main-form', 'url' => ['_name' => 'execute_queries']]) ?>
        <?= $this->Form->textarea('queries', ['id' => 'input-queries', 'class' => 'queries']) ?>
        <div class="button-row">
            <?=$this->Form->button('更新', ['data-button-type' => 'execute-queries', 'class' => 'button button-primary'])?>
            <?=$this->Form->button('クリア', ['type' => 'button', 'data-button-type' => 'clear-queries'])?>
        </div>
    <?= $this->Form->end() ?>
</section>
