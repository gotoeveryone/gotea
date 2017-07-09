<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PlayerRanks Model
 *
 * @property \App\Model\Table\PlayersTable|\Cake\ORM\Association\BelongsTo $Players
 * @property \App\Model\Table\RanksTable|\Cake\ORM\Association\BelongsTo $Ranks
 *
 * @method \App\Model\Entity\PlayerRank get($primaryKey, $options = [])
 * @method \App\Model\Entity\PlayerRank newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\PlayerRank[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PlayerRank|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PlayerRank patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PlayerRank[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\PlayerRank findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PlayerRanksTable extends AppTable
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

        $this->setTable('player_ranks');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Players');
        $this->belongsTo('Ranks');
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
            ->date('promoted')
            ->requirePresence('promoted', 'create')
            ->notEmpty('promoted');

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
        $rules->add($rules->existsIn(['player_id'], 'Players'));
        $rules->add($rules->existsIn(['rank_id'], 'Ranks'));

        return $rules;
    }

    /**
     * データを追加します。
     *
     * @param array $data
     * @return \App\Model\Entity\PlayerRank|false データが登録できればそのEntity
     */
    public function add(array $data)
    {
        // 同一キーのデータがあれば終了
		if ($this->findByKey($data, [
            'player_id', 'rank_id',
        ])) {
            return false;
		}

        // タイトル保持情報の登録
        $history = $this->newEntity($data);
        return $this->save($history);
    }
}
