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
     * 所属国情報を取得します。
     *
     * @return array 所属国一覧
     */
    public function countries()
    {
        $this->loadModel('Countries');
        $query = $this->Countries->find()->where(['code is not' => null]);
        if ($this->request->getQuery('has_title')) {
            $query->where(['has_title' => true]);
        }
        $countries = $query->select(['id', 'code', 'name', 'name_english'])->all();
        $this->__renderJson($countries->toArray());
    }

    /**
     * 名前をもとに棋士情報を取得します。
     *
     * @return array 棋士情報一覧
     */
    public function players()
    {
        $this->__renderJson($this->Json->sendResource(
            'players/search', 'post',
            ['name' => $this->request->getData('name')]
        ));
    }

    /**
     * IDをもとに棋士情報を取得します。
     * 
     * @param $id
     * @return object 棋士情報
     */
    public function player(int $id)
    {
        $this->__renderJson($this->Json->sendResource(
            'players/'.$id, 'get'
        ));
    }

    /**
     * Go Newsの出力データを取得します。
     *
     * @return array タイトル一覧
     */
    public function news()
    {
        // モデルのロード
        $this->loadModel('Titles');
        $titles = $this->Titles->findTitlesByCountry();

        // JSON生成
        $json = $this->Titles->toRankingArray($titles);

        // パラメータがあればファイル作成
        if ($this->request->getQuery('make') === 'true') {
            if (!file_put_contents($this->_homepage.'news.json', json_encode($json))) {
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
     * @return array ランキング
     */
    public function rankings($country = null, $year = null, $rank = null)
    {
        // 日本語情報を出力するかどうか
        $isJp = ($this->request->getQuery('jp') === 'true');

        // 2017年以降
        if ($year >= 2017) {
            $json = $this->__newRankings($country, $year, $rank, $isJp);
        } else {
            // 2016年以前
            $encodeCountry = urlencode($country);
            $path = "players/ranking?country={$encodeCountry}&year={$year}&limit={$rank}".($isJp ? "&with=jp" : "");
            $json = $this->Json->sendResource($path, 'get');
        }

        // パラメータがあればファイル作成
        if ($this->request->getQuery('make') === 'true') {
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
     * @return array 段位別棋士一覧
     */
    public function categorize($country = null)
    {
        $encodeCountry = urlencode($country);
        $json = $this->Json->sendResource("players/categorize?country={$encodeCountry}".
                (($this->request->getQuery('all') === 'true') ? '&all=true' : ''), 'get');
        $this->__renderJson($json);
    }

    /**
     * ランキングを取得します。
     * 
     * @param string $countryName
     * @param int $year
     * @param int $rank
     * @param bool $isJp
     * @return array
     */
    private function __newRankings(string $countryName, int $year, int $rank, bool $isJp) : array
    {
        // モデルのロード
        $this->loadModel('Players');
        $this->loadModel('Countries');
        $this->loadModel('TitleScoreDetails');

        // 集計対象国の取得
        $country = $this->Countries->findByName($countryName)->first();

        // ランキングデータの取得
        $models = $this->Players->findRanking($country, $year, $rank);

        // JSON生成
        return [
            'lastUpdate' => $this->TitleScoreDetails->getRecent($country),
            'targetYear' => $year,
            'countryName' => $country->name_english,
            'countryNameAbbreviation' => $country->code,
            'ranking' => $this->Players->toRankingArray($models, $isJp)
        ];
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