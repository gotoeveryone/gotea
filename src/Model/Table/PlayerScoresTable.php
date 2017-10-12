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
     * @param int $id
     * @return \Cake\ORM\ResultSet
     */
    public function findDescYears(int $id)
    {
        return $this->findByPlayerId($id)
            ->contain(['Ranks'])->orderDesc('target_year')->all();
    }

    /**
     * ランキング集計データを取得します。
     * ※2016年以前の集計です。
     *
     * @param Country $country
     * @param int $targetYear
     * @param int $offset
     * @return \Cake\ORM\Query
     */
    public function findRanking(Country $country, int $targetYear, int $offset)
    {
        $suffix = ($country->has_title ? '' : '_world');

        // サブクエリ
        $subQuery = $this->find()
                ->select('PlayerScores.win_point'.$suffix)
                ->innerJoinWith('Players')->innerJoinWith('Players.Countries')
                ->where([
                    'PlayerScores.target_year' => $targetYear,
                ])->orderDesc('PlayerScores.win_point'.$suffix)->order('PlayerScores.lose_point'.$suffix)
                ->limit(1)->offset($offset - 1);

        if ($country->has_title) {
            $subQuery->where(['Countries.id' => $country->id]);
        }

        $query = $this->find()
            ->contain([
                'Ranks', 'Players', 'Players.Countries',
            ])->where(function ($exp, $q) use ($subQuery, $suffix) {
                return $exp->gte('PlayerScores.win_point'.$suffix, $subQuery);
            })->where([
                'PlayerScores.target_year' => $targetYear,
            ])->orderDesc('PlayerScores.win_point'.$suffix)
            ->order('PlayerScores.lose_point'.$suffix)
            ->orderDesc('Ranks.rank_numeric')
            ->order('Players.joined');

        if (!$country->isWorlds()) {
            $query->where(['country_id' => $country->id]);
        }

        return $query;
    }
}
