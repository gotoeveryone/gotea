<?php
/**
 * アプリケーション全体で利用するナビゲーション
 */
?>
<nav class="nav">
    <?=
        $this->Html->link('棋士勝敗ランキング出力', [
            'controller' => 'players',
            'action' => 'ranking'
        ], ['class' => 'nav-menu']);
    ?>
    <?=
        $this->Html->link('タイトル勝敗検索', [
            'controller' => 'titleScores',
            'action' => 'index'
        ], ['class' => 'nav-menu']);
    ?>
    <?=
        $this->Html->link('棋士情報検索', [
            'controller' => 'players',
            'action' => 'index'
        ], ['class' => 'nav-menu']);
    ?>
    <?=
        $this->Html->link('タイトル情報検索', [
            'controller' => 'titles',
            'action' => 'index'
        ], ['class' => 'nav-menu']);
    ?>
    <?=
        $this->Html->link('段位別棋士数検索', [
            'controller' => 'players',
            'action' => 'view-ranks'
        ], ['class' => 'nav-menu']);
    ?>
    <?php if ($admin) : ?>
    <?=
        $this->Html->link('各種情報クエリ更新', [
            'controller' => 'nativeQuery',
            'action' => 'index'
        ], ['class' => 'nav-menu']);
    ?>
    <?php endif ?>
</nav>
