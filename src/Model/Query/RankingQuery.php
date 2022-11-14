<?php
declare(strict_types=1);

namespace Gotea\Model\Query;

use Cake\Collection\Collection;
use Cake\I18n\Number;
use Cake\ORM\Query;
use Cake\Routing\Router;

class RankingQuery extends Query
{
    /**
     * ランキングモデルを配列に変換します。
     *
     * @param bool $isWorlds 国際棋戦かどうか
     * @param bool $withJa 日本語情報を表示するかどうか
     * @param string $type 種類（何順で表示するか）
     * @return \Cake\Collection\Collection ランキング
     */
    public function mapRanking(bool $isWorlds, bool $withJa, string $type): Collection
    {
        $rank = 0;
        $win = 0;

        return $this->all()->each(function ($item, $key) use (&$rank, &$win, $type): void {
            $currentValue = $type === 'percent' ? $item->win_percent : $item->win_point;
            if ($win !== $currentValue) {
                $rank = $key + 1;
                $win = $currentValue;
            }
            $item->win_rank = $rank;
        })->map(function ($item) use ($isWorlds, $withJa) {
            $row = [
                'rank' => $item->win_rank,
                'name' => $item->getRankingName($isWorlds, $withJa),
                'win' => (int)$item->win_point,
                'lose' => (int)$item->lose_point,
                'draw' => (int)$item->draw_point,
                'percentage' => Number::toPercentage($item->win_percent, 0, [
                    'multiply' => true,
                ]),
            ];

            // 日本語出力あり
            if ($withJa) {
                $row['id'] = $item->player->id;
                $row['sex'] = $item->player->sex;
                $row['url'] = Router::url(['_name' => 'view_player', $item->player->id]);
            }

            return $row;
        });
    }
}
