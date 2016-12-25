<article class="login">
	<?= $this->Form->create(null, [
        'method' => 'post',
        'url' => ['action' => 'login'],
        'templates' => [
            'inputContainer' => '{{content}}',
            'textFormGroup' => '{{input}}',
            'passwordFormGroup' => '{{input}}'
        ]
    ]) ?>
        <section class="row">
            <section class="label"><span>ID</span></section>
            <section>
                <?=
                    $this->Form->input('username', [
                        'value' => $this->request->data('username'),
                        'maxlength' => 20,
                        'class' => 'imeDisabled'
                    ]);
                ?>
            </section>
        </section>
        <section class="row">
            <section class="label"><span>Password</span></section>
            <section>
                <?=
                    $this->Form->input('password', [
                        'value' => $this->request->data('password'),
                        'maxlength' => 20,
                        'class' => 'imeDisabled'
                    ]);
                ?>
            </section>
        </section>
        <section class="row button-row">
            <?=$this->Form->button('ログイン', ['type' => 'submit'])?>
            <?=$this->Form->button('クリア', ['type' => 'reset'])?>
            <?=$this->Form->button('戻る', ['type' => 'button', 'id' => 'back'])?>
        </section>
	<?= $this->Form->end() ?>
</article>

<?php $this->Html->scriptStart(['inline' => false, 'block' => 'script']); ?>
	$(function() {
		// 戻るボタン押下時
		$('#back').click(function() {
			location.href = '/';
		});
	});
<?php $this->Html->scriptEnd(); ?>
