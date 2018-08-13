<?= $this->Form->create($form, [
    'method' => 'post',
    'url' => ['_name' => 'login'],
]) ?>
    <fieldset class="login-row">
        <?=
            $this->Form->control('account', [
                'label' => [
                    'class' => 'label',
                    'text' => 'ID',
                ],
                'class' => 'login-account',
            ]);
        ?>
    </fieldset>
    <fieldset class="login-row">
        <?=
            $this->Form->control('password', [
                'label' => [
                    'class' => 'label',
                    'text' => 'Password',
                ],
                'class' => 'login-password',
            ]);
        ?>
    </fieldset>
    <fieldset class="row button-row">
        <?= $this->Form->hidden('redirect', ['value' => $this->request->getQuery('redirect')]) ?>
        <?= $this->Form->button('ログイン', ['type' => 'submit', 'class' => 'button button-primary']) ?>
        <?= $this->Form->button('クリア', ['type' => 'reset']) ?>
    </fieldset>
<?= $this->Form->end() ?>
