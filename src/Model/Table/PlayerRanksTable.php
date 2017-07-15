<?php
namespace App\Model\Table;

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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->notEmpty('promoted', $this->getMessage($this->REQUIRED, '昇段日'))
            ->date('promoted', 'ymd', $this->getMessage($this->INLALID_FORMAT, ['昇段日', 'yyyy/MM/dd']))
            ->requirePresence('promoted', 'create');

        return $validator;
    }

    /**
     * {@inheritdoc}
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
		return $this->_addEntity($data, [
            'player_id', 'rank_id',
        ]);
    }

    /**
     * 棋士に該当する段位一覧を取得します。
     *
     * @param int $playerId
     * @return \Cake\ORM\ResultSet
     */
    public function findRanks(int $playerId)
    {
        return $this->findByPlayerId($playerId)
            ->contain(['Ranks'])->orderDesc('Ranks.rank_numeric')->all();
    }
}
