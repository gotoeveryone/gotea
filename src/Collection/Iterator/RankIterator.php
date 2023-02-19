<?php
declare(strict_types=1);

namespace Gotea\Collection\Iterator;

/**
 * 段位データを処理するイテレータ
 */
class RankIterator
{
    /**
     * 実行メソッド
     *
     * @param mixed $item 段位データ
     * @param int $index インデックス
     * @return array
     */
    public function __invoke(mixed $item, int $index): array
    {
        return [
            'id' => $item->id,
            'rank' => $item->rank_numeric,
            'name' => $item->name,
            'count' => $item->count,
        ];
    }
}
