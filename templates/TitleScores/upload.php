<section class="update-score">
    <?= $this->Form->create(null, ['class' => 'main-form', 'url' => ['_name' => 'execute_upload_scores'], 'type' => 'file']) ?>
        <?= $this->Form->control('file', ['type' => 'file', 'required' => true]) ?>
        <div class="button-row">
            <?=$this->Form->button(__('Upload'), ['class' => 'button button-primary'])?>
        </div>
    <?= $this->Form->end() ?>
</section>
