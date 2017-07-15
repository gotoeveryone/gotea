<?php

namespace App\Model\Entity;

use Cake\ORM\TableRegistry;

/**
 * 保持履歴エンティティ
 */
class RetentionHistory extends AppEntity
{
    /**
     * 棋士を取得します。
     *
     * @param mixed $value
     * @return Player|null
     */
    protected function _getPlayer($value)
    {
        if ($value) {
            return $value;
        }

        if (!$this->player_id) {
            return null;
        }

        $result = TableRegistry::get('Players')->get($this->player_id);
        return $this->player = $result;
    }

    /**
     * タイトル保持者を取得します。
     *
     * @param bool $is_team
     * @return string
     */
    public function getWinnerName($is_team = false)
    {
        return $is_team ? $this->win_group_name : "{$this->player->name} {$this->rank->name}";
    }
}
