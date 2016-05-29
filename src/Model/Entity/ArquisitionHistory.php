<?php

namespace App\Model\Entity;

use Cake\Network\Request;

/**
 * 取得履歴エンティティ
 */
class ArquisitionHistory extends AppEntity
{
    /**
     * タイトル保持者を取得します。
     * 
     * @param type $groupFlag
     * @return type
     */
    public function getWinnerName($groupFlag = false)
    {
        return $groupFlag ? $this->WIN_GROUP_NAME : __("{$this->player->NAME} {$this->rank->NAME}");
    }

    /**
     * リクエストの値をエンティティに保存します。
     * 
     * @param Request $request
     * @param type $titleId
     * @param type $holding
     */
    public function patchEntity(Request $request, $titleId, $holding)
    {
        // タイトルID
        $this->set('TITLE_ID', $titleId);
        // 期
        $this->set('HOLDING', $holding);
        // 対象年
        $this->set('TARGET_YEAR', $request->data('registYear'));
        // 優勝棋士ID
		$playerId = $request->data('registPlayerId');
        $this->set('PLAYER_ID', (empty($playerId) ? null : $playerId));
        // 優勝棋士段位
		$rankId = $request->data('registRank');
        $this->set('RANK_ID', (empty($rankId) ? null : $rankId));
        // 優勝団体名
        $winGroupName = $request->data('registGroupName');
        $this->set('WIN_GROUP_NAME', (empty($winGroupName) ? null : $winGroupName));
        // 終了フラグ
        $this->set('DELETE_FLAG', 0);
    }
}
