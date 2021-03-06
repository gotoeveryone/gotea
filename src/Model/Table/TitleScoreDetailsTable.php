<?php
declare(strict_types=1);

namespace Gotea\Model\Table;

use Cake\I18n\Date;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Gotea\Model\Entity\Country;
use Gotea\Model\Query\RankingQuery;

/**
 * TitleScoreDetails Model
 *
 * @property \Cake\ORM\Association\BelongsTo $TitleScores
 * @property \Cake\ORM\Association\BelongsTo $Players
 * @method \Gotea\Model\Entity\TitleScoreDetail get($primaryKey, $options = [])
 * @method \Gotea\Model\Entity\TitleScoreDetail newEntity($data = null, array $options = [])
 * @method \Gotea\Model\Entity\TitleScoreDetail[] newEntities(array $data, array $options = [])
 * @method \Gotea\Model\Entity\TitleScoreDetail|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Gotea\Model\Entity\TitleScoreDetail patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Gotea\Model\Entity\TitleScoreDetail[] patchEntities($entities, array $data, array $options = [])
 * @method \Gotea\Model\Entity\TitleScoreDetail findOrCreate($search, callable $callback = null, $options = [])
 */
class TitleScoreDetailsTable extends AppTable
{
    /**
     * @inheritDoc
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->belongsTo('TitleScores');
        $this->belongsTo('Players')
            ->setJoinType(Query::JOIN_TYPE_INNER);
    }

    /**
     * @inheritDoc
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence([
                'title_score_id', 'player_id', 'division',
            ])
            ->integer('title_score_id')
            ->integer('player_id');

        return $validator;
    }

    /**
     * @inheritDoc
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['title_score_id'], 'TitleScores'));
        $rules->add($rules->existsIn(['player_id'], 'Players'));

        return $rules;
    }

    /**
     * @inheritDoc
     */
    public function query(): Query
    {
        return new RankingQuery($this->getConnection(), $this);
    }

    /**
     * 成績情報のファインダーメソッド。
     *
     * @param \Cake\ORM\Query $query 生成クエリ
     * @return \Cake\ORM\Query
     */
    public function findScores(Query $query)
    {
        return $query->contain('TitleScores')->select([
            'player_id',
            'target_year' => 'year(started)',
            'win_point' => $query->func()->count("(division = '勝' and is_official = 1) or null"),
            'lose_point' => $query->func()->count("(division = '敗' and is_official = 1) or null"),
            'draw_point' => $query->func()->count("(division = '分' and is_official = 1) or null"),
            'win_point_world' => $query->func()->count(
                "(division = '勝' and is_official = 1 and is_world = 1) or null"
            ),
            'lose_point_world' => $query->func()->count(
                "(division = '敗' and is_official = 1 and is_world = 1) or null"
            ),
            'draw_point_world' => $query->func()->count(
                "(division = '分' and is_official = 1 and is_world = 1) or null"
            ),
            'win_point_all' => $query->func()->count("division = '勝' or null"),
            'lose_point_all' => $query->func()->count("division = '敗' or null"),
            'draw_point_all' => $query->func()->count("division = '分' or null"),
        ])->group([
            'player_id', 'target_year',
        ]);
    }

    /**
     * 対象年・対象棋士の成績を取得します。
     *
     * @param int $playerId 棋士ID
     * @param int $targetYear 年
     * @return \Gotea\Model\Entity\TitleScoreDetail|null
     */
    public function findByPlayerAtYear(int $playerId, int $targetYear)
    {
        return $this->findScores($this->query())->where([
            'player_id' => $playerId,
            'year(started)' => $targetYear,
        ])->first();
    }

    /**
     * ランキング集計データを取得します。
     *
     * @param \Gotea\Model\Entity\Country $country 所属国
     * @param int $limit 取得順位の上限
     * @param \Cake\I18n\Date $started 対局日FROM
     * @param \Cake\I18n\Date $ended 対局日TO
     * @return \Cake\ORM\Query 生成されたクエリ
     */
    public function findRanking(Country $country, int $limit, Date $started, Date $ended)
    {
        // 旧方式
        if ($this->isOldRanking($started->year)) {
            $playerScores = TableRegistry::getTableLocator()->get('PlayerScores');

            return $playerScores->findRanking($country, $started->year, $limit);
        }

        $query = $this->findScores($this->query())
            ->contain([
                'Players', 'Players.Countries', 'Players.Ranks',
                'Players.PlayerRanks' => function ($q) use ($ended) {
                    // 抽出期間TOよりも前の段位を取得
                    return $q->where(['PlayerRanks.promoted <=' => $ended])
                        ->orderDesc('PlayerRanks.promoted');
                },
                'Players.PlayerRanks.Ranks' => function ($q) {
                    // 昇段情報が不足しているケースがあるため、初段は含めない
                    return $q->where(['Ranks.rank_numeric !=' => 1]);
                },
            ])
            ->select($this->Players)
            ->select($this->Players->Countries)
            ->select($this->Players->Ranks)
            ->where([
                'TitleScores.started >= ' => $started,
                'TitleScores.ended <= ' => $ended,
            ]);

        $sub = $this->findScores($this->query());
        $sub->select(['win_point' => $sub->func()->count("(division = '勝' and is_official = 1) or null")], true)
            ->where([
                'TitleScores.started >= ' => $started,
                'TitleScores.ended <= ' => $ended,
            ])
            ->group(['player_id'], true)
            ->having(['win_point >' => 0])
            ->orderDesc('win_point')->limit(1)->offset($limit - 1);

        if (!$country->isWorlds()) {
            $query->where(['Countries.id' => $country->id]);
            $sub->innerJoinWith('Players')->innerJoinWith('Players.Countries')
                ->where(['Countries.id' => $country->id]);
        } else {
            $query->where(['TitleScores.is_world' => true]);
            $sub->where(['TitleScores.is_world' => true]);
        }

        return $query
            ->having(['win_point >= ' => $query->func()->coalesce([$sub, 1])])
            ->orderDesc('win_point')
            ->order(['lose_point'])
            ->orderDesc('Ranks.rank_numeric')
            ->order(['Players.joined']);
    }

    /**
     * 最新データの対局日を取得します。
     *
     * @param \Gotea\Model\Entity\Country $country 所属国
     * @param \Cake\I18n\Date $started 対局日FROM
     * @param \Cake\I18n\Date $ended 対局日TO
     * @return string|null
     */
    public function findRecent(Country $country, Date $started, Date $ended)
    {
        // 旧方式
        if ($this->isOldRanking($started->year)) {
            $points = TableRegistry::getTableLocator()->get('UpdatedPoints');

            return $points->findRecent($country, $started->year);
        }

        // 対局棋士の所属国が該当する・もしくは国際棋戦の最新であるデータの対局日を返却
        $query = $this->find()->contain('TitleScores')
            ->where(['TitleScores.started >= ' => $started])
            ->where(['TitleScores.ended <= ' => $ended]);

        if (!$country->isWorlds()) {
            $query->contain(['Players', 'Players.Countries'])
                ->where(['Countries.id' => $country->id]);
        } else {
            $query->where(['TitleScores.is_world' => true]);
        }

        return $query->select([
            'max' => $query->func()->max('ended'),
        ], true)->first()->max;
    }

    /**
     * ランキングデータの取得方法を判定します。
     *
     * @param int $targetYear 対象年度
     * @return bool
     */
    private function isOldRanking(int $targetYear): bool
    {
        return $targetYear < 2017;
    }
}
