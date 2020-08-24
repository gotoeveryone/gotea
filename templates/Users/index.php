<?= $this->Form->create($form, [
    'method' => 'post',
    'url' => ['_name' => 'login'],
]) ?>
<div class="login-row">
    <?=
        $this->Form->control('account', [
            'label' => [
                'class' => 'login-label',
                'text' => 'ID',
            ],
            'class' => 'login-account',
        ]);
    ?>
</div>
<div class="login-row">
    <?=
        $this->Form->control('password', [
            'label' => [
                'class' => 'login-label',
                'text' => 'Password',
            ],
            'class' => 'login-password',
        ]);
    ?>
</div>
<div class="row button-row">
    <?= $this->Form->hidden('redirect', ['value' => $this->getRequest()->getQuery('redirect')]) ?>
    <?= $this->Form->button('ログイン', ['type' => 'submit', 'class' => 'button button-primary']) ?>
    <?= $this->Form->button('クリア', ['type' => 'reset']) ?>
</div>
<?= $this->Form->end() ?>
