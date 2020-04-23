<?php
declare(strict_types=1);

namespace Gotea\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\I18n\Date;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * PlayerRanks Model
 *
 * @property \Gotea\Model\Table\PlayersTable|\Cake\ORM\Association\BelongsTo $Players
 * @property \Gotea\Model\Table\RanksTable|\Cake\ORM\Association\BelongsTo $Ranks
 * @method \Gotea\Model\Entity\PlayerRank get($primaryKey, $options = [])
 * @method \Gotea\Model\Entity\PlayerRank newEntity($data = null, array $options = [])
 * @method \Gotea\Model\Entity\PlayerRank[] newEntities(array $data, array $options = [])
 * @method \Gotea\Model\Entity\PlayerRank|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Gotea\Model\Entity\PlayerRank patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Gotea\Model\Entity\PlayerRank[] patchEntities($entities, array $data, array $options = [])
 * @method \Gotea\Model\Entity\PlayerRank findOrCreate($search, callable $callback = null, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PlayerRanksTable extends AppTable
{
    /**
     * @inheritDoc
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('player_ranks');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Players')
            ->setJoinType('INNER');
        $this->belongsTo('Ranks')
            ->setJoinType('INNER');
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
            ->requirePresence(['player_id', 'rank_id', 'promoted'])
            ->integer('player_id')
            ->integer('rank_id')
            ->date('promoted', 'y/m/d');

        return $validator;
    }

    /**
     * @inheritDoc
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['player_id'], 'Players'));
        $rules->add($rules->existsIn(['rank_id'], 'Ranks'));

        $rules->add($rules->isUnique(
            ['player_id', 'rank_id'],
            '昇段情報がすでに存在します。'
        ));

        return $rules;
    }

    /**
     * @inheritDoc
     */
    public function save(EntityInterface $entity, $options = [])
    {
        $save = parent::save($entity, $options);

        // 最新の昇段情報として登録する場合は棋士情報を更新
        if ($save && $entity->newest) {
            $players = TableRegistry::getTableLocator()->get('Players');
            $player = $players->get($entity->player_id);
            $player->rank_id = $entity->rank_id;
            $players->save($player, $options);
        }

        return $save;
    }

    /**
     * 棋士に該当する段位一覧を取得します。
     *
     * @param int $playerId 棋士ID
     * @return \Cake\ORM\ResultSet
     */
    public function findRanks(int $playerId)
    {
        return $this->findByPlayerId($playerId)
            ->contain(['Ranks'])->orderDesc('Ranks.rank_numeric')->all();
    }

    /**
     * 最近の昇段者を取得します。
     *
     * @return \Cake\ORM\Query 生成されたクエリ
     */
    public function findRecentPromoted()
    {
        return $this->find()
            ->contain([
                'Players',
                'Players.Countries',
                'Ranks' => function (Query $q) {
                    return $q->where(['rank_numeric >' => 1]);
                },
            ])
            ->where(['PlayerRanks.promoted >=' => Date::now()->addMonths(-2)])
            ->orderDesc('PlayerRanks.promoted')
            ->order('Players.country_id')
            ->orderDesc('Ranks.rank_numeric');
    }
}
