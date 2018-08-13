<?php
/**
 * アプリケーションの共通テンプレート
 * @property \Gotea\View\AppView $this
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <?= $this->Html->charset() ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>
            <?='Gotea'.h($this->hasTitle() ? " - ${title}" : '') ?>
        </title>
        <?= $this->Html->meta('icon') ?>
        <?= $this->Html->css('app') ?>
        <?= $this->fetch('css') ?>
    </head>
    <body>
        <div class="container">
            <!-- blockUI -->
            <blocked :hide="hide"></blocked>
            <?php if (!$this->isDialogMode()) : ?>
            <!-- ヘッダー -->
            <header class="header">
                <!-- 見出し -->
                <h1 class="page-title"><?= h($this->hasTitle() ? $title : 'Gotea') ?></h1>
                <div class="other">
                    <?php if ($this->isAuth()) : ?>
                        <span class="username">ユーザ：<?= h($this->getUser('name')) ?></span>
                    <?php endif ?>
                    <span><?=date('Y年m月d日 H時i分s秒')?></span>
                </div>
            </header>

            <!-- メインコンテンツ -->
            <div class="main-content">
                <?php if ($this->isAuth()) : ?>
                    <?= $this->element('sidebar') ?>
                <?php endif ?>
                <main class="main">
                    <?= $this->fetch('content') ?>
                </main>
            </div>

            <!-- フッター -->
            <footer class="footer">
                <?php if ($this->isAuth()) : ?>
                <div>
                    <?= $this->Html->link('ログアウト', ['_name' => 'logout']); ?>
                </div>
                <?php endif ?>
            </footer>
            <?php else : ?>

            <!-- メインコンテンツ（モーダル表示） -->
            <div class="main-content-modal">
                <main class="main">
                    <?=$this->fetch('content')?>
                </main>
            </div>
            <?php endif ?>

            <!-- モーダルコンテンツ -->
            <modal :options="modal"></modal>
            <!-- ダイアログ -->
            <app-dialog :options="dialog"></app-dialog>
            <?= $this->Flash->render() ?>
        </div>

        <?php if ($this->isAuth()) : ?>
        <script>
            window.Cake = {
                csrfToken: '<?= $this->request->getParam('_csrfToken') ?>',
                accessUser: '<?= $this->getUser('account') ?>',
            };
        </script>
        <?php endif ?>
        <?= $this->Html->script('app') ?>
        <?= $this->fetch('script') ?>
    </body>
</html>
