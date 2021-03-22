<?php
declare(strict_types=1);

namespace Gotea\Model\Entity;

/**
 * 保持履歴エンティティ
 *
 * @property int $id
 * @property int $title_id
 * @property int $player_id
 * @property int $holding
 * @property int $target_year
 * @property string $name
 * @property string $win_group_name
 * @property bool $is_team
 * @property \Cake\I18n\FrozenDate $acquired
 * @property bool $is_official
 * @property \Cake\I18n\FrozenDate|null $broadcasted
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \Gotea\Model\Entity\Title $title
 * @property \Gotea\Model\Entity\Player|null $player
 * @property \Gotea\Model\Entity\Country|null $country
 * @property \Gotea\Model\Entity\Rank|null $rank
 *
 * @property string $team_label
 * @property string $winner_name
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
        if ($this->player_id) {
            return "{$this->player->name} {$this->player->getRankByDate($this->acquired)->name}";
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
        // 放映日があればその値を基準に判定する
        if ($this->broadcasted !== null) {
            return $this->broadcasted->wasWithinLast('20 days');
        }

        return $this->acquired->wasWithinLast('20 days');
    }
}
