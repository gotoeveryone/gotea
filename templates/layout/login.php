<?php
declare(strict_types=1);

/**
 * アプリケーションの共通テンプレート
 *
 * @property \Gotea\View\AppView $this
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <?= $this->Html->charset() ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= __('Gotea') ?></title>
        <?= $this->Html->meta('icon') ?>
        <?= $this->Html->css('main') ?>
        <?= $this->Html->css('app') ?>
        <?= $this->fetch('css') ?>
    </head>
    <body>
        <div class="container login-container">
            <section class="login-content">
                <h1 class="login-title"><?= __('Gotea') ?></h1>
                <main class="login-main">
                    <?= $this->fetch('content') ?>
                </main>
            </section>
            <!-- ダイアログ -->
            <?= $this->Flash->render() ?>
        </div>
        <?= $this->Html->script('main', ['type' => 'module']) ?>
        <?= $this->fetch('script') ?>
    </body>
</html>
