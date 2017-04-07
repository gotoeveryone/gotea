<section class="login">
	<?= $this->Form->create(null, [
        'method' => 'post',
        'url' => ['action' => 'login'],
        'templates' => [
            'inputContainer' => '{{content}}',
            'textFormGroup' => '{{input}}',
            'passwordFormGroup' => '{{input}}'
        ]
    ]) ?>
        <div class="row">
            <label>ID</label>
            <?=
                $this->Form->text('username', [
                    'value' => $this->request->getData('username'),
                    'maxlength' => 10,
                    'class' => 'imeDisabled'
                ]);
            ?>
        </div>
        <div class="row">
            <label>Password</label>
            <?=
                $this->Form->password('password', [
                    'value' => $this->request->getData('password'),
                    'maxlength' => 20,
                    'class' => 'imeDisabled'
                ]);
            ?>
        </div>
        <div class="row button-row">
            <?=$this->Form->button('ログイン', ['type' => 'submit'])?>
            <?=$this->Form->button('クリア', ['type' => 'reset'])?>
            <?=$this->Form->button('戻る', ['type' => 'button', 'id' => 'back'])?>
        </div>
	<?= $this->Form->end() ?>
</section>

<?php $this->MyHtml->scriptStart(['inline' => false, 'block' => 'script']); ?>
<script>
	$(function() {
		// 戻るボタン押下時
		$('#back').click(function() {
			location.href = '/';
		});
	});
</script>
<?php $this->MyHtml->scriptEnd(); ?>
