<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeVersion = __d('cake_dev', 'CakePHP %s', Configure::version())
?>
<!DOCTYPE html>
<html>
<head>
    <?php echo $this->Html->charset(); ?>
    <title>
        <?php echo '棋士情報管理システム - '.$cakeDescription ?>:
        <?php //echo $title_for_layout; ?>
    </title>
    <?php echo $this->Html->meta('icon'); ?>
    <script type="text/javascript" src="/js/jquery.js"></script>
    <script type="text/javascript" src="/js/jquery-ui.custom.js"></script>
    <script type="text/javascript" src="/js/jquery.blockUI.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
    <?php echo $this->Html->script('common'); ?>
    <!--<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css" />-->
    <link rel="stylesheet" type="text/css" href="/css/common.css" />
    <?php echo $this->Html->css('app'); ?>
    <link rel="stylesheet" type="text/css" href="/css/jquery-ui.custom.css" />
</head>
<body>
    <section id="container">

        <!-- ヘッダー -->
        <?php if (isset($username) && (!isset($dialogFlag) || !$dialogFlag)) { ?>
        <section id="header">
            <section class="username">
                ユーザ：<?php echo $username; ?>
            </section>
            <section class="time">
                <span><?php echo date('Y年m月d日 H時i分s秒') ?></span><br/>
            </section>
        </section>
        <?php } ?>

        <!-- 見出し -->
        <section id="title" class="center">
            <h1><?php if (!empty($cakeDescription)) { echo $cakeDescription.'画面'; } ?></h1>
            <hr />
        </section>

        <!-- 本体 -->
        <section id="content">
            <?php if (isset($username) && (!isset($dialogFlag) || !$dialogFlag)) { ?>
            <section class="sideMenu">
                <?php
                    echo $this->Html->link('棋士勝敗ランキング出力',
                        array(
                            'controller' => 'Ranking',
                            'action' => 'index'
                        ),
                        array(
                            'class' => 'button menu'
                        )
                    );
                    echo $this->Html->link('棋士情報検索',
                        array(
                            'controller' => 'SearchPlayer',
                            'action' => 'index'
                        ),
                        array(
                            'class' => 'button menu'
                        )
                    );
                    echo $this->Html->link('タイトル情報検索・修正',
                        array(
                            'controller' => 'EditTitle',
                            'action' => 'index'
                        ),
                        array(
                            'class' => 'button menu'
                        )
                    );
                    echo $this->Html->link('成績更新日編集',
                        array(
                            'controller' => 'EditScoreUpdate',
                            'action' => 'index'
                        ),
                        array(
                            'class' => 'button menu'
                        )
                    );
                    echo $this->Html->link('各種情報クエリ更新',
                        array(
                            'controller' => 'ExecuteQuery',
                            'action' => 'index'
                        ),
                        array(
                            'class' => 'button menu'
                        )
                    );
                ?>
            </section>
            <section class="main">
            <?php } else { ?>
            <section class="child">
            <?php } ?>
                <?php echo $this->fetch('content'); ?>
            </section>
        </section>

        <!-- フッター -->
        <section id="footer" class="center">
            <hr />
            <?php if (isset($username) && (!isset($dialogFlag) || !$dialogFlag)) { ?>
            <a href="<?php echo $this->Html->url(array(
                'controller' => 'login',
                'action' => 'logout'
            )); ?>">ログアウト</a>
            <?php } ?>
            <?php // echo $this->element('sql_dump'); ?>
        </section>
    </section>

    <section id="dialog" title="メッセージ">
        <?php echo $this->Session->flash(); ?>
    </section>
    <section id="confirm" title="確認"></section>

    <script type="text/javascript">
        $(document).ready(function () {

            $('#confirm').click(function (event) {
                $('#confirm').dialog('open');
                event.preventDefault();
            });

            $('#dialog').click(function (event) {
                $('#dialog').dialog('open');
                event.preventDefault();
            });

            // 確認ダイアログ
            $('#confirm').dialog({
                autoOpen: false,
                modal: true,
                top: 0,
                left: 0,
                width: 400,
                open: function (event, ui) {
                    $('.ui-dialog-titlebar-close').hide();
                },
                buttons: [
                    {
                        text: 'OK',
                        click: function (event) {
                            $('#mainForm').submit();
                            var button = $('.ui-dialog-buttonpane').find('button:contains("OK")');
                            button.attr('disabled', 'disabled');
                            button.addClass('ui-state-disabled');
                        }
                    },
                    {
                        text: 'キャンセル',
                        click: function () {
                            $(this).dialog('close');
                        }
                    }
                ]
            });

            $('#dialog').dialog({
                autoOpen: false,
                modal: true,
                top: 0,
                left: 0,
                width: 400,
                open: function (event, ui) {
                    $('.ui-dialog-titlebar-close').hide();
                },
                buttons: [
                    {
                        text: 'OK',
                        click: function () {
                            $(this).dialog('close');
                        }
                    }
                ]
            });

            var dialog = $('#dialog');
            var message = dialog.text();
            // 空白文字をすべて置換（改行コードやタブなど）
            message = message.replace(/\s/g, '');
            // エラーメッセージが格納されていればダイアログに表示
            if (message !== '') {
                dialog.click();
            }
        });
    </script>
</body>
</html>
