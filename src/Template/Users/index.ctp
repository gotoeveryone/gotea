<section class="center">
	<?= $this->Form->create(null, [
        'method' => 'post',
        'url' => ['action' => 'login'],
        'templates' => [
            'inputContainer' => '{{content}}',
            'textFormGroup' => '{{input}}',
            'passwordFormGroup' => '{{input}}'
        ]
    ]) ?>
        <section class="login">
            <section class="loginRow">
                <section class="loginColumnLeft">
                    <span class="label">ID</span>
                </section>
                <section class="loginColumnRight">
                    <?=
						$this->Form->input('username',
							[
								'value' => $this->request->data('username'),
	                            'maxlength' => 20,
	                            'class' => 'imeDisabled'
							]
                        );
                    ?>
                </section>
            </section>
            <section class="loginRow">
                <section class="loginColumnLeft">
                    <span class="label">Password</span>
                </section>
                <section class="loginColumnRight">
                    <?=
                        $this->Form->input('password',
							[
								'value' => $this->request->data('password'),
	                            'maxlength' => 20,
	                            'class' => 'imeDisabled'
							]
                        );
                    ?>
                </section>
            </section>
            <section class="loginRow">
                <?=$this->Form->button('ログイン', ['type' => 'submit'])?>
                <?=$this->Form->button('クリア', ['type' => 'reset'])?>
                <?=$this->Form->button('戻る', ['type' => 'button', 'id' => 'back'])?>
            </section>
        </section>
	<?= $this->Form->end() ?>
</section>
<script type="text/javascript">
	$(document).ready(function() {
		// 戻るボタン押下時
		$('#back').click(function() {
			location.href = '/';
		});
	});
</script>
