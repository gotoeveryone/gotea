<?php

namespace App\Model\Table;

use Cake\Validation\Validator;

/**
 * 棋士マスタ
 */
class PlayersTable extends AppTable
{
	/**
	 * 初期設定
     * 
     * @param $config
	 */
    public function initialize(array $config)
    {
        $this->table('M_PLAYER');
        $this->primaryKey('ID');
//        $this->entityClass('App\Model\Entity\Player');
        // 所属国マスタ
        $this->belongsTo('Countries', [
            'foreignKey' => 'COUNTRY_CD',
            'joinType' => 'INNER'
        ]);
        // 段位マスタ
        $this->belongsTo('Ranks', [
            'foreignKey' => 'RANK',
            'joinType' => 'INNER'
        ]);
        // 棋士成績情報
        $this->hasMany('PlayerScores', [
            'foreignKey' => 'PLAYER_ID',
            'order' => array('PlayerScores.TARGET_YEAR' => 'DESC')
        ]);
        // タイトル保持情報
        $this->hasMany('TitleRetains', [
            'foreignKey' => 'PLAYER_ID',
            'order' => array('TitleRetains.TARGET_YEAR' => 'DESC')
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
            ->notEmpty('PLAYER_NAME', '棋士名は必須です。')
            ->notEmpty('BIRTHDAY', '生年月日は必須です。')
//            ->add('BIRTHDAY', [
//                'valid' => [
//                    'rule' => ['date', 'ymd'],
//                    'message' => '生年月日は「yyyy/MM/dd」形式で入力してください。'
//                ]
//            ])
            ->notEmpty('ENROLLMENT', '入段日は必須です。')
            ->add('ENROLLMENT', [
                'alphaNumeric' => [
                    'rule' => 'alphaNumeric',
                    'message' => '入段日は数字で入力してください。'
                ]
            ]);
    }

    /**
     * 棋士情報に関する一式を取得します。
     * 
     * @param type $id
     * @return type
     */
    public function findPlayerAllRelations($id)
    {
		return $this->find()->contain([
            'Countries',
            'Ranks',
            'PlayerScores' => function ($q) {
                return $q->order(['PlayerScores.TARGET_YEAR' => 'DESC']);
            },
            'PlayerScores.Ranks',
            'TitleRetains.Titles',
            'TitleRetains' => function ($q) {
                return $q->order([
                    'TitleRetains.TARGET_YEAR' => 'DESC',
                    'Titles.COUNTRY_CD' => 'ASC',
                    'Titles.SORT_ORDER' => 'ASC'
                ]);
            },
            'TitleRetains.Titles.Countries'
        ])->where(['Players.ID' => $id])->first();
    }
}
