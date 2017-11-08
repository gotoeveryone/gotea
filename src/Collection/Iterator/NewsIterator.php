<?php

namespace Gotea\Collection\Iterator;

use Gotea\Model\Entity\Title;

/**
 * Go News出力データを処理するイテレータ
 */
class NewsIterator
{
    /**
     * 実行メソッド
     *
     * @param \Gotea\Model\Entity\Title $item タイトル
     * @param string $key キー
     * @return array
     */
    public function __invoke(Title $item, string $key)
    {
        return [
            'nameEnglish' => $item->name_english,
            'countryName' => $item->country->name_english,
            'countryCode' => $item->country->code,
            'holding' => $item->holding,
            'winnerName' => $item->getWinnerName(false),
            'htmlFileName' => $item->html_file_name,
            'htmlFileModified' => $item->html_file_modified->format('Y-m-d'),
            'isNewHistories' => $item->isNewHistories(),
            'isRecent' => $item->isRecentModified(),
        ];
    }
}
