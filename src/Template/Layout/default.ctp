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
    <script type="text/javascript" src="/js/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="/js/jquery.blockUI.js"></script>
    <script type="text/javascript" src="/js/jquery.colorbox-min.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
    <?=$this->Html->script('app')?>
    <link rel="stylesheet" type="text/css" href="/css/jquery-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="/css/colorbox.css" />
    <link rel="stylesheet" type="text/css" href="/css/common.css" />
    <?=$this->Html->css('app')?>
</head>
<body>
    <section id="container">

        <!-- ヘッダー -->
        <?php if (!isset($dialogFlag) || !$dialogFlag) : ?>
        <section id="header">
            <section class="system-name">
                棋士情報管理システム
            </section>
            <!-- 見出し -->
            <section class="page-title">
            <?php if (!empty($cakeDescription)) : ?>
                <h1><?=h($cakeDescription)?></h1>
            <?php endif ?>
            </section>
            <section class="username">
                <?php if (isset($username)) : ?>
                    <span>ユーザ：<?=h($username)?></span><br>
                <?php endif ?>
                <?=date('Y年m月d日 H時i分s秒')?>
            </section>
        </section>
        <hr />
        <?php endif ?>

        <!-- 本体 -->
        <?php if (!empty($cakeDescription)) : ?>
            <section id="content">
        <?php else : ?>
            <section id="contentModal">
        <?php endif ?>
            <?php if (isset($username) && (!isset($dialogFlag) || !$dialogFlag)) : ?>
            <section class="sideMenu">
                <?=
                    $this->Html->link('棋士勝敗ランキング出力',
                        [
                            'controller' => 'ranking',
                            'action' => 'index'
                        ],
                        [
                            'class' => 'button menu'
                        ]
                    );
                ?>
                <?=
                    $this->Html->link('棋士情報検索',
                        [
                            'controller' => 'players',
                            'action' => 'index'
                        ],
                        [
                            'class' => 'button menu'
                        ]
                    );
                ?>
                <?=
                    $this->Html->link('タイトル情報検索',
                        [
                            'controller' => 'titles',
                            'action' => 'index'
                        ],
                        [
                            'class' => 'button menu'
                        ]
                    );
                ?>
                <?=
                    $this->Html->link('段位別人数検索',
                        [
                            'controller' => 'players',
                            'action' => 'categorize'
                        ],
                        [
                            'class' => 'button menu'
                        ]
                    );
                ?>
                <?=
                    $this->Html->link('成績更新日編集',
                        [
                            'controller' => 'updatedPoints',
                            'action' => 'index'
                        ],
                        [
                            'class' => 'button menu'
                        ]
                    );
                ?>
                <?php if ($admin) : ?>
                <?=
                    $this->Html->link('各種情報クエリ更新',
                        [
                            'controller' => 'nativeQuery',
                            'action' => 'index'
                        ],
                        [
                            'class' => 'button menu'
                        ]
                    );
                ?>
                <?php endif ?>
            </section>
            <section class="main">
            <?php else : ?>
            <section class="child">
            <?php endif ?>
                <?=$this->fetch('content')?>
            </section>
        </section>

        <!-- フッター -->
        <?php if (!empty($cakeDescription)) : ?>
            <section id="footer" class="center">
                <hr />
                <?php if (isset($username) && (!isset($dialogFlag) || !$dialogFlag)) { ?>
                <a href="<?=$this->Url->build(['controller' => 'users', 'action' => 'logout']); ?>">
                    ログアウト
                </a>
                <?php } ?>
            </section>
        <?php endif ?>
    </section>

    <section id="dialog" title="メッセージ">
        <?=$this->Flash->render()?>
    </section>
    <section id="confirm" title="確認"></section>
</body>
</html>
