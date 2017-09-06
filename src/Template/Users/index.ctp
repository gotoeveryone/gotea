<?= $this->Form->create(null, [
    'method' => 'post',
    'url' => ['action' => 'login'.(
        $this->request->getQuery('redirect') ? '?redirect='.$this->request->getQuery('redirect') : ''
    )],
    'templates' => [
        'inputContainer' => '{{content}}',
        'textFormGroup' => '{{input}}',
        'passwordFormGroup' => '{{input}}'
    ]
]) ?>
    <ul class="login">
        <li class="row">
            <label>ID</label>
            <?=
                $this->Form->text('username', [
                    'value' => $this->request->getData('username'),
                    'maxlength' => 10,
                    'class' => 'imeDisabled'
                ]);
            ?>
        </li>
        <li class="row">
            <label>Password</label>
            <?=
                $this->Form->password('password', [
                    'value' => $this->request->getData('password'),
                    'maxlength' => 20,
                    'class' => 'imeDisabled'
                ]);
            ?>
        </li>
        <li class="row button-row">
            <?=$this->Form->button('ログイン', ['type' => 'submit'])?>
            <?=$this->Form->button('クリア', ['type' => 'reset'])?>
            <?=$this->Form->button('戻る', ['type' => 'button', 'class' => 'back'])?>
        </li>
    </ul>
<?= $this->Form->end() ?>
