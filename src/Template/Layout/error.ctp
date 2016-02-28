<?php
/**
 * エラー用の共通テンプレート
 */
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <title>
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>
    <link rel="stylesheet" type="text/css" href="/css/common.css" />
    <?=$this->Html->css('app')?>
</head>
<body>
    <div id="container">
        <div id="header" class="red">
            <h1><?= h($message) ?></h1>
        </div>
        <div id="content">
            <?= $this->Flash->render() ?>

            <?= $this->fetch('content') ?>
        </div>
        <div id="footer">
            <?=
                $this->Html->link('メニューへ戻る', ['controller' => 'menu', 'action' => 'index'])
            ?>
        </div>
    </div>
</body>
</html>
