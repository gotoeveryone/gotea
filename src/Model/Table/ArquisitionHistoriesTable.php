<?php

namespace App\Model\Table;

use Cake\Validation\Validator;

/**
 * タイトル保持情報
 */
class ArquisitionHistoriesTable extends AppTable {

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
     * タイトル情報を1件取得します。
     * 
     * @param type $titleId
     * @param type $holding
     * @param type $isCount
     */
    public function findByKey($titleId, $holding, $isCount = false)
    {
		$query = $this->find()->where([
            'title.id' => $titleId,
            'holding' => $holding
		]);
        return $isCount ? $query->count() : $query->all();
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
            ->notEmpty('target_year', '対象年は必須です。')
            ->add('target_year', [
                'valid' => [
                    'rule' => 'numeric', 'message' => '対象年は数字で入力してください。'
                ]
            ])
            ->notEmpty('holding', '期は必須です。')
            ->add('holding', [
                'valid' => [
                    'rule' => 'numeric', 'message' => '期は数字で入力してください。'
                ]
            ])
            ->allowEmpty('win_group_name')
            ->add('win_group_name', [
                'length' => [
                    'rule' => ['maxLength', 30], 'message' => 'グループ名は30文字以下で入力してください。'
                ]
            ]);
    }
}
