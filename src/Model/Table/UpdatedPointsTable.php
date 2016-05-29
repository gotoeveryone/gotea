<?php

namespace App\Model\Table;

use Cake\Validation\Validator;

/**
 * 成績更新
 */
class UpdatedPointsTable extends AppTable
{
    /**
	 * 初期設定
	 */
    public function initialize(array $config)
    {
        $this->belongsTo('Countries');
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
            ->notEmpty('score_updated', '成績更新日は必須です。')
            ->add('score_updated', [
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
            'target_year' => $targetYear
        ])->order([
            'country_id',
            'target_year' => 'DESC'
        ])->contain(['Countries'])->all();
    }

    /**
     * 成績更新日情報の取り扱い念を取得します。
     * 
     * @param type $targetYear
     * @return type
     */
    public function findToArray()
    {
        return $this->find('list', [
            'keyField' => 'keyField',
            'valueField' => 'valueField'
        ])->group(['target_year'])->order(['target_year' => 'DESC'])->select([
            'keyField' => 'target_year',
            'valueField' => 'target_year'
        ])->toArray();
    }
}
