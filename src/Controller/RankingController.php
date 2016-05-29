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
    // タイトル保持情報テーブル
    private $PlayerScores = null;

    // 所属国マスタテーブル
    private $Countries = null;

    /**
     * 初期処理
     */
	public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Json');
        $this->PlayerScores = TableRegistry::get('PlayerScores');
        $this->Countries = TableRegistry::get('Countries');
    }

	/**
	 * 描画前処理
	 */
    public function beforeRender(Event $event)
    {
        $this->_setTitle('棋士勝敗ランキング出力');
        parent::beforeRender($event);
    }

    /**
     * メイン処理
     */
    public function index()
    {
		// 所属国プルダウン
		$this->set('countries', $this->Countries->findCountryHasFileToArrayWithSuffix());

        // 年度プルダウン
        $this->set('years', $this->PlayerScores->findScoreUpdateToArrayWithSuffix());

        return $this->render('index');
    }
}
