<?php

namespace Gotea\Model\Entity;

use Cake\ORM\TableRegistry;

/**
 * 棋士の操作クラス
 */
trait PlayerTrait
{
    /**
     * 棋士を取得します。
     *
     * @param \Gotea\Model\Entity\Player|null $player 棋士オブジェクト
     * @return \Gotea\Model\Entity\Player
     */
    protected function _getPlayer($player)
    {
        if ($player) {
            return $player;
        }

        if (!$this->player_id) {
            return null;
        }

        $result = TableRegistry::getTableLocator()->get('Players')->get($this->player_id);

        return $this->player = $result;
    }
}
