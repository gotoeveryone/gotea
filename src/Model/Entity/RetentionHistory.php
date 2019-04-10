<?php

namespace Gotea\Model\Entity;

/**
 * 保持履歴エンティティ
 *
 * @property int $id
 * @property int $title_id
 * @property int $player_id
 * @property int $rank_id
 * @property int $holding
 * @property int $target_year
 * @property string $name
 * @property string $win_group_name
 * @property bool $is_team
 * @property \Cake\I18n\FrozenDate $acquired
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \Gotea\Model\Entity\Title $title
 * @property \Gotea\Model\Entity\Player|null $player
 * @property \Gotea\Model\Entity\Rank|null $rank
 */
class RetentionHistory extends AppEntity
{
    use PlayerTrait;
    use RankTrait;

    /**
     * 団体戦判定結果を取得します。
     *
     * @return string
     */
    protected function _getTeamLabel()
    {
        return __($this->is_team ? '（団体戦）' : '（個人戦）');
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

    /**
     * 新着データかどうかを判定する。
     *
     * @return bool
     */
    public function isRecent(): bool
    {
        return $this->acquired->wasWithinLast('20 days');
    }
}
