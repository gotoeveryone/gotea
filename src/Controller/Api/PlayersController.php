<?php
declare(strict_types=1);

namespace Gotea\Controller\Api;

use Cake\Http\Response;
use Cake\Utility\Hash;
use Gotea\Collection\Iterator\RankingIterator;
use Gotea\Collection\Iterator\RankIterator;
use Gotea\Utility\FileBuilder;

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
     * @inheritDoc
     */
    protected array $publicActions = ['search', 'searchRanks', 'searchRanking'];

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
            'results' => $players->all()->map(function ($item) {
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
    public function searchRanks(int $countryId): ?Response
    {
        $ranks = $this->Players->findRanksCount($countryId)->all();

        return $this->renderJson($ranks->map(new RankIterator()));
    }

    /**
     * ランキングを取得します。
     *
     * @param string $country 所属国
     * @param int $year 対象年度
     * @param int $limit 取得上限値
     * @return \Cake\Http\Response ランキング
     */
    public function searchRanking(string $country, int $year, int $limit): ?Response
    {
        $request = $this->getRequest();
        $from = $request->getQuery('from');
        $to = $request->getQuery('to');
        $type = $request->getQuery('type');

        // ランキングデータ取得
        $json = $this->getRankingData([
            'country' => $country,
            'year' => $year,
            'limit' => $limit,
            'from' => $from,
            'to' => $to,
            'ja' => true,
            'type' => $type,
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
     * @param int $year 対象年度
     * @param int $limit 取得上限値
     * @return \Cake\Http\Response ランキング
     */
    public function createRanking(string $country, int $year, int $limit): ?Response
    {
        $request = $this->getRequest();
        $from = $request->getData('from');
        $to = $request->getData('to');
        $type = $request->getData('type');

        // ランキングデータ取得
        $ranking = $this->getRankingData([
            'country' => $country,
            'year' => $year,
            'limit' => $limit,
            'from' => $from,
            'to' => $to,
            'type' => $type,
        ]);

        if (!$ranking) {
            return $this->renderError(404);
        }

        $filename = strtolower($ranking['countryName']) . $ranking['year'];
        $builder = FileBuilder::new();
        $builder->setParentDirPath('ranking', $ranking['countryCode']);
        if (!$builder->output($filename, $ranking)) {
            return $this->renderError(500, 'JSON出力失敗');
        }

        return $this->renderJson($ranking);
    }

    /**
     * ランキングを取得します。
     *
     * @param array $params パラメータ
     * @return array ランキングデータ
     */
    private function getRankingData(array $params): array
    {
        $data = $this->fetchTable('TitleScoreDetails')->findRankingData($params);
        if (!$data) {
            return [];
        }

        $players = $data['players']->mapRanking(
            $data['country']->isWorlds(),
            (bool)Hash::get($params, 'ja', false),
            $data['type'],
        );

        return [
            'countryCode' => $data['country']->code,
            'countryName' => $data['country']->name_english,
            'year' => $data['year'],
            'lastUpdate' => $data['lastUpdate'],
            'count' => iterator_count($players),
            'ranking' => $players->map(new RankingIterator()),
        ];
    }
}
