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
     * @param $id
     * @return array 棋士情報一覧
     */
    public function players($id = null)
    {
        $res = [];
        if (!($name = $this->request->getData('name'))) {
            $this->__renderJson($res);
            return;
        }

        $this->loadModel('Players');
        $players = $this->Players->findPlayers(['name' => $name]);
        foreach ($players as $player) {
            $res[] = $player->renderArray();
        }
        $this->__renderJson([
            'size' => $players->count(),
            'results' => $res,
        ]);
    }

    /**
     * IDをもとに棋士情報を取得します。
     * 
     * @param int|null $id ID
     * @return object 棋士情報
     */
    public function player(int $id)
    {
        $this->loadModel('Players');
        $player = $this->Players->findById($id)->contain(['Countries', 'Ranks'])->first();
        $this->__renderJson($player->renderArray());
    }

    /**
     * IDをもとにタイトル情報を取得します。
     * 
     * @param int|null $id ID
     * @return object 棋士情報
     */
    public function titles($id = null)
    {
        $this->loadModel('Titles');

        if ($this->request->is('GET') && $id) {
            $title = $this->Titles->get($id);
            $this->__renderJson($title->renderArray());
            return;
        }

        if ($this->request->is('POST') || $this->request->is('PUT')) {
            $input = $this->request->getParsedBody();
            if ($id) {
                $input['titleId'] = $id;
            }
            $title = $this->Titles->fromArray($input);

            if (($errors = $this->Titles->validator()->errors($title->toArray()))) {
                // バリデーションの場合、フィールド => [定義 => メッセージ]となっている
                foreach ($errors as $expr) {
                    $out[] = array_shift($expr);
                }
                $this->response = $this->response->withStatus(400);
                $this->__renderJson([
                    'status' => $this->response->statusCode(),
                    'messages' => $out,
                ]);
                return;
            }

            $this->Titles->save($title);
            $this->__renderJson(['titleId' => $title->id]);
        }
    }

    /**
     * Go Newsの出力データを取得します。
     *
     * @return array タイトル一覧
     */
    public function news()
    {
        // 日本語情報を出力するかどうか
        $isJp = ($this->request->getQuery('jp') === 'true');

        // 管理者情報を出力するかどうか
        $admin = ($this->request->getQuery('admin') === 'true');

        // モデルのロード
        $this->loadModel('Titles');
        $titles = $this->Titles->findTitlesByCountry($this->request->getQuery());

        // 出力データを生成
        $json = $this->Titles->toArray($titles, $admin, $isJp);

        // パラメータがあればファイル作成
        if ($this->request->getQuery('make') === 'true') {
            if (!file_put_contents(env('JSON_OUTPUT_DIR').'news.json', json_encode($json))) {
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

        // ランキングデータ取得
        $json = $this->__rankings($country, $year, $rank, $isJp);

        // パラメータがあればファイル作成
        if ($this->request->getQuery('make') === 'true') {
            $dir = $json["countryNameAbbreviation"];
            $fileName = strtolower($json["countryName"]).$json["targetYear"];
            if (!file_put_contents(env('JSON_OUTPUT_DIR')."{$dir}/ranking/{$fileName}.json", json_encode($json))) {
                throw new MissingActionException(__("JSON出力失敗"), 500);
            }
        }
        $this->__renderJson($json);
    }

    /**
     * カテゴリを取得します。
     * 
     * @param int $countryId
     * @return array 段位別棋士一覧
     */
    public function ranks(int $countryId)
    {
        $this->loadModel('Players');
        $ranks = $this->Players->find()->contain(['Ranks'])->select([
            'Ranks.name', 'count' => 'count(*)'
        ])->where([
            'country_id' => $countryId,
            'is_retired' => false,
        ])->group('Ranks.name')->orderDesc('Ranks.rank_numeric')->all();
        $this->__renderJson($ranks->toArray());
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
    private function __rankings(string $countryName, int $year, int $rank, bool $isJp) : array
    {
        // モデルのロード
        $this->loadModel('Players');
        $this->loadModel('Countries');
        $this->loadModel('TitleScoreDetails');

        // 集計対象国の取得
        $country = $this->Countries->findByName($countryName)->first();

        // ランキングデータの取得
        $models = $this->Players->findRanking($country, $year, $rank);

        // 最終更新日の取得
        $lastUpdate = $this->TitleScoreDetails->findRecent($country, $year);

        // JSON生成
        return [
            'lastUpdate' => $lastUpdate,
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