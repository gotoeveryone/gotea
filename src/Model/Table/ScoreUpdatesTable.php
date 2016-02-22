<?php

namespace App\Model\Table;

use Cake\Validation\Validator;

/**
 * 成績更新日情報
 */
class ScoreUpdatesTable extends AppTable
{
    /**
	 * 初期設定
	 */
    public function initialize(array $config)
    {
        $this->table('T_SCORE_UPDATE');
        $this->primaryKey('ID');
        $this->belongsTo('Countries', [
            'foreignKey' => 'COUNTRY_CD',
            'joinType' => 'INNER'
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
            ->notEmpty('SCORE_UPDATE_DATE', '成績更新日は必須です。')
            ->add('SCORE_UPDATE_DATE', [
                'valid' => [
                    'rule' => ['date', 'ymd'],
                    'message' => '成績更新日は「yyyy/MM/dd」形式で入力してください。'
                ]
            ]);
    }

    /**
     * 成績更新日情報の取り扱い念を取得します。
     * 
     * @param type $targetYear
     * @return type
     */
    public function findScoreUpdateHasYear($targetYear)
    {
        return $this->find()->where([
            'ScoreUpdates.TARGET_YEAR' => $targetYear
        ])->order([
            'ScoreUpdates.COUNTRY_CD',
            'ScoreUpdates.TARGET_YEAR' => 'DESC'
        ])->contain(['Countries'])->all();
    }

    /**
     * 成績更新日情報の取り扱い念を取得します。
     * 
     * @param type $targetYear
     * @return type
     */
    public function findScoreUpdateToArray()
    {
        return $this->find('list', [
            'keyField' => 'keyField',
            'valueField' => 'valueField'
        ])->group(['ScoreUpdates.TARGET_YEAR'])->order(['ScoreUpdates.TARGET_YEAR' => 'DESC'])->select([
            'keyField' => 'ScoreUpdates.TARGET_YEAR',
            'valueField' => 'ScoreUpdates.TARGET_YEAR'
        ])->toArray();
    }
}
