<?php

namespace App\Model\Table;

use Cake\I18n\Time;
use Cake\Validation\Validator;

/**
 * 棋士
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
        // 所属国マスタ
        $this->belongsTo('Countries');
        // 段位マスタ
        $this->belongsTo('Ranks');
        // 棋士成績情報
        $this->hasMany('PlayerScores', [
            'order' => array('PlayerScores.target_year' => 'DESC')
        ]);
        // タイトル保持情報
        $this->hasMany('ArquisitionHistories', [
            'joinType' => 'LEFT',
            'order' => array('ArquisitionHistories.target_year' => 'DESC')
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
            ->notEmpty('name', '棋士名は必須です。')
            ->allowEmpty('birthday')
            ->add('birthday', [
                'valid' => [
                    'rule' => ['date', 'ymd'],
                    'message' => '生年月日は「yyyy/MM/dd」形式で入力してください。'
                ]
            ])
            ->notEmpty('joined', '入段日は「yyyy/MM/dd」形式で入力してください。')
            ->add('joined', [
                'valid' => [
                    'rule' => ['date', 'ymd'],
                    'message' => '入段日は「yyyy/MM/dd」形式で入力してください。'
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
                return $q->order(['PlayerScores.target_year' => 'DESC']);
            },
            'PlayerScores.Ranks',
            'ArquisitionHistories.Titles',
            'ArquisitionHistories' => function ($q) {
                return $q->order([
                    'ArquisitionHistories.target_year' => 'DESC',
                    'Titles.country_id' => 'ASC',
                    'Titles.sort_order' => 'ASC'
                ]);
            },
            'ArquisitionHistories.Titles.Countries'
        ])->where(['Players.id' => $id])->first();
    }

    /**
     * 指定条件に合致した棋士情報を取得します。
     * 
     * @param type $countryCode
     * @param type $sex
     * @param type $rank
     * @param type $playerName
     * @param type $playerNameEn
     * @param type $joined
     * @param type $enrollmentTo
     * @param type $retire
     * @param type $count
     * @return Player 棋士情報一覧
     */
    public function findPlayers($countryCode = null, $sex = null, $rank = null, $playerName = null, $playerNameEn = null,
            $joined = null, $enrollmentTo = null, $retire = null, $count = false)
    {
        // 棋士情報の取得
        $query = $this->find();

        // 入力されたパラメータが空でなければ、WHERE句へ追加
        if ($countryCode) {
            $query->where(['Countries.id' => $countryCode]);
        }
        if ($sex) {
            $query->where(['Players.sex' => $sex]);
        }
        if ($rank) {
            $query->where(['Players.rank_id' => $rank]);
        }
        if ($playerName) {
            $query->where(['Players.name LIKE' => '%'.$playerName.'%']);
        }
        if ($playerNameEn) {
            $query->where(['Players.name_english LIKE' => '%'.$playerNameEn.'%']);
        }
        if (is_numeric($joined)) {
            $query->where(["DATE_FORMAT(Players.joined, '%Y') >=" => $joined]);
        }
        if (is_numeric($enrollmentTo)) {
            $query->where(["DATE_FORMAT(Players.joined, '%Y') <=" => $enrollmentTo]);
        }
        if ($retire && $retire === 'false') {
            $query->where(['Players.is_retired' => 0]);
        }

        // データを取得
        $res = $query->order([
            'Ranks.rank_numeric DESC',
            'Players.joined',
            'Players.id'
        ])->contain([
            'PlayerScores' => function ($q) {
                return $q->where(['PlayerScores.target_year' => intval(Time::now()->year)]);
            },
            'Ranks',
            'Countries'
        ]);
        if ($count) {
            return $res->count();
        }
        return $res->all();
    }
}
