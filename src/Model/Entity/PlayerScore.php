<?php

namespace App\Model\Entity;

/**
 * 棋士成績エンティティ
 */
class PlayerScore extends AppEntity
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

        // 棋士名
        $name = $this->player->{'name'.$suffix};

        // 国際棋戦は所属国を表示
        if ($isWorlds) {
            $countryName = $this->player->country->{'name'.$suffix};
            return $name.'('.$countryName.')';
        }

        // 上記以外は段位を表示
        $rankName = ($this->rank ? $this->rank->nae : $this->player->rank->name);
        $rankNumeric = ($this->rank->rank_numeric ? $this->rank->rank_numeric : $this->player->rank->rank_numeric);

        return $name.($showJp ? " ${rankName}" : "(${rankNumeric} dan)");
    }
}
