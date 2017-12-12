<?php
/**
 * アプリケーションの共通テンプレート
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

        <?= $this->MyHtml->commonCss('css/common.css') ?>
        <?= $this->Html->css('app') ?>
        <?= $this->fetch('css') ?>
    </head>
    <body>
        <div class="block-ui"></div>
        <div class="container">
            <?php if (!$this->isDialogMode()) : ?>
            <!-- ヘッダー -->
            <header class="header">
                <div class="system-name"><?= $this->hasTitle() ? '' : 'Gotea' ?></div>
                <!-- 見出し -->
                <h1 class="page-title"><?= h($this->hasTitle() ? $title : 'Gotea') ?></h1>
                <div class="other">
                    <?php if ($this->isAuth()) : ?>
                        <span class="username">ユーザ：<?= h($this->getUser('account')) ?></span>
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
                    <?=$this->fetch('content')?>
                    <!-- モーダルコンテンツ -->
                    <modal :options="modal"></modal>
                    <!-- ダイアログ -->
                    <app-dialog :options="dialog"></app-dialog>
                    <?= $this->Flash->render() ?>
                </main>
            </div>

            <!-- フッター -->
            <footer>
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
                    <!-- ダイアログ -->
                    <app-dialog :options="dialog"></app-dialog>
                    <?= $this->Flash->render() ?>
                </main>
            </div>
            <?php endif ?>
        </div>

        <?php if ($this->isAuth()) : ?>
        <script>
            window.Cake = {
                csrfToken: '<?= $this->request->getParam('_csrfToken') ?>',
                accessUser: '<?= $this->getUser('account') ?>',
            };
        </script>
        <?php endif ?>
        <?= $this->MyHtml->commonScript('js/common.js') ?>
        <?= $this->Html->script(['app', 'common']) ?>
        <?= $this->fetch('script') ?>
    </body>
</html>
