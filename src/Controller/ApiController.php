<?php

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Controller\Exception\MissingActionException;

/**
 * アプリの共通コントローラ
 * 
 * @property \App\Controller\Component\JsonComponent $Json
 */
class ApiController extends Controller
{
    // ホームページコンテンツのディレクトリ
    private $_homepage = "/share/windows/Homepage/";

    /**
     * 初期処理
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Json');

        // 当アクションのレスポンスはすべてJSON形式
        $this->RequestHandler->renderAs($this, 'json');
        $this->response->type('application/json');
    }

    /**
     * 名前をもとに棋士情報を取得します。
     */
    public function players()
    {
        $this->__renderJson($this->Json->sendResource(
            'players/search', 'post',
            ['name' => $this->request->data('name')]
        ));
    }

    /**
     * IDをもとに棋士情報を取得します。
     * 
     * @param $id
     */
    public function player(int $id)
    {
        $this->__renderJson($this->Json->sendResource(
            'players/'.$id, 'get'
        ));
    }

    /**
     * Go Newsを取得します。
     */
    public function news()
    {
        $json = $this->Json->sendResource('titles/news', 'get');
        // パラメータがあればファイル作成
        if ($this->request->query('make') === 'true') {
            if (!file_put_contents($this->_homepage."news.json", json_encode($json))) {
                throw new MissingActionException(__("JSON出力失敗"), 500);
            }
        }
        $this->__renderJson($json);
    }

    /**
     * ランキングを取得します。
     * 
     * @param string $country
     * @param string $year
     * @param string $rank
     */
    public function rankings($country = null, $year = null, $rank = null)
    {
        $encodeCountry = urlencode($country);
        $path = "players/ranking?country={$encodeCountry}&year={$year}&limit={$rank}".($this->request->query('jp') === 'true' ? "&with=jp" : "");
        $json = $this->Json->sendResource($path, 'get');
        // パラメータがあればファイル作成
        if ($this->request->query('make') === 'true') {
            $dir = $json["countryNameAbbreviation"];
            $fileName = strtolower($json["countryName"]).$json["targetYear"];
            if (!file_put_contents($this->_homepage."{$dir}/ranking/{$fileName}.json", json_encode($json))) {
                throw new MissingActionException(__("JSON出力失敗"), 500);
            }
        }
        $this->__renderJson($json);
    }

    /**
     * カテゴリを取得します。
     * 
     * @param string $country
     */
    public function categorize($country = null)
    {
        $encodeCountry = urlencode($country);
        $json = $this->Json->sendResource("players/categorize?country={$encodeCountry}".
                (($this->request->query('all') === 'true') ? "&all=true" : ""), 'get');
        $this->__renderJson($json);
    }

    /**
     * レスポンスにJSONを設定します。
     * 
     * @param type $json
     */
    private function __renderJson($json)
    {
        $this->set([
            'response' => $json,
            '_serialize' => true
        ]);
    }
}