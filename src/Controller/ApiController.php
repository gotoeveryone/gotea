<?php

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Filesystem\File;
use Cake\Log\Log;

/**
 * APIコントローラ
 *
 * @property \App\Controller\Component\JsonComponent $Json
 */
class ApiController extends Controller
{
    /**
     * {@inheritDoc}
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
     * 所属国一覧を取得します。
     *
     * @return Response 所属国一覧
     */
    public function countries()
    {
        $this->loadModel('Countries');
        $query = $this->Countries->find()->where(['code is not' => null]);
        if ($this->request->getQuery('has_title')) {
            $query->where(['has_title' => true]);
        }
        $countries = $query->select(['id', 'code', 'name', 'name_english'])->all();
        return $this->__renderJson($countries->toArray());
    }

    /**
     * 名前をもとに棋士情報を取得します。
     *
     * @param int|null $id ID
     * @return Response 棋士情報一覧
     */
    public function players($id = null)
    {
        $this->loadModel('Players');

        // IDの指定があれば1件取得して返却
        if ($id && is_numeric($id)) {
            $player = $this->Players->findById($id)->contain(['Countries', 'Ranks'])->first();
            return $this->__renderJson($player->renderArray());
        }

        if (!($name = $this->request->getData('name'))) {
            return $this->__renderJson();
        }

        $players = $this->Players->findPlayersQuery($this->request)->all();
        return $this->__renderJson([
            'size' => $players->count(),
            'results' => $players->map(function($item, $key) {
                return $item->renderArray();
            }),
        ]);
    }

    /**
     * IDをもとにタイトル情報を取得します。
     *
     * @param int|null $id ID
     * @return Response タイトル情報
     */
    public function titles($id = null)
    {
        $this->loadModel('Titles');

        if ($this->request->isGet()) {
            if (!$id) {
                return $this->__renderJson();
            }
            $title = $this->Titles->get($id);
            return $this->__renderJson($title->renderArray());
        }

        if ($this->request->isPost() || $this->request->isPut()) {
            $input = $this->request->getParsedBody();
            if ($id) {
                $input['titleId'] = $id;
            }
            $title = $this->Titles->fromArray($input);

            if (($errors = $this->Titles->getValidator()->errors($title->toArray()))) {
                // バリデーションの場合、フィールド => [定義 => メッセージ]となっている
                foreach ($errors as $expr) {
                    $out[] = array_shift($expr);
                }
                return $this->__renderError(400, $out);
            }

            $this->Titles->save($title);
            return $this->__renderJson(['titleId' => $title->id]);
        }

        return $this->__renderError(405, 'Method Not Allowed');
    }

    /**
     * Go Newsの出力データを取得します。
     *
     * @return Response タイトル一覧
     */
    public function news()
    {
        // 日本語情報を出力するかどうか
        $withJa = ($this->request->getQuery('withJa') === '1');

        // 管理者情報を出力するかどうか
        $admin = ($this->request->getQuery('admin') === '1');

        // モデルのロード
        $this->loadModel('Titles');
        $titles = $this->Titles->findTitlesByCountry($this->request->getQuery());

        // 出力データを生成
        $json = $this->Titles->toArray($titles, $admin, $withJa);

        // パラメータがあればファイル作成
        if ($this->request->getQuery('make') === '1') {
            $file = new File(env('JSON_OUTPUT_DIR').'news.json');
            Log::info('JSONファイル出力：'.$file->path);

            if (!$file->write(json_encode($json))) {
                return $this->__renderError(500, 'JSON出力失敗');
            }
        }

        return $this->__renderJson($json);
    }

    /**
     * ランキングを取得します。
     *
     * @param string $country
     * @param string $year
     * @param string $rank
     * @return Response ランキング
     */
    public function rankings($country = null, $year = null, $rank = null)
    {
        // 日本語情報を出力するかどうか
        $withJa = ($this->request->getQuery('withJa') === '1');

        // ランキングデータ取得
        $json = $this->__rankings($country, $year, $rank, $withJa);

        if (!$json) {
            return $this->__renderJson($json);
        }

        // パラメータがあればファイル作成
        if ($this->request->getQuery('make') === '1') {
            $dir = $json["countryNameAbbreviation"];
            $fileName = strtolower($json["countryName"]).$json["targetYear"];
            $file = new File(env('JSON_OUTPUT_DIR')."ranking/${country}/{$fileName}.json");
            Log::info('JSONファイル出力：'.$file->path);

            if (!$file->write(json_encode($json))) {
                return $this->__renderError(500, 'JSON出力失敗');
            }
        }
        return $this->__renderJson($json);
    }

    /**
     * カテゴリを取得します。
     *
     * @param int $countryId
     * @return Response 段位別棋士一覧
     */
    public function ranks(int $countryId)
    {
        $this->loadModel('Players');
        $ranks = $this->Players->findRanksCount($countryId);
        return $this->__renderJson($ranks->toArray());
    }

    /**
     * ランキングを取得します。
     *
     * @param string $countryCode
     * @param int $year
     * @param int $rank
     * @param bool $withJa
     * @return array
     */
    private function __rankings(string $countryCode, int $year, int $rank, bool $withJa) : array
    {
        // モデルのロード
        $this->loadModel('Players');
        $this->loadModel('Countries');
        $this->loadModel('TitleScoreDetails');

        // 集計対象国の取得
        $country = $this->Countries->findByCode($countryCode)->first();

        // ランキングデータの取得
        $ranking = $this->Players->findRanking($country, $year, $rank, $withJa);

        // 最終更新日の取得
        $lastUpdate = $this->TitleScoreDetails->findRecent($country, $year);

        // JSON生成
        return [
            'lastUpdate' => $lastUpdate,
            'targetYear' => $year,
            'countryName' => $country->name_english,
            'countryNameAbbreviation' => $country->code,
            'ranking' => $ranking->toArray(),
        ];
    }

    /**
     * エラーレスポンスを生成します。
     *
     * @param int $code
     * @param string $message
     * @return Response
     */
    private function __renderError($code = 500, $message = 'Internal Error')
    {
        $this->response = $this->response->withStatus($code);
        return $this->__renderJson([
            'code' => $code,
            'message' => $message,
        ]);
    }

    /**
     * JSONレスポンスを返却します。
     *
     * @param array $json
     * @return Response
     */
    private function __renderJson($json = [])
    {
        return $this->set([
            'response' => $json,
            '_serialize' => true,
        ])->render();
    }
}
