<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TitleScores Controller
 *
 * @property \App\Model\Table\TitleScoresTable $TitleScores
 */
class TitleScoresController extends AppController
{
    /**
     * 初期処理
     */
	public function initialize()
    {
        parent::initialize();

        $this->_setTitle('タイトル勝敗検索');

        // モデルをロード
        $this->loadModel('TitleScoreDetails');
        $this->loadModel('Players');
        $this->loadModel('Countries');
    }

    /**
     * 初期表示、検索処理
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
		// 所属国プルダウン
		$this->set('countries', $this->Countries->findCountryHasFileToArray());

        // 検索
        if ($this->request->isPost()) {
            $countryId = (int) $this->request->data('country_id');
            $titleScores = $this->TitleScores->findMatches(
                $countryId, $this->request->data('name'), $this->request->data('started'), $this->request->data('ended')
            );
            $this->set('titleScores', $titleScores);
        }

        return $this->render('index');
    }
}
