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
            <?='Gotea'.h(!empty($title) ? " - ${title}" : '') ?>
        </title>
        <?= $this->Html->meta('icon') ?>

        <?= $this->MyHtml->commonCss('css/common.css') ?>
        <?= $this->Html->css('app') ?>
        <?= $this->fetch('css') ?>
    </head>
    <body>
        <div class="block-ui"></div>
        <div class="container">
            <!-- ヘッダー -->
            <?php if (!isset($isDialog)) : ?>
            <header class="header">
                <div class="system-name"><?= !empty($title) ? 'Gotea' : '' ?></div>
                <!-- 見出し -->
                <h1 class="page-title"><?=h($title ?? 'Gotea')?></h1>
                <div class="other">
                    <?php if (isset($username)) : ?>
                        <span class="username">ユーザ：<?=h($username)?></span>
                    <?php endif ?>
                    <span><?=date('Y年m月d日 H時i分s秒')?></span>
                </div>
            </header>
            <?php endif ?>

            <!-- 本体 -->
            <div class="main-content<?=(isset($isDialog) ? ' modal' : '')?>">
                <?php if (isset($username) && !isset($isDialog)) : ?>
                    <?= $this->element('sidebar') ?>
                <?php endif ?>
                <!-- メインコンテンツ -->
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
            <?php if (!isset($isDialog)) : ?>
            <footer>
                <?php if (isset($username)) : ?>
                <div>
                    <?= $this->Html->link('ログアウト', ['_name' => 'logout']); ?>
                </div>
                <?php endif ?>
            </footer>
            <?php endif ?>
        </div>

        <script>
            window.Cake = {
                csrfToken: '<?= $this->request->getParam('_csrfToken') ?>',
                accessUser: '<?= $userid ?? '' ?>',
            };
        </script>
        <?= $this->MyHtml->commonScript('js/common.js') ?>
        <?= $this->Html->script(['app', 'common']) ?>
        <?= $this->fetch('script') ?>
    </body>
</html>
