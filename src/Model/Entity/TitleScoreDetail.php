<?php
namespace Gotea\Model\Entity;

/**
 * TitleScoreDetail Entity
 *
 * @property int $id
 * @property int $title_score_id
 * @property int $player_id
 * @property string $player_name
 * @property int $rank_id
 * @property string $division
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 *
 * @property \Gotea\Model\Entity\TitleScore $title_score
 * @property \Gotea\Model\Entity\Player $player
 * @property \Gotea\Model\Entity\Rank $rank
 *
 * @property string $player_name_with_rank
 */
class TitleScoreDetail extends AppEntity
{
    use PlayerTrait;
    use RankTrait;

    /**
     * 棋士名と段位を返却します。
     *
     * @return string 棋士名 段位
     */
    protected function _getPlayerNameWithRank()
    {
        return implode(' ', [$this->player_name, $this->rank->name]);
    }

    /**
     * ランキング表示用の名前を取得します。
     *
     * @param bool $isWorlds 国際棋戦かどうか
     * @param bool $showJp 日本語で表示するか
     * @return string ランキング表示用の名前
     */
    public function getRankingName(bool $isWorlds, $showJp = false): string
    {
        // 取得するプロパティ名のサフィックス
        $suffix = ($showJp ? '' : '_english');
        $propertyName = "name${suffix}";

        // 棋士名
        $name = $this->player->$propertyName;

        // 国際棋戦は所属国を表示
        if ($isWorlds) {
            $countryName = $this->player->country->$propertyName;

            return "${name}(${countryName})";
        }

        // 上記以外は段位を表示
        $rank = $this->player->rank;
        if ($this->player->player_ranks) {
            $rank = collection($this->player->player_ranks)
                ->sortBy('rank_numeric', SORT_DESC, SORT_NUMERIC)->first()->rank;
        }

        return $name . ($showJp ? " {$rank->name}" : "({$rank->rank_numeric} dan)");
    }
}
