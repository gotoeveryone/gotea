<?php

namespace App\Model\Table;

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
        return $validator
            ->notEmpty(['target_year', 'name', 'holding'])
            ->numeric('target_year')
            ->numeric('holding')
            ->allowEmpty('win_group_name', function($context) {
                return $context['data']['is_team'] === '0';
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
            $table = TableRegistry::get('Titles');
            $title = $table->get($entity->title_id);
            $title->holding = $entity->holding;
            $table->save($title);
        }

        return $save;
    }

    /**
     * 指定した棋士のタイトル履歴を取得します。
     *
     * @param int $playerId
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
     * @param int $titleId
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
