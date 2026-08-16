<?php
declare(strict_types=1);

namespace Gotea\Collection\Iterator;

use Gotea\Model\Entity\RetentionHistory;

/**
 * 公開 API のタイトル保持履歴を処理するイテレータ。
 */
class PublicRetentionHistoryIterator
{
    /**
     * @param \Gotea\Model\Entity\RetentionHistory $item 保持履歴
     * @param int $index インデックス
     * @return array<string, mixed>
     */
    public function __invoke(RetentionHistory $item, int $index): array
    {
        $player = $item->player;
        $country = $item->country;

        return [
            'holding' => $item->holding,
            'targetYear' => $item->target_year,
            'winnerName' => $item->is_team ? $item->win_group_name : $player?->name,
            'winnerNameEnglish' => $item->is_team ? $item->win_group_name : $player?->name_english,
            'countryName' => $country?->name_english,
            'countryCode' => $country?->code,
            'isTeam' => $item->is_team,
            'acquired' => $item->acquired?->format('Y-m-d'),
            'broadcasted' => $item->broadcasted?->format('Y-m-d'),
            'isOfficial' => $item->is_official,
        ];
    }
}
