<?php

namespace App\Model\Table;

use Cake\I18n\Time;
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
            'foreignKey' => 'COUNTRY_ID',
            'joinType' => 'INNER'
        ]);
        // 段位マスタ
        $this->belongsTo('Ranks', [
            'foreignKey' => 'RANK_ID',
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
            ->notEmpty('NAME', '棋士名は必須です。')
//            ->notEmpty('BIRTHDAY', '生年月日は必須です。')
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
     * @return Player 棋士情報
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
                    'Titles.COUNTRY_ID' => 'ASC',
                    'Titles.SORT_ORDER' => 'ASC'
                ]);
            },
            'TitleRetains.Titles.Countries'
        ])->where(['Players.ID' => $id])->first();
    }

    /**
     * 指定条件に合致した棋士情報を取得します。
     * 
     * @param type $countryCode
     * @param type $sex
     * @param type $rank
     * @param type $playerName
     * @param type $playerNameEn
     * @param type $enrollmentFrom
     * @param type $enrollmentTo
     * @param type $retire
     * @return Player 棋士情報一覧
     */
    public function findPlayers($countryCode = null, $sex = null, $rank = null, $playerName = null, $playerNameEn = null,
            $enrollmentFrom = null, $enrollmentTo = null, $retire = null)
    {
        // 棋士情報の取得
        $query = $this->find();

        // 入力されたパラメータが空でなければ、WHERE句へ追加
        if ($countryCode) {
            $query->where(['Players.COUNTRY_ID' => $countryCode]);
        }
        if ($sex) {
            $query->where(['Players.SEX' => $sex]);
        }
        if ($rank) {
            $query->where(['Players.RANK_ID' => $rank]);
        }
        if ($playerName) {
            $query->where(['Players.NAME LIKE' => '%'.$playerName.'%']);
        }
        if ($playerNameEn) {
            $query->where(['Players.NAME_ENGLISH LIKE' => '%'.$playerNameEn.'%']);
        }
        if ($enrollmentFrom) {
            $query->where(['SUBSTRING(Players.ENROLLMENT, 1, 4) >=' => $enrollmentFrom]);
        }
        if ($enrollmentTo) {
            $query->where(['SUBSTRING(Players.ENROLLMENT, 1, 4) <=' => $enrollmentTo]);
        }
        if ($retire && $retire === 'false') {
            $query->where(['Players.DELETE_FLAG' => 0]);
        }

        // データを取得
        return $query->order([
            'Players.RANK_ID DESC',
            'Players.ENROLLMENT',
            'Players.ID'
        ])->contain([
            'PlayerScores' => function ($q) {
                return $q->where(['PlayerScores.TARGET_YEAR' => intval(Time::now()->year)]);
            },
            'Ranks',
            'Countries'
        ])->all();
    }
}
