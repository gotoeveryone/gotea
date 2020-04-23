<?= $this->Form->create(null, [
    'class' => 'main-form',
    'enctype' => 'multipart/form-data',
    'url' => ['_name' => 'add_csv'],
    'templates' => [
        'inputContainer' => '{{content}}',
        'textFormGroup' => '{{input}}',
        'selectFormGroup' => '{{input}}'
    ]
]) ?>
    <?= $this->Form->file('csv') ?>
    <?= $this->Form->button('登録') ?>
<?= $this->Form->end(); ?>