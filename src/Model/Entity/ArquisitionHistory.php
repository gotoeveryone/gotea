<?php

namespace App\Model\Entity;

use Cake\Network\Request;
use Cake\ORM\TableRegistry;

/**
 * 取得履歴エンティティ
 */
class ArquisitionHistory extends AppEntity
{
    /**
     * タイトル保持者を取得します。
     * 
     * @param type $is_team
     * @return type
     */
    public function getWinnerName($is_team = false)
    {
        return $is_team ? $this->win_group_name : __("{$this->player->name} {$this->rank->name}");
    }

    /**
     * 棋士を設定します。
     * 
     * @param $playerId
     */
    public function setPlayer($playerId)
    {
        $players = TableRegistry::get('Players');
        $this->player = $players->get($playerId);
    }

    /**
     * 段位を設定します。
     * 
     * @param $rankId
     */
    public function setRank($rankId)
    {
        $ranks = TableRegistry::get('Ranks');
        $this->rank = $ranks->get($rankId);
    }

    /**
     * リクエストの値をエンティティに保存します。
     * 
     * @param Request $request
     * @param type $titleId
     * @param type $holding
     */
    public function setFromRequest(Request $request, $titleId, $holding)
    {
        // タイトルID
        $this->title_id = $titleId;
        // 期
        $this->holding = $holding;
        // 対象年
        $this->target_year = $request->data('registYear');
        // 優勝棋士ID
		$playerId = $request->data('registPlayerId');
        if ($playerId) {
            $this->setPlayer($playerId);
        }
        // 優勝棋士段位
		$rankId = $request->data('registRank');
        if ($rankId) {
            $this->setRank($rankId);
        }
        // 優勝団体名
        $winGroupName = $request->data('registGroupName');
        $this->win_group_name = (empty($winGroupName) ? null : $winGroupName);
    }
}
