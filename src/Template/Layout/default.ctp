<?php
/**
 * アプリケーションの共通テンプレート
 */
?>
<!DOCTYPE html>
<html>
<head>
    <?=$this->Html->charset()?>
    <title>
        <?='棋士情報管理システム - '.$cakeDescription ?>
    </title>
    <?=$this->Html->meta('icon')?>
    <link rel="stylesheet" href="/css/jquery-ui.min.css" />
    <link rel="stylesheet" href="/css/colorbox.css" />
    <link rel="stylesheet" href="/css/common.css" />
    <?=$this->Html->css('app')?>
</head>
<body>
    <div class="block-ui"></div>
    <div class="container">
        <!-- ヘッダー -->
        <?php if (!isset($isDialog)) : ?>
        <header>
            <div class="system-name"><span>棋士情報管理システム</span></div>
            <!-- 見出し -->
            <div class="page-title">
                <h1><?=h($cakeDescription)?></h1>
            </div>
            <div class="other">
                <?php if (isset($username)) : ?>
                    <span class="username">ユーザ：<?=h($username)?></span>
                <?php endif ?>
                <span><?=date('Y年m月d日 H時i分s秒')?></span>
            </div>
        </header>
        <?php endif ?>

        <!-- 本体 -->
        <div class="content<?=(isset($isDialog) ? ' modal' : '')?>">
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
                        'action' => 'categorize'
                    ], ['class' => 'button']);
                ?>
                <?=
                    $this->Html->link('成績更新日編集', [
                        'controller' => 'updatedPoints',
                        'action' => 'index'
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
            <main>
                <?=$this->fetch('content')?>
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

    <!-- ダイアログ -->
    <div id="dialog" title="メッセージ"><?=$this->Flash->render()?></div>
    <div id="confirm" title="確認"></div>

    <script src="/js/jquery-2.2.3.min.js"></script>
    <script src="/js/jquery-ui.min.js"></script>
    <script src="/js/jquery.blockUI.js"></script>
    <script src="/js/jquery.colorbox-min.js"></script>
    <script src="/js/common.js"></script>
    <?=$this->Html->script('app')?>
    <script>
        // containerを非表示
        document.getElementsByClassName("content")[0].style.display = "none";
    </script>
    <?=$this->fetch('script')?>
</body>
</html>
