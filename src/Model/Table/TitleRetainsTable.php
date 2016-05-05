<?php

namespace App\Model\Table;

use Cake\Validation\Validator;

/**
 * タイトル保持情報
 */
class TitleRetainsTable extends AppTable {

    /**
	 * 初期設定
	 */
    public function initialize(array $config)
    {
        $this->table('T_TITLE_RETAIN');
        $this->primaryKey('ID');
        // タイトルマスタ
        $this->belongsTo('Titles', [
            'foreignKey' => 'TITLE_ID',
            'joinType' => 'INNER'
        ]);
        // 棋士マスタ
        $this->belongsTo('Players', [
            'foreignKey' => 'PLAYER_ID',
            'joinType' => 'LEFT'
        ]);
        // 段位マスタ
        $this->belongsTo('Ranks', [
            'foreignKey' => 'RANK_ID',
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
            'TitleRetains.TITLE_ID' => $titleId,
            'TitleRetains.HOLDING' => $holding
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
            ->notEmpty('TARGET_YEAR', '対象年は必須です。')
            ->add('TARGET_YEAR', [
                'valid' => [
                    'rule' => 'numeric', 'message' => '対象年は数字で入力してください。'
                ]
            ])
            ->notEmpty('HOLDING', '期は必須です。')
            ->add('HOLDING', [
                'valid' => [
                    'rule' => 'numeric', 'message' => '期は数字で入力してください。'
                ]
            ])
            ->allowEmpty('WIN_GROUP_NAME')
            ->add('WIN_GROUP_NAME', [
                'length' => [
                    'rule' => ['maxLength', 30], 'message' => 'グループ名は30文字以下で入力してください。'
                ]
            ]);
    }
}
