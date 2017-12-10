<?php

namespace Gotea\Model\Table;

use Gotea\Model\Entity\Country;
use Gotea\Model\Query\RankingQuery;

/**
 * 棋士成績
 */
class PlayerScoresTable extends AppTable
{
    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->belongsTo('Players')
            ->setJoinType('INNER');
        $this->belongsTo('Ranks')
            ->setJoinType('INNER');
    }

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        return new RankingQuery($this->getConnection(), $this);
    }

    /**
     * 対象棋士の成績一覧を取得します。
     *
     * @param int $playerId 棋士ID
     * @return \Cake\ORM\ResultSet
     */
    public function findDescYears(int $playerId)
    {
        return $this->findByPlayerId($playerId)
            ->contain(['Ranks'])->orderDesc('target_year')->all();
    }

    /**
     * ランキング集計データを取得します。
     * ※2016年以前の集計です。
     *
     * @param \Gotea\Model\Entity\Country $country 所属国
     * @param int $targetYear 対象年度
     * @param int $offset 取得開始行
     * @return \Cake\ORM\Query
     */
    public function findRanking(Country $country, int $targetYear, int $offset)
    {
        $suffix = ($country->has_title ? '' : '_world');
        $winColumn = "PlayerScores.win_point${suffix}";
        $loseColumn = "PlayerScores.lose_point${suffix}";

        // サブクエリ
        $subQuery = $this->find()
                ->select($winColumn)
                ->innerJoinWith('Players')->innerJoinWith('Players.Countries')
                ->where([
                    'PlayerScores.target_year' => $targetYear,
                ])->orderDesc($winColumn)->order($loseColumn)
                ->limit(1)->offset($offset - 1);

        if ($country->has_title) {
            $subQuery->where(['Countries.id' => $country->id]);
        }

        $query = $this->find()
            ->contain([
                'Ranks', 'Players', 'Players.Countries',
            ])->where(function ($exp, $q) use ($winColumn, $subQuery) {
                return $exp->gte($winColumn, $subQuery);
            })->where([
                'PlayerScores.target_year' => $targetYear,
            ])->orderDesc($winColumn)
            ->order($loseColumn)
            ->orderDesc('Ranks.rank_numeric')
            ->order('Players.joined');

        if (!$country->isWorlds()) {
            $query->where(['country_id' => $country->id]);
        }

        return $query;
    }
}
