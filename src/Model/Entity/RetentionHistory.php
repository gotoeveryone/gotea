<?php

namespace App\Model\Entity;

/**
 * 保持履歴エンティティ
 */
class RetentionHistory extends AppEntity
{
    /**
     * 棋士を取得します。
     * 
     * @param type $player
     * @return App\Model\Entity\Player
     */
    protected function _getPlayer($player)
    {
        return ($player ? $player : TableRegistry::get('Players')->get($this->player_id));
    }

    /**
     * タイトル保持者を取得します。
     * 
     * @param type $is_team
     * @return type
     */
    public function getWinnerName($is_team = false)
    {
        return $is_team ? $this->win_group_name : __("{$this->player->name} {$this->rank->name}");
    }
}
