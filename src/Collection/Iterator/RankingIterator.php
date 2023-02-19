<?php
declare(strict_types=1);

namespace Gotea\Collection\Iterator;

/**
 * ランキングデータを処理するイテレータ
 */
class RankingIterator
{
    /**
     * 実行メソッド
     *
     * @param array $item ランキングデータ
     * @param int $index インデックス
     * @return array
     */
    public function __invoke(array $item, int $index): array
    {
        return [
            'id' => $item['id'],
            'rank' => $item['win_rank'],
            'name' => $item['name'],
            'win' => $item['win'],
            'lose' => $item['lose'],
            'draw' => $item['draw'],
            'percentage' => $item['percentage'],
            'sex' => $item['sex'],
            'url' => $item['url'],
        ];
    }
}
