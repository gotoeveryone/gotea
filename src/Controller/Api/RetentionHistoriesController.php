<?php

namespace Gotea\Controller\Api;

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
    public function view(int $id)
    {
        $history = $this->RetentionHistories->get($id, [
            'contain' => ['Players', 'Ranks'],
        ]);

        return $this->renderJson([
            'id' => $history->id,
            'titleId' => $history->title_id,
            'holding' => $history->holding,
            'targetYear' => $history->target_year,
            'winPlayerName' => $history->player ? $history->player->name : '',
            'winGroupName' => $history->win_group_name,
            'winRankName' => $history->is_team ? null : $history->rank->name,
            'isTeam' => $history->is_team,
            'acquired' => $history->acquired->format('Y/m/d'),
            'playerId' => $history->player_id,
            'countryId' => $history->country_id,
            'rankId' => $history->rank_id,
        ]);
    }
}
