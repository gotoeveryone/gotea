<?php
namespace Gotea\Model\Entity;

/**
 * TitleScoreDetail Entity
 *
 * @property int $id
 * @property int $title_score_id
 * @property int $player_id
 * @property string $division
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 *
 * @property \Gotea\Model\Entity\TitleScore $title_score
 * @property \Gotea\Model\Entity\Player $player
 */
class TitleScoreDetail extends AppEntity
{
    /**
     * ランキング表示用の名前を取得します。
     *
     * @param boolean $isWorlds 国際棋戦かどうか
     * @param boolean $showJp 日本語で表示するか
     * @return string ランキング表示用の名前
     */
    public function getRankingName(bool $isWorlds, $showJp = false): string
    {
        // 取得するプロパティ名のサフィックス
        $suffix = ($showJp ? '' : '_english');
        $propertyName = 'name'.$suffix;

        // 棋士名
        $name = $this->player->$propertyName;

        // 国際棋戦は所属国を表示
        if ($isWorlds) {
            $countryName = $this->player->country->$propertyName;
            return $name.'('.$countryName.')';
        }

        // 上記以外は段位を表示
        $rankName = $this->player->rank->name;
        $rankNumeric = $this->player->rank->rank_numeric;

        return $name.($showJp ? " ${rankName}" : "(${rankNumeric} dan)");
    }
}
