<?php
/**
 * @var \Gotea\View\AppView $this ビューオブジェクト
 * @var \Gotea\Model\Entity\Player $player 棋士データ
 */
$isAdmin = $this->isAdmin();
?>
<player :is-admin="<?= $isAdmin ? 'true' : 'false' ?>" :id="<?= $player->id ?>" />
