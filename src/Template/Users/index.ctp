<?= $this->Form->create($form, [
    'method' => 'post',
    'url' => ['action' => 'login'.(
        $this->request->getQuery('redirect') ? '?redirect='.$this->request->getQuery('redirect') : ''
    )],
    'novalidate' => 'novalidate',
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
                $this->Form->control('username', [
                    'maxlength' => 10,
                ]);
            ?>
        </li>
        <li class="row">
            <label>Password</label>
            <?=
                $this->Form->control('password', [
                    'maxlength' => 20,
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
