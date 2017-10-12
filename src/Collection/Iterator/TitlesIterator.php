<?php

namespace Gotea\Collection\Iterator;

use Gotea\Model\Entity\Title;

/**
 * タイトルを処理するイテレータ
 */
class TitlesIterator
{
    public function __invoke(Title $item, $key)
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
            'htmlFileModified' => $item->html_file_modified->format('Y/m/d'),
            'isNewHistories' => $item->isNewHistories(),
            'isRecent' => $item->isRecentModified(),
            'isClosed' => $item->is_closed,
        ];
    }
}
