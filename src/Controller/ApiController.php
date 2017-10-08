<?php

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Filesystem\File;
use Cake\I18n\FrozenDate;
use Cake\Log\Log;
use App\Collection\Iterator\NewsIterator;
use App\Collection\Iterator\TitlesIterator;

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

        // ヘッダのユーザIDをセッションに乗せる
        $this->request->session()->write(
            'Api-UserId', $this->request->getHeaderLine('X-Access-User'));

        // 当アクションのレスポンスはすべてJSON形式
        $this->RequestHandler->renderAs($this, 'json');
        $this->response->type('application/json');
    }

    /**
     * 管理対象年を取得します。
     *
     * @return \Cake\Http\Response 年一覧
     */
    public function years()
    {
        $nowYear = FrozenDate::now()->year;
        $years = [];
        for ($i = $nowYear; $i >= 2013; $i--) {
            $years[] = $i;
        }
        return $this->__renderJson($years);
    }

    /**
     * 所属国一覧を取得します。
     *
     * @return \Cake\Http\Response 所属国一覧
     */
    public function countries()
    {
        $hasTitle = ($this->request->getQuery('has_title') === '1');

        $this->loadModel('Countries');
        $countries = $this->Countries->findAllHasCode($hasTitle);

        return $this->__renderJson($countries);
    }

    /**
     * カテゴリを取得します。
     *
     * @param int $countryId
     * @return \Cake\Http\Response 段位別棋士一覧
     */
    public function ranks(int $countryId)
    {
        $this->loadModel('Players');
        $ranks = $this->Players->findRanksCount($countryId);

        return $this->__renderJson($ranks);
    }

    /**
     * 棋士情報を取得します。
     *
     * @param int|null $id ID
     * @return \Cake\Http\Response 棋士情報
     */
    public function players($id = null)
    {
        $this->loadModel('Players');

        // IDの指定があれば1件取得して返却
        if ($id && is_numeric($id)) {
            $player = $this->Players->get($id, [
                'contain' => ['Countries', 'Ranks'],
            ]);
            return $this->__renderJson($player->toArray());
        }

        // 以降は検索処理のためPOST以外は許可しない
        if (!$this->request->isPost()) {
            $message = $this->request->getMethod().'リクエストは許可されていません。';
            return $this->__renderError(405, $message);
        }

        // limit、offsetを指定して取得
        $query = $this->Players->findPlayers($this->request);
        $players = $query->limit($this->request->getData('limit', 100))
            ->offset($this->request->getData('offset', 0));

        return $this->__renderJson([
            'count' => $query->count(),
            'results' => $players->map(function($item, $key) {
                return $item->toArray();
            }),
        ]);
    }

    /**
     * タイトル情報を取得・更新します。
     *
     * @param int|null $id ID
     * @return \Cake\Http\Response タイトル情報
     */
    public function titles($id = null)
    {
        $this->loadModel('Titles');

        if ($this->request->isGet()) {
            // IDの指定があれば1件取得して返却
            if ($id && is_numeric($id)) {
                $title = $this->Titles->get($id);
                return $this->__renderJson($title->toArray());
            }

            // 検索
            $titles = $this->Titles->findTitles($this->request->getQuery());
            return $this->__renderJson($titles->map(new TitlesIterator));
        }

        if (!$this->request->isPost() && !$this->request->isPut()) {
            $message = $this->request->getMethod().'リクエストは許可されていません。';
            return $this->__renderError(405, $message);
        }

        $input = $this->request->getParsedBody();
        $title = $this->Titles->createEntity($input);

        if (!$this->Titles->save($title)) {
            // バリデーションの場合、フィールド => [定義 => メッセージ]となっている
            $errors = $title->errors();
            foreach ($errors as $expr) {
                $out[] = array_shift($expr);
            }
            return $this->__renderError(400, $out);
        }

        return $this->__renderJson($title->toArray());
    }

    /**
     * Go Newsの出力データを取得します。
     *
     * @return \Cake\Http\Response Go News出力データ
     */
    public function createNews()
    {
        // POSTのみ許可
        $this->request->allowMethod(['post']);

        $this->loadModel('Titles');
        $titles = $this->Titles->findTitles($this->request->getQuery())
            ->map(new NewsIterator);

        // ファイル作成
        $file = new File(env('JSON_OUTPUT_DIR').'news.json');
        Log::info('JSONファイル出力：'.$file->path);

        if (!$file->write(json_encode($titles))) {
            return $this->__renderError(500, 'JSON出力失敗');
        }

        return $this->__renderJson();
    }

    /**
     * ランキングを取得します。
     *
     * @param string $country
     * @param int $year
     * @param int $offset
     * @return \Cake\Http\Response ランキング
     */
    public function rankings(string $country, int $year, int $offset)
    {
        // 日本語情報を出力するかどうか
        $withJa = ($this->request->getQuery('withJa') === '1');

        // ランキングデータ取得
        $json = $this->__rankings($country, $year, $offset, $withJa);

        if (!$json) {
            return $this->__renderJson($json);
        }

        // POSTの場合はファイル作成
        if ($this->request->isPost()) {
            $dir = $json['countryCode'];
            $fileName = strtolower($json['countryName']).$json['year'];
            $file = new File(env('JSON_OUTPUT_DIR')."ranking/${dir}/{$fileName}.json");
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
     * @param string $countryCode
     * @param int $year
     * @param int $offset
     * @param bool $withJa
     * @return array
     */
    private function __rankings(string $countryCode, int $year, int $offset, bool $withJa) : array
    {
        // モデルのロード
        $this->loadModel('Countries');
        $this->loadModel('TitleScoreDetails');

        // 集計対象国の取得
        $country = $this->Countries->findByCode($countryCode)->first();

        // ランキングデータの取得
        $ranking = $this->TitleScoreDetails->findRanking($country, $year, $offset)
            ->mapRanking($country->isWorlds(), $withJa);

        // 最終更新日の取得
        $lastUpdate = $this->TitleScoreDetails->findRecent($country, $year);

        // JSON生成
        return [
            'countryCode' => $country->code,
            'countryName' => $country->name_english,
            'year' => $year,
            'lastUpdate' => $lastUpdate,
            'count' => iterator_count($ranking),
            'ranking' => $ranking,
        ];
    }

    /**
     * エラーレスポンスを生成します。
     *
     * @param int $code
     * @param string $message
     * @return \Cake\Http\Response
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
     * @return \Cake\Http\Response
     */
    private function __renderJson($json = [])
    {
        return $this->set([
            'response' => $json,
            '_serialize' => true,
        ])->render();
    }
}
