<?php

namespace Gotea\Model\Query;

use Cake\ORM\Query;
use Gotea\Utility\CalculatorTrait;

class RankingQuery extends Query
{
    use CalculatorTrait;

    /**
     * ランキングモデルを配列に変換します。
     *
     * @param bool $isWorlds 国際棋戦かどうか
     * @param bool $withJa 日本語情報を表示するかどうか
     * @return \Cake\Collection\Collection ランキング
     */
    public function mapRanking(bool $isWorlds, bool $withJa)
    {
        $rank = 0;
        $win = 0;

        return $this->each(function ($item, $key) use (&$rank, &$win) {
            if ($win !== $item->win_point) {
                $rank = $key + 1;
                $win = $item->win_point;
            }
            $item->win_rank = $rank;
        })->map(function ($item, $key) use ($isWorlds, $withJa) {
            $sum = $item->win + $item->lose;

            $row = [
                'rank' => $item->win_rank,
                'name' => $item->getRankingName($isWorlds, $withJa),
                'win' => (int) $item->win_point,
                'lose' => (int) $item->lose_point,
                'draw' => (int) $item->draw_point,
                'percentage' => $this->percent($item->win_point, $item->lose_point),
            ];

            // 日本語出力あり
            if ($withJa) {
                $row['id'] = $item->player->id;
                $row['sex'] = $item->player->sex;
            }

            return $row;
        });
    }
}
