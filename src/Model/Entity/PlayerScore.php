<?php

namespace App\Model\Entity;

use Cake\Network\Request;
use Cake\ORM\TableRegistry;

/**
 * 棋士成績情報エンティティ
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
    public function patchEntity(Request $request)
    {
        // 対象年
        $this->set('TARGET_YEAR', $request->data('selectYear'));
        // 勝数
		$this->set('WIN_POINT', $request->data('selectWinPoint'));
        // 敗数
		$this->set('LOSE_POINT', $request->data('selectLosePoint'));
        // 引分数
		$this->set('DRAW_POINT', $request->data('selectDrawPoint'));
        // 勝数（国際棋戦）
		$this->set('WIN_POINT_WORLD', $request->data('selectWinPointWr'));
        // 敗数（国際棋戦）
		$this->set('LOSE_POINT_WORLD', $request->data('selectLosePointWr'));
        // 引分数（国際棋戦）
		$this->set('DRAW_POINT_WORLD', $request->data('selectDrawPointWr'));
    }
}
