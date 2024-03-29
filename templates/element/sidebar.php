<?php

/**
 * アプリケーション全体で利用するナビゲーション
 */
?>
<nav class="nav">
<?=
    $this->Html->link('棋士勝敗ランキング出力', [
        '_name' => 'ranking',
    ], ['class' => 'nav-menu']);
?>
<?=
    $this->Html->link('タイトル成績検索', [
        '_name' => 'scores',
    ], ['class' => 'nav-menu']);
?>
<?=
    $this->Html->link('棋士情報検索', [
        '_name' => 'players',
    ], ['class' => 'nav-menu']);
?>
<?=
    $this->Html->link('タイトル情報検索', [
        '_name' => 'titles',
    ], ['class' => 'nav-menu']);
?>
<?=
    $this->Html->link('段位別棋士数検索', [
        '_name' => 'ranks',
    ], ['class' => 'nav-menu']);
?>
<?php if ($this->isAdmin()) : ?>
    <?=
        $this->Html->link('お知らせ一覧', [
        '_name' => 'notifications',
    ], ['class' => 'nav-menu']);
    ?>
    <?=
        $this->Html->link('表テンプレート一覧', [
            '_name' => 'table_templates',
        ], ['class' => 'nav-menu']);
    ?>
<?php endif ?>
<?= $this->cell('Navigation')->render() ?>
</nav>
