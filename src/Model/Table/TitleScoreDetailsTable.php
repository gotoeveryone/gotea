<?php

namespace App\Model\Table;

use App\Model\Entity\Country;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * @param int $targetYear
     * @return string|null
     */
    public function findRecent(Country $country, int $targetYear)
    {
        // 旧方式
        if ($this->_isOldRanking($targetYear)) {
            $points = TableRegistry::get('UpdatedPoints');
            return $points->findRecent($country, $targetYear);
        }

        // 対局棋士の所属国が該当する・もしくは国際棋戦の最新であるデータの対局日を返却
        return $this->find()->select([
            'max' => 'max(ended)'
        ])->contain([
            'TitleScores',
            'Players',
        ])->where(['Players.country_id' => $country->id])
            ->orWhere(['TitleScores.is_world' => true])->first()->max;
    }
}
