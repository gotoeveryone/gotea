<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;

/**
 * ランキング検索用コントローラ
 *
 * @author		Kazuki Kamizuru
 * @since		2015/07/21
 */
class RankingController extends AppController
{
	/**
	 * 描画前処理
	 */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
		$this->set('cakeDescription', '棋士勝敗ランキング出力');
    }

    /**
     * メイン処理
     */
    public function index()
    {
		// 所属国プルダウン
        $countries = TableRegistry::get('Countries');
		$this->set('countries', $countries->findCountryHasFileToArrayWithSuffix());

        // 年度プルダウン
        $scores = TableRegistry::get('PlayerScores');
        $this->set('years', $scores->findScoreUpdateToArrayWithSuffix());
    }
}
