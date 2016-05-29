<?php

namespace App\Model\Table;

use Cake\Validation\Validator;

/**
 * 棋士成績
 */
class PlayerScoresTable extends AppTable
{
    /**
	 * 初期設定
	 */
    public function initialize(array $config)
    {
        $this->belongsTo('Ranks');
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
            ->notEmpty('win_point', '勝数は必須です。')
            ->add('win_point', [
                'valid' => [
                    'rule' => 'numeric', 'message' => '勝数は数字で入力してください。'
                ]
            ])
            ->notEmpty('lose_point', '敗数は必須です。')
            ->add('lose_point', [
                'valid' => [
                    'rule' => 'numeric', 'message' => '敗数は数字で入力してください。'
                ]
            ])
            ->notEmpty('draw_point', '引分数は必須です。')
            ->add('draw_point', [
                'valid' => [
                    'rule' => 'numeric', 'message' => '引分数は数字で入力してください。'
                ]
            ])
            ->notEmpty('win_point_world', '勝数（国際棋戦）は必須です。')
            ->add('win_point_world', [
                'valid' => [
                    'rule' => 'numeric', 'message' => '勝数（国際棋戦）は数字で入力してください。'
                ]
            ])
            ->notEmpty('lose_point_world', '敗数（国際棋戦）は必須です。')
            ->add('lose_point_world', [
                'valid' => [
                    'rule' => 'numeric', 'message' => '敗数（国際棋戦）は数字で入力してください。'
                ]
            ])
            ->notEmpty('draw_point_world', '引分数（国際棋戦）は必須です。')
            ->add('draw_point_world', [
                'valid' => [
                    'rule' => 'numeric', 'message' => '引分数（国際棋戦）は数字で入力してください。'
                ]
            ]);
    }

    /**
     * 棋士IDと対象年で棋士成績情報を1件取得します。
     * 
     * @param type $playerId
     * @param type $targetYear
     * @return type
     */
    public function findByPlayerAndYear($playerId, $targetYear)
    {
        return $this->find()->contain([
            'Ranks',
        ])->where([
            'PLAYER_ID' => $playerId,
            'TARGET_YEAR' => $targetYear
        ])->first();
    }

    /**
     * 成績更新日情報の取り扱い念を取得します。
     * 
     * @param type $targetYear
     * @return type
     */
    public function findScoreUpdateToArrayWithSuffix()
    {
        return $this->find('list', [
            'keyField' => 'keyField',
            'valueField' => 'valueField'
        ])->group(['PlayerScores.TARGET_YEAR'])->order(['PlayerScores.TARGET_YEAR' => 'DESC'])->select([
            'keyField' => 'PlayerScores.TARGET_YEAR',
            'valueField' => 'CONCAT(PlayerScores.TARGET_YEAR, \'年度\')'
        ])->toArray();
    }
}
