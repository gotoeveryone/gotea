<?php

namespace Gotea\Controller\Api;

use Cake\Filesystem\File;
use Cake\I18n\FrozenDate;
use Cake\Log\Log;

/**
 * API・棋士コントローラ
 */
class PlayersController extends ApiController
{
    /**
     * 棋士を検索します。
     *
     * @return \Cake\Http\Response 棋士情報
     */
    public function search()
    {
        // limit、offsetを指定して取得
        $query = $this->Players->findPlayers($this->request);
        $players = $query->limit($this->request->getData('limit', 100))
            ->offset($this->request->getData('offset', 0));

        return $this->_renderJson([
            'count' => $query->count(),
            'results' => $players->map(function ($item, $key) {
                return $item->toArray();
            }),
        ]);
    }

    /**
     * 所属国に該当する段位と棋士数を取得します。
     *
     * @param int $countryId 所属国ID
     * @return \Cake\Http\Response 段位別棋士一覧
     */
    public function searchRanks(int $countryId)
    {
        $ranks = $this->Players->findRanksCount($countryId);

        return $this->_renderJson($ranks);
    }

    /**
     * ランキングを取得します。
     *
     * @param string $country 所属国
     * @param int $year 対象年度
     * @param int $offset 取得上限値
     * @return \Cake\Http\Response ランキング
     */
    public function searchRanking(string $country, int $year, int $offset)
    {
        // ランキングデータ取得
        $json = $this->__ranking($country, $year, $offset, true);

        if (!$json) {
            return $this->_renderJson($json);
        }

        return $this->_renderJson($json);
    }

    /**
     * ランキングJSONデータを生成します。
     *
     * @param string $country 所属国
     * @param int $year 対象年度
     * @param int $offset 取得上限値
     * @return \Cake\Http\Response ランキング
     */
    public function createRanking(string $country, int $year, int $offset)
    {
        // ランキングデータ取得
        $json = $this->__ranking($country, $year, $offset);

        if (!$json) {
            return $this->_renderJson($json);
        }

        // ファイル作成
        $dir = $json['countryCode'];
        $fileName = strtolower($json['countryName']) . $json['year'];
        $file = new File(env('JSON_OUTPUT_DIR') . "ranking/${dir}/{$fileName}.json");
        Log::info("JSONファイル出力：{$file->path}");

        if (!$file->write(json_encode($json))) {
            return $this->_renderError(500, 'JSON出力失敗');
        }

        return $this->_renderJson($json);
    }

    /**
     * ランキングを取得します。
     *
     * @param string $countryCode 所属国コード
     * @param int $year 対象年度
     * @param int $offset 取得上限値
     * @param bool $withJa 日本語情報を出力するか
     * @return array
     */
    private function __ranking(string $countryCode, int $year, int $offset, bool $withJa = false) : array
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
}
