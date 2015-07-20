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
    public function index() {
		// 所属国プルダウン
        $countries = TableRegistry::get('Countries')->find('list', [
            'keyField' => 'keyField',
            'valueField' => 'valueField'
        ])->where(function ($exp, $q) {
            return $exp->isNotNull('OUTPUT_FILE_NAME');
        })->order(['Countries.COUNTRY_CD' => 'ASC'])->select([
            'keyField' => 'Countries.COUNTRY_CD',
            'valueField' => 'CASE Countries.COUNTRY_CD WHEN \'99\' THEN CONCAT(Countries.COUNTRY_NAME, \'棋戦\') ELSE CONCAT(Countries.COUNTRY_NAME, \'棋士\') END'
        ])->toArray();
		$this->set('countries', $countries);

        // 年度プルダウン
        $years = TableRegistry::get('PlayerScores')->find('list', [
            'keyField' => 'keyField',
            'valueField' => 'valueField'
        ])->group(['PlayerScores.TARGET_YEAR'])->order(['PlayerScores.TARGET_YEAR' => 'DESC'])->select([
            'keyField' => 'PlayerScores.TARGET_YEAR',
            'valueField' => 'CONCAT(PlayerScores.TARGET_YEAR, \'年度\')'
        ])->toArray();
        $this->set('years', $years);
    }
}
