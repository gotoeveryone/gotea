<?= $this->Form->create($form, [
    'method' => 'post',
    'url' => ['_name' => 'login'],
]) ?>
    <ul class="login">
        <li class="row">
            <?=
                $this->Form->control('account', [
                    'label' => [
                        'class' => 'label',
                        'text' => 'ID',
                    ],
                    'maxlength' => 10,
                ]);
            ?>
        </li>
        <li class="row">
            <?=
                $this->Form->control('password', [
                    'label' => [
                        'class' => 'label',
                        'text' => 'Password',
                    ],
                    'maxlength' => 20,
                ]);
            ?>
        </li>
        <li class="row button-row">
            <?= $this->Form->hidden('redirect', ['value' => $this->request->getQuery('redirect')]) ?>
            <?=$this->Form->button('ログイン', ['type' => 'submit'])?>
            <?=$this->Form->button('クリア', ['type' => 'reset'])?>
        </li>
    </ul>
<?= $this->Form->end() ?>
