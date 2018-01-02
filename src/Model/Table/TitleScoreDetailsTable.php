<?php

namespace Gotea\Model\Table;

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
 *
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
     * {@inheritdoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('title_score_details');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->belongsTo('TitleScores');
        $this->belongsTo('Players')
            ->setJoinType(Query::JOIN_TYPE_INNER);
        $this->belongsTo('Ranks')
            ->setJoinType(Query::JOIN_TYPE_INNER);
    }

    /**
     * {@inheritdoc}
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence([
                'title_score_id', 'player_id', 'rank_id', 'division',
            ])
            ->integer('title_score_id')
            ->integer('player_id')
            ->integer('rank_id');

        return $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['title_score_id'], 'TitleScores'));
        $rules->add($rules->existsIn(['player_id'], 'Players'));

        return $rules;
    }

    /**
     * {@inheritDoc}
     */
    public function query()
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
            'win_point' => $query->func()->count("division = '勝' or null"),
            'lose_point' => $query->func()->count("division = '敗' or null"),
            'draw_point' => $query->func()->count("division = '分' or null"),
            'win_point_world' => $query->func()->count("division = '勝' and is_world = 1 or null"),
            'lose_point_world' => $query->func()->count("division = '敗' and is_world = 1 or null"),
            'draw_point_world' => $query->func()->count("division = '分' and is_world = 1 or null"),
        ])->group([
            'player_id', 'target_year',
        ]);
    }

    /**
     * ランキング集計データを取得します。
     *
     * @param \Gotea\Model\Entity\Country $country 所属国
     * @param int $targetYear 対象年度
     * @param int $offset 取得開始行
     * @param string|null $started 対局日FROM
     * @param string|null $ended 対局日TO
     * @return \Cake\ORM\Query 生成されたクエリ
     */
    public function findRanking(Country $country, int $targetYear, int $offset, $started = null, $ended = null)
    {
        // 旧方式
        if ($this->_isOldRanking($targetYear)) {
            $playerScores = TableRegistry::get('PlayerScores');

            return $playerScores->findRanking($country, $targetYear, $offset);
        }

        $query = $this->findScores($this->query())
            ->contain(['Players', 'Players.Countries', 'Players.Ranks'])
            ->select($this->Players)
            ->select($this->Players->Countries)
            ->select($this->Players->Ranks);

        $sub = $this->findScores($this->query());
        $sub->select(['win_point' => $sub->func()->count("division = '勝' or null")], true)
            ->group(['player_id', 'YEAR(started)'], true)
            ->orderDesc('win_point')->limit(1)->offset($offset - 1);

        if (!$country->isWorlds()) {
            $query->where(['Countries.id' => $country->id]);
            $sub->innerJoinWith('Players')->innerJoinWith('Players.Countries')
                ->where(['Countries.id' => $country->id]);
        } else {
            $query->where(['TitleScores.is_world' => true]);
            $sub->where(['TitleScores.is_world' => true]);
        }

        if ($started) {
            $query->where(['TitleScores.started >= ' => $started]);
            $sub->where(['TitleScores.started >= ' => $started]);
        }
        if ($ended) {
            $query->where(['TitleScores.ended <= ' => $ended]);
            $sub->where(['TitleScores.ended <= ' => $ended]);
        }

        return $query
            ->where(['YEAR(started)' => $targetYear])
            ->having(['win_point >= ' => $sub])
            ->orderDesc('win_point')
            ->order(['lose_point', 'Players.joined'])
            ->orderDesc('Ranks.rank_numeric');
    }

    /**
     * 最新データの対局日を取得します。
     *
     * @param \Gotea\Model\Entity\Country $country 所属国
     * @param int $targetYear 対象年度
     * @param string|null $started 対局日FROM
     * @param string|null $ended 対局日TO
     * @return string|null
     */
    public function findRecent(Country $country, int $targetYear, $started = null, $ended = null)
    {
        // 旧方式
        if ($this->_isOldRanking($targetYear)) {
            $points = TableRegistry::get('UpdatedPoints');

            return $points->findRecent($country, $targetYear);
        }

        // 対局棋士の所属国が該当する・もしくは国際棋戦の最新であるデータの対局日を返却
        $query = $this->find()->contain('TitleScores')
            ->where(['YEAR(TitleScores.ended)' => $targetYear]);

        if (!$country->isWorlds()) {
            $query->contain(['Players', 'Players.Countries'])
                ->where(['Countries.id' => $country->id]);
        } else {
            $query->where(['TitleScores.is_world' => true]);
        }

        if ($started) {
            $query->where(['TitleScores.started >= ' => $started]);
        }
        if ($ended) {
            $query->where(['TitleScores.ended <= ' => $ended]);
        }

        return $query->select([
            'max' => $query->func()->max('ended'),
        ], true)->first()->max;
    }
}
