<?php
declare(strict_types=1);

namespace Gotea\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * 保持履歴
 *
 * @property \Gotea\Model\Table\TitlesTable|\Cake\ORM\Association\BelongsTo $Titles
 * @property \Gotea\Model\Table\PlayersTable|\Cake\ORM\Association\BelongsTo $Players
 * @property \Gotea\Model\Table\CountriesTable|\Cake\ORM\Association\BelongsTo $Countries
 */
class RetentionHistoriesTable extends AppTable
{
    /**
     * @inheritDoc
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        // タイトルマスタ
        $this->belongsTo('Titles')
            ->setJoinType(Query::JOIN_TYPE_INNER);
        // 棋士
        $this->belongsTo('Players');
        // 出場国
        $this->belongsTo('Countries');
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
            ->integer('title_id')
            ->requirePresence('title_id', 'create')
            ->notEmptyString('title_id');

        $validator
            ->integer('player_id')
            ->requirePresence('player_id', function ($context) {
                return empty($context['data']['is_team']);
            })
            ->notEmptyString('player_id', null, function ($context) {
                return empty($context['data']['is_team']);
            });

        $validator
            ->integer('country_id')
            ->allowEmptyString('country_id');

        $validator
            ->integer('holding')
            ->requirePresence('holding', 'create')
            ->notEmptyString('holding');

        $validator
            ->integer('target_year')
            ->requirePresence('target_year', 'create')
            ->notEmptyString('target_year');

        $validator
            ->scalar('name')
            ->maxLength('name', 30)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('win_group_name')
            ->maxLength('win_group_name', 30)
            ->requirePresence('win_group_name', function ($context) {
                return !empty($context['data']['is_team']);
            })
            ->notEmptyString('win_group_name', null, function ($context) {
                return !empty($context['data']['is_team']);
            });

        $validator
            ->boolean('is_team')
            ->notEmptyString('is_team');

        $validator
            ->date('acquired', ['y/m/d'])
            ->requirePresence('acquired', 'create')
            ->notEmptyDate('acquired');

        $validator
            ->boolean('is_official')
            ->notEmptyString('is_official');

        $validator
            ->date('broadcasted', ['y/m/d'])
            ->allowEmptyDate('broadcasted');

        return $validator;
    }

    /**
     * @inheritDoc
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(
            ['title_id', 'holding'],
            '該当期の保持履歴がすでに存在します。'
        ));

        $rules->add($rules->existsIn(['title_id'], 'Titles'));
        $rules->add($rules->existsIn(['player_id'], 'Players'));
        $rules->add($rules->existsIn(['country_id'], 'Countries'));

        return $rules;
    }

    /**
     * @inheritDoc
     */
    public function save(EntityInterface $entity, $options = [])
    {
        $save = parent::save($entity, $options);

        // 最新を登録する場合はタイトルマスタも更新
        if ($save && $entity->newest) {
            $table = TableRegistry::getTableLocator()->get('Titles');
            $title = $table->get($entity->title_id);
            $title->holding = $entity->holding;
            $table->save($title);
        }

        return $save;
    }

    /**
     * 指定した棋士のタイトル履歴を取得します。
     *
     * @param int $playerId 棋士ID
     * @return \Cake\ORM\Query 生成されたクエリ
     */
    public function findHistoriesByPlayer(int $playerId): Query
    {
        return $this->findByPlayerId($playerId)
            ->contain(['Titles.Countries'])
            ->order([
                'RetentionHistories.target_year' => 'DESC',
                'Titles.country_id' => 'ASC',
                'RetentionHistories.acquired' => 'ASC',
            ]);
    }

    /**
     * 指定したタイトルの履歴を取得します。
     *
     * @param int $titleId タイトルID
     * @return \Cake\ORM\Query 生成されたクエリ
     */
    public function findHistoriesByTitle(int $titleId): Query
    {
        return $this->findByTitleId($titleId)
            ->contain(['Titles'])
            ->order([
                'RetentionHistories.target_year' => 'DESC',
            ]);
    }
}
