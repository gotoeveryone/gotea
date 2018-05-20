<?php

namespace Gotea\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * 保持履歴
 */
class RetentionHistoriesTable extends AppTable
{
    /**
     * {@inheritdoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        // タイトルマスタ
        $this->belongsTo('Titles')
            ->setJoinType('INNER');
        // 棋士マスタ
        $this->belongsTo('Players');
        // 段位マスタ
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

        return $validator
            ->requirePresence([
                'title_id', 'holding', 'target_year', 'name',
            ])
            ->integer('player_id')
            ->integer('rank_id')
            ->integer('holding')
            ->integer('target_year')
            ->requirePresence(['player_id', 'rank_id'], function ($context) {
                return empty($context['data']['is_team']);
            })
            ->requirePresence('win_group_name', function ($context) {
                return !empty($context['data']['is_team']);
            })
            ->maxLength('win_group_name', 30);
    }

    /**
     * {@inheritdoc}
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(
            ['title_id', 'holding'],
            '該当期の保持履歴がすでに存在します。'
        ));

        return $rules;
    }

    /**
     * {@inheritdoc}
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
    public function findHistoriesByPlayer(int $playerId) : Query
    {
        return $this->findByPlayerId($playerId)
            ->contain(['Titles.Countries'])
            ->order([
                'RetentionHistories.target_year' => 'DESC',
                'Titles.country_id' => 'ASC',
                'Titles.sort_order' => 'ASC'
            ]);
    }

    /**
     * 指定したタイトルの履歴を取得します。
     *
     * @param int $titleId タイトルID
     * @return \Cake\ORM\Query 生成されたクエリ
     */
    public function findHistoriesByTitle(int $titleId) : Query
    {
        return $this->findByTitleId($titleId)
            ->contain(['Titles'])
            ->order([
                'RetentionHistories.target_year' => 'DESC',
            ]);
    }
}
