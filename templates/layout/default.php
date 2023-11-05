<?php
declare(strict_types=1);

/**
 * アプリケーションの共通テンプレート
 *
 * @var \Gotea\View\AppView $this
 */

use Cake\I18n\FrozenTime;

?>
<!DOCTYPE html>
<html>
    <head>
        <?= $this->Html->charset() ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>
            <?= 'Gotea' . h($this->hasTitle() ? " - {$pageTitle}" : '') ?>
        </title>
        <?= $this->Html->meta('icon') ?>
        <?= $this->Html->css('main') ?>
        <?= $this->Html->css('app') ?>
        <?= $this->fetch('css') ?>
    </head>

    <body>
        <div class="container">
            <!-- blockUI -->
            <app-block :hide="hide"></app-block>
            <?php if (!$this->isDialogMode()) : ?>
            <!-- ヘッダー -->
            <header class="header">
                <!-- 見出し -->
                <h1 class="page-title"><?= h($this->hasTitle() ? $pageTitle : 'Gotea') ?></h1>
                <div class="other">
                    <?php if ($this->Identity->isLoggedIn()) : ?>
                    <span class="username">ユーザ：<?= h($this->Identity->get('name')) ?></span>
                    <?php endif ?>
                    <span><?= FrozenTime::now()->format('Y年m月d日 H時i分s秒') ?></span>
                </div>
            </header>

            <!-- メインコンテンツ -->
            <div class="main-content">
                <?php if ($this->Identity->isLoggedIn()) : ?>
                    <?= $this->element('sidebar') ?>
                <?php endif ?>
                <main class="main">
                    <?= $this->fetch('content') ?>
                </main>
            </div>

            <!-- フッター -->
            <footer class="footer">
                <?php if ($this->Identity->isLoggedIn()) : ?>
                <div>
                    <?= $this->Html->link('ログアウト', ['_name' => 'logout']); ?>
                </div>
                <?php endif ?>
            </footer>
            <?php else : ?>
            <!-- メインコンテンツ（モーダル表示） -->
            <div class="main-content-modal">
                <main class="main">
                    <?= $this->fetch('content') ?>
                </main>
            </div>
            <?php endif ?>

            <!-- モーダルコンテンツ -->
            <app-modal></app-modal>
            <!-- ダイアログ -->
            <app-dialog></app-dialog>
            <?= $this->Flash->render() ?>
        </div>

        <?php if ($this->Identity->isLoggedIn()) : ?>
        <script>
            window.Cake = {
                accessUser: '<?= $this->Identity->get('account') ?>',
            };
        </script>
        <?php endif ?>
        <?= $this->Html->script('main', ['type' => 'module']) ?>
        <?= $this->fetch('script') ?>
    </body>
</html>
