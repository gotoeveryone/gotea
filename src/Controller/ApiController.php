<?php

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Controller\Exception\MissingActionException;
use Cake\Event\Event;

/**
 * アプリの共通コントローラ
 */
class ApiController extends Controller
{
    // ホームページコンテンツのディレクトリ
    private $homepage = "/share/windows/Kazuki/Homepage/";

    /**
     * 初期処理
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Json');
    }

    /**
     * 描画前処理
     * 
     * @param Event $event
     */
    public function beforeRender(Event $event)
    {
        $this->RequestHandler->renderAs($this, 'json');
        $this->response->type('application/json');
        $this->set('_serialize', true);
    }

    /**
     * 名前をもとに棋士情報を取得する
     * 
     * @param type $name
     */
    public function players($name)
    {
        $json = $this->Json->getPlayer($name);
        $this->set([
            'response' => $json,
            '_serialize' => ['response']
        ]);
    }

    /**
     * Go Newsを取得します。
     */
    public function news()
    {
        $json = $this->Json->getNews();
        // パラメータがあればファイル作成
        if ($this->request->query('make') === 'true') {
            if (!file_put_contents($this->homepage."news.json", json_encode($json))) {
                throw new MissingActionException(__("JSON出力失敗"), 500);
            }
        }
        $this->set([
            'response' => $json,
            '_serialize' => ['response']
        ]);
    }

    /**
     * ランキングを取得します。
     * 
     * @param type $country
     * @param type $year
     * @param type $rank
     */
    public function rankings($country = null, $year = null, $rank = null)
    {
        $json = $this->Json->getRanking($country, $year, $rank, ($this->request->query('jp') === 'true'));
        // パラメータがあればファイル作成
        if ($this->request->query('make') === 'true') {
            $dir = $json["countryNameAbbreviation"];
            $fileName = strtolower($json["countryName"]).$json["targetYear"];
            if (!file_put_contents($this->homepage."{$dir}/{$fileName}.json", json_encode($json))) {
                throw new MissingActionException(__("JSON出力失敗"), 500);
            }
        }
        $this->set([
            'response' => $json,
            '_serialize' => ['response']
        ]);
    }
}