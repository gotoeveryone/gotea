<?php
declare(strict_types=1);

namespace Gotea\Collection\Iterator;

use Gotea\Model\Entity\Title;

/**
 * 公開 API のタイトル詳細を処理するイテレータ。
 */
class PublicTitleIterator
{
    /**
     * @param \Gotea\Model\Entity\Title $item タイトル
     * @param int $index インデックス
     * @return array<string, mixed>
     */
    public function __invoke(Title $item, int $index): array
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'nameEnglish' => $item->name_english,
            'countryName' => $item->country->name_english,
            'countryCode' => $item->country->code,
            'holding' => $item->holding,
            'winnerName' => $item->getWinnerName(false),
            'sortOrder' => $item->sort_order,
            'isTeam' => $item->is_team,
            'htmlFileName' => $item->html_file_name,
            'htmlFileHolding' => $item->html_file_holding,
            'htmlFileModified' => $item->html_file_modified->format('Y-m-d'),
            'isNewHistories' => $item->isNewHistories(),
            'isRecent' => $item->isRecentModified(),
            'isClosed' => $item->is_closed,
            'isOfficial' => $item->is_official,
            'histories' => collection($item->retention_histories)
                ->map(new PublicRetentionHistoryIterator())
                ->toList(),
        ];
    }
}
