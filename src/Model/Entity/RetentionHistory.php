<?php

namespace Gotea\Model\Entity;

use Cake\ORM\TableRegistry;

/**
 * 保持履歴エンティティ
 */
class RetentionHistory extends AppEntity
{
    /**
     * 棋士を取得します。
     *
     * @param mixed $value 値
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
     * 団体戦判定結果を取得します。
     *
     * @return string
     */
    protected function _getTeamLabel()
    {
        return __($this->is_team ? '（団体）' : '（個人）');
    }

    /**
     * タイトル保持者を取得します。
     *
     * @return string
     */
    protected function _getWinnerName()
    {
        if ($this->is_team) {
            return $this->win_group_name;
        }
        if ($this->player_id && $this->rank_id) {
            return "{$this->player->name} {$this->rank->name}";
        }

        return '';
    }
}
