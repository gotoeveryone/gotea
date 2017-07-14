<?php

namespace App\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * 保持履歴
 */
class RetentionHistoriesTable extends AppTable
{
    /**
	 * 初期設定
	 */
    public function initialize(array $config)
    {
        // タイトルマスタ
        $this->belongsTo('Titles');
        // 棋士マスタ
        $this->belongsTo('Players', [
            'joinType' => 'LEFT'
        ]);
        // 段位マスタ
        $this->belongsTo('Ranks', [
            'joinType' => 'LEFT'
        ]);
    }

    /**
     * バリデーションルール
     *
     * @param \App\Model\Table\Validator $validator
     * @return type
     */
    public function validationDefault(Validator $validator)
    {
        return $validator
            ->notEmpty('target_year', $this->getMessage($this->REQUIRED, '対象年'))
            ->numeric('target_year', $this->getMessage($this->NUMERIC, '対象年'))
            ->notEmpty('name', $this->getMessage($this->REQUIRED, 'タイトル名'))
            ->notEmpty('holding', $this->getMessage($this->REQUIRED, '期'))
            ->numeric('holding', $this->getMessage($this->NUMERIC, '期'))
            ->allowEmpty('win_group_name')
            ->maxLength('win_group_name', 30, $this->getMessage($this->MAX_LENGTH, ['グループ名', 30]));
    }

    /**
     * {@inheritdoc}
     */
    public function save(EntityInterface $entity, $options = [])
    {
        $save = parent::save($entity, $options);

        // 最新を登録する場合はタイトルマスタも更新
        if ($entity->is_latest) {
            $table = TableRegistry::get('Titles');
            $title = $table->get($entity->title_id);
            $title->holding = $entity->holding;
            $table->save($title);
        }

        return $save;
    }

    /**
     * データを追加します。
     *
     * @param array $data
     * @return \App\Model\Entity\RetentionHistory|false データが登録できればそのEntity
     */
    public function add(array $data)
    {
		return $this->_addEntity($data, [
            'title_id', 'holding',
        ]);
    }
}
