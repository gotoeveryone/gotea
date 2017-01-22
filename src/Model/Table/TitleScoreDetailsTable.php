<?php
namespace App\Model\Table;

use App\Model\Entity\Country;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TitleScoreDetails Model
 *
 * @property \Cake\ORM\Association\BelongsTo $TitleScores
 * @property \Cake\ORM\Association\BelongsTo $Players
 *
 * @method \App\Model\Entity\TitleScoreDetail get($primaryKey, $options = [])
 * @method \App\Model\Entity\TitleScoreDetail newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\TitleScoreDetail[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TitleScoreDetail|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TitleScoreDetail patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TitleScoreDetail[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\TitleScoreDetail findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TitleScoreDetailsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('title_score_details');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('TitleScores', [
            'foreignKey' => 'title_score_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Players', [
            'foreignKey' => 'player_id'
        ]);
        $this->belongsTo('Winner', [
            'className' => 'Players',
            'foreignKey' => 'player_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Loser', [
            'className' => 'Players',
            'foreignKey' => 'player_id',
            'joinType' => 'INNER'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('division', 'create')
            ->notEmpty('division');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['title_score_id'], 'TitleScores'));
        $rules->add($rules->existsIn(['player_id'], 'Players'));

        return $rules;
    }

    /**
     * 最新データの対局日を取得します。
     * 
     * @param \App\Model\Entity\Country $country
     * @return int|null
     */
    public function getRecent(Country $country)
    {
        return $this->find()->select([
            'max' => 'max(ended)'
        ])->contain([
            'TitleScores' => function(Query $q) use ($country) {
                return $q->where(['country_id' => $country->id]);
            }
        ])->first()->max;
    }
}
