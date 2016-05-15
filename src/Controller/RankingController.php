<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Psr\Log\LogLevel;

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

    /**
     * 検索処理
     */
    public function search()
    {
        // パラメータ
        $country = $this->request->data('selectCountry');
        $year = $this->request->data('selectYear');
        $rank = $this->request->data('selectRank');
        $json = $this->Json->getRanking($country, $year, $rank, true);
        $this->set('json', $json);
        return $this->index();
    }

    /**
     * JSON出力処理
     */
    public function output()
    {
        // パラメータ
        $country = $this->request->data('selectCountry');
        $year = $this->request->data('selectYear');
        $rank = $this->request->data('selectRank');
        $this->log(__("country:{$country} - year:{$year} - rank:{$rank}"), LogLevel::INFO);

        // 取得したJSONをファイル出力
        $json = $this->Json->getRanking($country, $year, $rank);
        $dir = $json["countryAbbreviation"];
        $fileName = strtolower($json["countryName"]);
        if (file_put_contents("/share/Homepage/{$dir}/{$fileName}.json", json_encode($json))) {
            $this->Flash->info(__("JSON出力に成功しました。"));
        } else {
            $this->Flash->error(__("JSON出力に失敗しました…。"));
        }

        return $this->index();
    }
}
