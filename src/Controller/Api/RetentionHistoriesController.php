<?php

namespace Gotea\Controller\Api;

/**
 * API・タイトル保持履歴コントローラ
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

        return $this->_renderJson([
            'id' => $history->id,
            'titleId' => $history->title_id,
            'holding' => $history->holding,
            'targetYear' => $history->target_year,
            'winPlayerName' => $history->player ? $history->player->name : '',
            'winGroupName' => $history->win_group_name,
            'winRankName' => $history->is_team ? null : $history->rank->name,
            'isTeam' => $history->is_team,
            'playerId' => $history->player_id,
            'rankId' => $history->rank_id,
        ]);
    }
}
