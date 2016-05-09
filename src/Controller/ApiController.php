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
        // TODO：タイトル保持情報の棋士検索で利用する
        $url = "http://localhost/WebResource/api/igokisen/players/?name={$name}";
        $json = $this->Json->getJson($url);
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
        $json = $this->Json->getNewsJson();
        // パラメータがあればファイル作成
        if ($this->request->query('make') === 'true') {
            if (!file_put_contents("/share/windows/Kazuki/Homepage/news.json", json_encode($json))) {
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
        $json = $this->Json->getRankingJson($country, $year, $rank);
        // パラメータがあればファイル作成
        if ($this->request->query('make') === 'true') {
            $dir = $json["countryAbbreviation"];
            $fileName = strtolower($json["countryName"]);
            if (!file_put_contents("/share/windows/Kazuki/Homepage/{$dir}/{$fileName}.json", json_encode($json))) {
                throw new MissingActionException(__("JSON出力失敗"), 500);
            }
        }
        $this->set([
            'response' => $json,
            '_serialize' => ['response']
        ]);
    }
}