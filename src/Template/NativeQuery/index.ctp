<section class="update-score">
    <?=$this->Form->create(null, [
        'id' => 'mainForm',
        'class' => 'main-form',
        'method' => 'post',
        'url' => ['action' => 'index'],
        'templates' => [
            'inputContainer' => '{{content}}',
            'textFormGroup' => '{{input}}',
            'selectFormGroup' => '{{input}}'
        ]
    ])?>
        <?=$this->Form->textarea('queries', ['id' => 'input-queries', 'class' => 'queries'])?>
        <div class="button-row">
            <?=$this->Form->button('更新', ['data-button-type' => 'execute-queries'])?>
            <?=$this->Form->button('クリア', ['type' => 'button', 'data-button-type' => 'clear-queries'])?>
        </div>
    <?=$this->Form->end()?>
</section>
