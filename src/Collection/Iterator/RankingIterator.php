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
        $res = [
            'rank' => $item['win_rank'],
            'name' => $item['name'],
            'win' => $item['win'],
            'lose' => $item['lose'],
            'draw' => $item['draw'],
            'percentage' => $item['percentage'],
        ];

        // 以下は項目が存在する場合のみ出力
        $optionFields = ['id', 'sex', 'url'];
        foreach ($optionFields as $field) {
            if (isset($item[$field])) {
                $res[$field] = $item[$field];
            }
        }

        return $res;
    }
}
