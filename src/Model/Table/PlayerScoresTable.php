<?php
declare(strict_types=1);

namespace Gotea\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\ResultSet;
use Gotea\Model\Entity\Country;
use Gotea\Model\Query\RankingQuery;

/**
 * 棋士成績
 *
 * @property \Cake\ORM\Association\BelongsTo $Players
 * @property \Cake\ORM\Association\BelongsTo $Ranks
 */
class PlayerScoresTable extends AppTable
{
    /**
     * @inheritDoc
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->belongsTo('Players')
            ->setJoinType('INNER');
        $this->belongsTo('Ranks')
            ->setJoinType('INNER');
    }

    /**
     * @inheritDoc
     */
    public function selectQuery(): SelectQuery
    {
        return new RankingQuery($this);
    }

    /**
     * 対象棋士の成績一覧を取得します。
     *
     * @param int $playerId 棋士ID
     * @return \Cake\ORM\ResultSet
     */
    public function findDescYears(int $playerId): ResultSet
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
     * @param string $type 種類（何順で表示するか）
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findRanking(Country $country, int $targetYear, int $offset, string $type = 'point'): SelectQuery
    {
        $suffix = ($country->has_title ? '' : '_world');
        $winColumn = "PlayerScores.win_point{$suffix}";
        $loseColumn = "PlayerScores.lose_point{$suffix}";
        $drawColumn = "PlayerScores.draw_point{$suffix}";
        $isPercent = $type === 'percent';

        // サブクエリ
        $subQuery = $this->find();
        $subQuery->select($isPercent ? [
                    'win_percent' => $subQuery->func()->round([
                        "{$winColumn} / ({$winColumn} + {$loseColumn})" => 'identifier', 2,
                    ]),
                ] : $winColumn)
                ->innerJoinWith('Players')->innerJoinWith('Players.Countries')
                ->where([
                    'PlayerScores.target_year' => $targetYear,
                ]);

        if ($isPercent) {
            $subQuery->orderDesc('win_percent');
        }

        $subQuery->orderDesc($winColumn)
            ->order($loseColumn)
            ->limit(1)->offset($offset - 1);

        if ($country->has_title) {
            $subQuery->where(['Countries.id' => $country->id]);
        }

        $query = $this->find()
            ->select([
                'player_id',
                'target_year',
                'win_point' => $winColumn,
                'lose_point' => $loseColumn,
                'draw_point' => $drawColumn,
                'win_percent' => "{$winColumn} / ({$winColumn} + {$loseColumn})",
            ])
            ->select($this->Ranks)
            ->select($this->Players)
            ->select($this->Players->Countries)
            ->contain([
                'Ranks', 'Players', 'Players.Countries',
            ])->where([
                'PlayerScores.target_year' => $targetYear,
            ]);

        if ($isPercent) {
            $query->having(function ($exp, $q) use ($subQuery) {
                return $exp->gte('win_percent', $subQuery);
            })->orderDesc('win_percent');
        } else {
            $query->where(function ($exp, $q) use ($winColumn, $subQuery) {
                return $exp->gte($winColumn, $subQuery);
            });
        }

        $query->orderDesc($winColumn)
            ->order($loseColumn)
            ->orderDesc('Ranks.rank_numeric')
            ->order('Players.joined');

        if (!$country->isWorlds()) {
            $query->where(['country_id' => $country->id]);
        }

        return $query;
    }
}
