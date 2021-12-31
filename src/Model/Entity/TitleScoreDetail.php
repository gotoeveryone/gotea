<?php
declare(strict_types=1);

namespace Gotea\Model\Entity;

use Cake\I18n\FrozenDate;

/**
 * TitleScoreDetail Entity
 *
 * @property int $id
 * @property int $title_score_id
 * @property int $player_id
 * @property string $player_name
 * @property string $division
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \Gotea\Model\Entity\TitleScore $title_score
 * @property \Gotea\Model\Entity\Player $player
 *
 * 以下は findScores() 関数で取得した場合に利用できるプロパティ
 * @see \Gotea\Model\Table\TitleScoreDetailsTable findScores()
 * @property int $target_year
 * @property int $win_point
 * @property int $lose_point
 * @property int $draw_point
 * @property int $win_point_world
 * @property int $lose_point_world
 * @property int $draw_point_world
 * @property int $win_point_all
 * @property int $lose_point_all
 * @property int $draw_point_all
 */
class TitleScoreDetail extends AppEntity
{
    use PlayerTrait;
    use RankTrait;

    /**
     * 棋士名と当時の段位を返却します。
     *
     * @param \Cake\I18n\FrozenDate $baseDate 基準となる日付
     * @return string 棋士名 段位
     */
    public function getPlayerNameWithRank(FrozenDate $baseDate)
    {
        return implode(' ', [$this->player_name, $this->player->getRankByDate($baseDate)->name]);
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
