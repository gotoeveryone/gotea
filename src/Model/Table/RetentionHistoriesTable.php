<?php

namespace App\Model\Table;

use Cake\Validation\Validator;

/**
 * 保持履歴
 */
class RetentionHistoriesTable extends AppTable {

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
     * キー情報をもとに、保持履歴を1件取得します。
     * 
     * @param array $data
     * @return null|\App\Model\Entity\RetentionHistory
     */
    public function findByKey($data)
    {
        if (empty($data['title_id']) || empty($data['holding'])) {
            return null;
        }
		$query = $this->find()->where([
            'title_id' => $data['title_id'],
            'holding' => $data['holding']
		]);
        return $query->first();
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
}
