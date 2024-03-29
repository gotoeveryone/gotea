<?php
declare(strict_types=1);

namespace Gotea\Collection\Iterator;

use Cake\Routing\Router;
use Gotea\Model\Entity\Title;

/**
 * タイトルを処理するイテレータ
 */
class TitlesIterator
{
    /**
     * 実行メソッド
     *
     * @param \Gotea\Model\Entity\Title $item タイトル
     * @param int $index インデックス
     * @return array
     */
    public function __invoke(Title $item, int $index): array
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'nameEnglish' => $item->name_english,
            'countryId' => $item->country_id,
            'countryName' => $item->country->name_english,
            'countryCode' => $item->country->code,
            'holding' => $item->holding,
            'winnerName' => $item->getWinnerName(true),
            'sortOrder' => $item->sort_order,
            'isTeam' => $item->is_team,
            'htmlFileName' => $item->html_file_name,
            'htmlFileHolding' => $item->html_file_holding,
            'htmlFileModified' => $item->html_file_modified->format('Y/m/d'),
            'isNewHistories' => $item->isNewHistories(),
            'isRecent' => $item->isRecentModified(),
            'isClosed' => $item->is_closed,
            'isOutput' => $item->is_output,
            'isOfficial' => $item->is_official,
            'url' => Router::url(['_name' => 'view_title', $item->id]),
        ];
    }
}
