<?php
/**
 * アプリケーションの共通テンプレート
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <?=$this->Html->charset()?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>
            <?='棋士情報管理システム - '.h($cakeDescription ?? '') ?>
        </title>
        <?=$this->Html->meta('icon')?>
        <link rel="stylesheet" href="<?=env('ASSETS_URL')?>css/common.css" />
        <?=$this->Html->css('app')?>
    </head>
    <body>
        <div class="block-ui"></div>
        <div class="container">
            <!-- ヘッダー -->
            <?php if (!isset($isDialog)) : ?>
            <header class="header">
                <div class="system-name">棋士情報管理システム</div>
                <!-- 見出し -->
                <h1 class="page-title"><?=h($cakeDescription ?? '')?></h1>
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
                <!-- ナビゲーション -->
                <nav>
                    <?=
                        $this->Html->link('棋士勝敗ランキング出力', [
                            'controller' => 'players',
                            'action' => 'ranking'
                        ], ['class' => 'button']);
                    ?>
                    <?=
                        $this->Html->link('タイトル勝敗検索', [
                            'controller' => 'titleScores',
                            'action' => 'index'
                        ], ['class' => 'button']);
                    ?>
                    <?=
                        $this->Html->link('棋士情報検索', [
                            'controller' => 'players',
                            'action' => 'index'
                        ], ['class' => 'button']);
                    ?>
                    <?=
                        $this->Html->link('タイトル情報検索', [
                            'controller' => 'titles',
                            'action' => 'index'
                        ], ['class' => 'button']);
                    ?>
                    <?=
                        $this->Html->link('段位別棋士数検索', [
                            'controller' => 'players',
                            'action' => 'view-ranks'
                        ], ['class' => 'button']);
                    ?>
                    <?php if ($admin) : ?>
                    <?=
                        $this->Html->link('各種情報クエリ更新', [
                            'controller' => 'nativeQuery',
                            'action' => 'index'
                        ], ['class' => 'button']);
                    ?>
                    <?php endif ?>
                </nav>
                <?php endif ?>
                <!-- メインコンテンツ -->
                <main class="main">
                    <?=$this->fetch('content')?>
                    <!-- モーダルコンテンツ -->
                    <modal></modal>
                    <!-- ダイアログ -->
                    <app-dialog></app-dialog>
                    <?= $this->Flash->render() ?>
                </main>
            </div>

            <!-- フッター -->
            <?php if (!isset($isDialog)) : ?>
            <footer>
                <?php if (isset($username)) : ?>
                <div>
                    <a href="<?=$this->Url->build(['controller' => 'users', 'action' => 'logout']); ?>">ログアウト</a>
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
        <script src="<?=env('ASSETS_URL')?>js/common.js"></script>
        <?=$this->Html->script('common')?>
        <?=$this->Html->script('app')?>
        <?=$this->fetch('script')?>
    </body>
</html>
