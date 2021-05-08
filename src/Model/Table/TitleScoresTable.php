<?php
declare(strict_types=1);

namespace Gotea\Model\Table;

use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Utility\Hash;
use Cake\Validation\Validator;

/**
 * TitleScores Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Titles
 * @property \Cake\ORM\Association\BelongsTo $Countries
 * @property \Cake\ORM\Association\HasMany $TitleScoreDetails
 * @method \Gotea\Model\Entity\TitleScore get($primaryKey, $options = [])
 * @method \Gotea\Model\Entity\TitleScore newEntity($data = null, array $options = [])
 * @method \Gotea\Model\Entity\TitleScore[] newEntities(array $data, array $options = [])
 * @method \Gotea\Model\Entity\TitleScore|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Gotea\Model\Entity\TitleScore patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Gotea\Model\Entity\TitleScore[] patchEntities($entities, array $data, array $options = [])
 * @method \Gotea\Model\Entity\TitleScore findOrCreate($search, callable $callback = null, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TitleScoresTable extends AppTable
{
    /**
     * @inheritDoc
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->belongsTo('Titles')
            ->setJoinType('INNER');
        $this->belongsTo('Countries')
            ->setJoinType('INNER');
        $this->hasMany('TitleScoreDetails');
    }

    /**
     * @inheritDoc
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('country_id')
            ->requirePresence('country_id', 'create')
            ->notEmptyString('country_id');

        $validator
            ->integer('title_id')
            ->allowEmptyString('title_id');

        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->allowEmptyString('name');

        $validator
            ->scalar('result')
            ->maxLength('result', 30)
            ->allowEmptyString('result');

        $validator
            ->date('started', ['y/m/d'])
            ->requirePresence('started', 'create')
            ->notEmptyDate('started');

        $validator
            ->date('ended', ['y/m/d'])
            ->requirePresence('ended', 'create')
            ->notEmptyDate('ended');

        $validator
            ->boolean('is_world')
            ->notEmptyString('is_world');

        $validator
            ->boolean('is_official')
            ->notEmptyString('is_official');

        return $validator;
    }

    /**
     * @inheritDoc
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['country_id'], 'Countries'));
        $rules->add($rules->existsIn(['title_id'], 'Titles'));

        return $rules;
    }

    /**
     * IDに合致するデータと関連データを取得します。
     *
     * @param int $id 検索キー
     * @return \Gotea\Model\Entity\TitleScore 取得したエンティティ
     * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException
     */
    public function findByIdWithRelation(int $id)
    {
        return $this->get($id, [
            'contain' => [
                'Countries',
                'Titles' => [
                    'joinType' => 'LEFT',
                ],
                'TitleScoreDetails',
                'TitleScoreDetails.Players',
                'TitleScoreDetails.Players.Ranks',
                'TitleScoreDetails.Players.PlayerRanks.Ranks',
            ],
        ]);
    }

    /**
     * タイトル勝敗を検索します。
     *
     * @param array $data パラメータ
     * @return \Cake\ORM\Query 生成されたクエリ
     */
    public function findMatches(array $data): Query
    {
        $query = $this->find()
            ->contain([
                'Countries',
                'TitleScoreDetails',
                'TitleScoreDetails.Players',
                'TitleScoreDetails.Players.Ranks',
                'TitleScoreDetails.Players.PlayerRanks.Ranks',
            ])
            ->orderDesc('started')->orderDesc('TitleScores.id');

        $id = Hash::get($data, 'player_id');
        if ($id) {
            $query->leftJoinWith('TitleScoreDetails', function (Query $q) use ($id) {
                return $q->innerJoinWith('Players', function (Query $q) use ($id) {
                    return $q->where(['TitleScoreDetails.player_id' => $id]);
                });
            });
        }

        // 棋士名は複数設定されているかどうかで抽出条件を変更する
        $name1 = Hash::get($data, 'name1');
        $name2 = Hash::get($data, 'name2');
        if ($name1 && $name2) {
            $query->join([
                's' => [
                    'type' => 'INNER',
                    'table' => $this->TitleScoreDetails->find()->select([
                        'title_score_id' => 'title_score_id',
                        'player_names' => $query->func()->group_concat([
                            'player_name separator \'/\'' => 'identifier',
                        ]),
                    ])->group('title_score_id')->having([
                        'OR' => [
                            ["player_names LIKE '%{$name1}%/%{$name2}%'"],
                            ["player_names LIKE '%{$name2}%/%{$name1}%'"],
                        ],
                    ]),
                    'conditions' => 's.title_score_id = TitleScores.id',
                ],
            ]);
        } elseif ($name1 || $name2) {
            $name = $name1 ? $name1 : $name2;
            $query->where(function (QueryExpression $exp) use ($name) {
                $q = $this->TitleScoreDetails->find();

                return $exp->exists(
                    $q
                        ->select(['X' => 1])
                        ->where([
                            'TitleScoreDetails.title_score_id = TitleScores.id',
                            'TitleScoreDetails.player_name LIKE' => "%{$name}%",
                        ]),
                );
            });
        }

        $titleName = Hash::get($data, 'title_name');
        if ($titleName) {
            $query->where(['TitleScores.name like' => "%${titleName}%"]);
        }

        $year = Hash::get($data, 'target_year');
        if ($year) {
            $query->where(['YEAR(TitleScores.started)' => $year])->where(['YEAR(TitleScores.ended)' => $year]);
        }

        $countryId = Hash::get($data, 'country_id');
        if ($countryId) {
            $query->where(['TitleScores.country_id' => $countryId]);
        }

        $started = Hash::get($data, 'started', 0);
        if ($started > 0) {
            $query->where(['TitleScores.started >= ' => $started]);
        }

        $ended = Hash::get($data, 'ended', 0);
        if ($ended > 0) {
            $query->where(['TitleScores.ended <= ' => $ended]);
        }

        return $query;
    }
}
