<?php

namespace App\Model\Entity;

use Cake\Network\Request;
use Cake\ORM\TableRegistry;

/**
 * 棋士成績エンティティ
 */
class PlayerScore extends AppEntity
{
    /**
     * 段位を設定します。
     * 
     * @param $rankId
     */
    public function setRank($rankId)
    {
        $ranks = TableRegistry::get('Ranks');
        $this->set('rank', $ranks->get($rankId));
    }

    /**
     * リクエストの値をエンティティに保存します。
     * 
     * @param Request $request
     */
    public function setFromRequest(Request $request)
    {
        // 対象年
        $this->target_year = $request->data('selectYear');
        // 勝数
		$this->win_point = $request->data('selectWinPoint');
        // 敗数
		$this->lose_point = $request->data('selectLosePoint');
        // 引分数
		$this->draw_point = $request->data('selectDrawPoint');
        // 勝数（国際棋戦）
		$this->win_point_world = $request->data('selectWinPointWr');
        // 敗数（国際棋戦）
		$this->lose_point_world = $request->data('selectLosePointWr');
        // 引分数（国際棋戦）
		$this->draw_point_world = $request->data('selectDrawPointWr');
    }
}
