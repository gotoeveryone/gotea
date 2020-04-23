<?php
declare(strict_types=1);

namespace Gotea\Controller\Api;

use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Http\Response;
use Cake\I18n\Date;
use Cake\Log\Log;
use Cake\Utility\Hash;

/**
 * API・棋士コントローラ
 *
 * @property \Gotea\Model\Table\PlayersTable $Players
 * @property \Gotea\Model\Table\CountriesTable $Countries
 * @property \Gotea\Model\Table\TitleScoreDetailsTable $TitleScoreDetails
 */
class PlayersController extends ApiController
{
    /**
     * 棋士を検索します。
     *
     * @return \Cake\Http\Response 棋士情報
     */
    public function search(): ?Response
    {
        // limit、offsetを指定して取得
        $request = $this->getRequest();
        $query = $this->Players->findPlayers($request->getParsedBody());
        $players = $query->limit($request->getData('limit', 100))
            ->offset($request->getData('offset', 0));

        return $this->renderJson([
            'count' => $query->count(),
            'results' => $players->map(function ($item) {
                return $item->toArray();
            }),
        ]);
    }

    /**
     * 所属国に該当する段位と棋士数を取得します。
     *
     * @param string $countryId 所属国ID
     * @return \Cake\Http\Response 段位別棋士一覧
     */
    public function searchRanks(string $countryId): ?Response
    {
        $ranks = $this->Players->findRanksCount((int)$countryId);

        return $this->renderJson($ranks);
    }

    /**
     * ランキングを取得します。
     *
     * @param string $country 所属国
     * @param string $year 対象年度
     * @param string $limit 取得上限値
     * @return \Cake\Http\Response ランキング
     */
    public function searchRanking(string $country, string $year, string $limit): ?Response
    {
        $request = $this->getRequest();
        $from = $request->getQuery('from');
        $to = $request->getQuery('to');

        // ランキングデータ取得
        $json = $this->getRankingData([
            'country' => $country,
            'year' => (int)$year,
            'limit' => (int)$limit,
            'from' => $from,
            'to' => $to,
            'ja' => true,
        ]);

        if (!$json) {
            return $this->renderError(404);
        }

        return $this->renderJson($json);
    }

    /**
     * ランキングJSONデータを生成します。
     *
     * @param string $country 所属国
     * @param string $year 対象年度
     * @param string $limit 取得上限値
     * @return \Cake\Http\Response ランキング
     */
    public function createRanking(string $country, string $year, string $limit): ?Response
    {
        $request = $this->getRequest();
        $from = $request->getData('from');
        $to = $request->getData('to');

        // ランキングデータ取得
        $json = $this->getRankingData([
            'country' => $country,
            'year' => (int)$year,
            'limit' => (int)$limit,
            'from' => $from,
            'to' => $to,
        ]);

        if (!$json) {
            return $this->renderError(404);
        }

        // フォルダ作成
        $dir = $json['countryCode'];
        $folder = new Folder(Configure::read('App.jsonDir') . 'ranking' . DS . $dir, true, 0755);
        if ($folder->errors()) {
            Log::error($folder->errors());

            return $this->renderError(500, 'JSON出力失敗');
        }

        // ファイル作成
        $fileName = strtolower($json['countryName']) . $json['year'];
        $file = new File($folder->pwd() . DS . "{$fileName}.json");
        Log::info("JSONファイル出力：{$file->path}");

        if (!$file->write(json_encode($json))) {
            return $this->renderError(500, 'JSON出力失敗');
        }

        return $this->renderJson($json);
    }

    /**
     * ランキングを取得します。
     *
     * @param array $params パラメータ
     * @return array ランキングデータ
     */
    private function getRankingData(array $params): array
    {
        // モデルのロード
        $this->loadModel('Countries');
        $this->loadModel('TitleScoreDetails');

        // 集計対象国の取得
        $countryCode = Hash::get($params, 'country');
        $country = $this->Countries->findByCode($countryCode)->first();
        if (!$country) {
            return [];
        }

        // パラメータ取得
        $year = Hash::get($params, 'year');
        $limit = Hash::get($params, 'limit');
        $from = Hash::get($params, 'from');
        $to = Hash::get($params, 'to');
        $withJa = Hash::get($params, 'ja', false);

        // 開始日・終了日の補填
        $from = $from ? Date::parse($from) : Date::createFromDate($year, 1, 1);
        $to = $to ? Date::parse($to) : Date::createFromDate($year, 12, 31);

        // ランキングデータの取得
        $ranking = $this->TitleScoreDetails
            ->findRanking($country, $limit, $from, $to)
            ->mapRanking($country->isWorlds(), $withJa);

        // 最終更新日の取得
        $lastUpdate = $this->TitleScoreDetails->findRecent($country, $from, $to);

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
}
