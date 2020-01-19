<?php
namespace Gotea\Model\Table;

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
 *
 * @method \Gotea\Model\Entity\TitleScore get($primaryKey, $options = [])
 * @method \Gotea\Model\Entity\TitleScore newEntity($data = null, array $options = [])
 * @method \Gotea\Model\Entity\TitleScore[] newEntities(array $data, array $options = [])
 * @method \Gotea\Model\Entity\TitleScore|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Gotea\Model\Entity\TitleScore patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Gotea\Model\Entity\TitleScore[] patchEntities($entities, array $data, array $options = [])
 * @method \Gotea\Model\Entity\TitleScore findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TitleScoresTable extends AppTable
{
    /**
     * {@inheritdoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->belongsTo('Titles')
            ->setJoinType('INNER');
        $this->belongsTo('Countries')
            ->setJoinType('INNER');
        $this->hasMany('TitleScoreDetails');
    }

    /**
     * {@inheritdoc}
     */
    public function validationDefault(Validator $validator)
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
     * {@inheritdoc}
     */
    public function buildRules(RulesChecker $rules)
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
                'TitleScoreDetails.Ranks',
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
                'TitleScoreDetails.Ranks',
            ])
            ->orderDesc('started')->orderDesc('TitleScores.id');

        if (($id = Hash::get($data, 'player_id'))) {
            $query->leftJoinWith('TitleScoreDetails', function (Query $q) use ($id) {
                return $q->innerJoinWith('Players', function (Query $q) use ($id) {
                    return $q->where(['TitleScoreDetails.player_id' => $id]);
                });
            });
        }
        if (($name = Hash::get($data, 'name'))) {
            $query->leftJoinWith('TitleScoreDetails', function (Query $q) use ($name) {
                return $q->innerJoinWith('Players', function (Query $q) use ($name) {
                    return $q->where(['Players.name like' => "%${name}%"]);
                });
            });
        }
        if (($titleName = Hash::get($data, 'title_name'))) {
            $query->where(['TitleScores.name like' => "%${titleName}%"]);
        }
        if (($year = Hash::get($data, 'target_year'))) {
            $query->where(['YEAR(TitleScores.started)' => $year])->where(['YEAR(TitleScores.ended)' => $year]);
        }
        if (($countryId = Hash::get($data, 'country_id'))) {
            $query->where(['TitleScores.country_id' => $countryId]);
        }
        if (($started = Hash::get($data, 'started', 0)) > 0) {
            $query->where(['TitleScores.started >= ' => $started]);
        }
        if (($ended = Hash::get($data, 'ended', 0)) > 0) {
            $query->where(['TitleScores.ended <= ' => $ended]);
        }

        return $query;
    }
}
