<?php
declare(strict_types=1);

namespace Gotea\Controller\Api;

use Cake\Http\Response;

/**
 * API・タイトル保持履歴コントローラ
 *
 * @property \Gotea\Model\Table\RetentionHistoriesTable $RetentionHistories
 */
class RetentionHistoriesController extends ApiController
{
    /**
     * 履歴を1件取得します。
     *
     * @param int $id ID
     * @return \Cake\Http\Response 所属国一覧
     */
    public function view(int $id): Response
    {
        /** @var \Gotea\Model\Entity\RetentionHistory $history */
        $history = $this->RetentionHistories->get($id, [
            'contain' => ['Players', 'Players.PlayerRanks.Ranks'],
        ]);

        return $this->renderJson([
            'id' => $history->id,
            'titleId' => $history->title_id,
            'holding' => $history->holding,
            'targetYear' => $history->target_year,
            'winPlayerName' => $history->winner_name,
            'winGroupName' => $history->win_group_name,
            'isTeam' => $history->is_team,
            'acquired' => $history->acquired->format('Y/m/d'),
            'isOfficial' => $history->is_official,
            'broadcasted' => $history->broadcasted === null ? null : $history->broadcasted->format('Y/m/d'),
            'playerId' => $history->player_id,
            'countryId' => $history->country_id,
        ]);
    }
}
