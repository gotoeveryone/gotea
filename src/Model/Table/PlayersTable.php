<?php

namespace App\Model\Table;

use App\Model\Entity\Player;
use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\ORM\Query;
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
        // 国
        $this->belongsTo('Countries');
        // 段位
        $this->belongsTo('Ranks');
        // 組織
        $this->belongsTo('Organizations');
        // 棋士成績
        $this->hasMany('PlayerScores', [
            'order' => array('PlayerScores.target_year' => 'DESC')
        ]);
        // 保持履歴
        $this->hasMany('RetentionHistories', [
            'joinType' => 'LEFT',
            'order' => array('RetentionHistories.target_year' => 'DESC')
        ]);
    }

	/**
	 * 保存前処理
     * 
     * @param Event $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     */
	public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options) {
        // 所属国
        $countries = TableRegistry::get('Countries');
        $this->country = $countries->get($entity->country_id);

        // 段位
        $ranks = TableRegistry::get('Ranks');
        $this->rank = $ranks->get($entity->rank_id);

        // 所属組織
        $organizations = TableRegistry::get('Organizations');
        $this->organization = $organizations->get($entity->organization_id);

        // 親クラスのメソッド呼び出し
        parent::beforeSave($event, $entity, $options);
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
            ->lengthBetween('name', [0, 20], '棋士名は20文字以下で入力してください。')
            ->notEmpty('name_english', '棋士名（英語）は必須です。')
            ->lengthBetween('name_english', [0, 40], '棋士名（英語）は40文字以下で入力してください。')
            ->allowEmpty('name_other')
            ->lengthBetween('name_other', [0, 20], '棋士名（その他）は20文字以下で入力してください。')
            ->allowEmpty('birthday')
            ->add('birthday', [
                'valid' => [
                    'rule' => ['date', 'ymd'],
                    'message' => '生年月日は「yyyy/MM/dd」形式で入力してください。'
                ]
            ])
            ->notEmpty('joined', '入段日は必須です。');
    }

    /**
     * 棋士とそれに紐づく棋士成績を取得します。
     * 
     * @param type $id
     * @return Player 棋士とそれに紐づく棋士成績
     */
    public function findPlayerWithScores($id) : Player
    {
        return $this->find()->contain(['PlayerScores' => function(Query $q) {
            return $q->orderDesc('PlayerScores.target_year');
        }])->where(['id' => $id])->first();
    }

    /**
     * 棋士情報に関する一式を取得します。
     * 
     * @param type $id
     * @return Player 棋士情報
     */
    public function findPlayerAllRelations($id) : Player
    {
		return $this->find()->contain([
            'Countries',
            'Ranks',
            'Organizations',
            'PlayerScores' => function (Query $q) {
                return $q->orderDesc('PlayerScores.target_year');
            },
            'PlayerScores.Ranks',
            'RetentionHistories.Titles',
            'RetentionHistories' => function (Query $q) {
                return $q->order([
                    'RetentionHistories.target_year' => 'DESC',
                    'Titles.country_id' => 'ASC',
                    'Titles.sort_order' => 'ASC'
                ]);
            },
            'RetentionHistories.Titles.Countries'
        ])->where(['Players.id' => $id])->first();
    }

    /**
     * 指定条件に合致した棋士情報を取得します。
     * 
     * @param Player $searchParams
     * @param string $joinedFrom
     * @param string $joinedTo
     * @return Player 棋士情報一覧
     */
    public function findPlayers(Player $searchParams, string $joinedFrom = null, string $joinedTo = null)
    {
        // 棋士情報の取得
        $query = $this->find();

        // 入力されたパラメータが空でなければ、WHERE句へ追加
        if (($countryId = $searchParams->country_id)) {
            $query->where(["Countries.id" => $countryId]);
        }
        if (($organizationId = $searchParams->organization_id)) {
            $query->where(["Organizations.id" => $organizationId]);
        }
        if (($rankId = $searchParams->rank_id)) {
            $query->where(["Ranks.id" => $rankId]);
        }
        if (($sex = $searchParams->sex)) {
            $query->where(["Players.sex" => $sex]);
        }
        if (($playerName = trim($searchParams->name))) {
            $query->where(["OR" => $this->__createLikeParams("name", $playerName)]);
        }
        if (($playerNameEn = trim($searchParams->name_english))) {
            $query->where(["OR" => $this->__createLikeParams("name_english", $playerNameEn)]);
        }
        if (is_numeric($joinedFrom)) {
            $query->where(["SUBSTR(Players.joined, 1, 4) >=" => $joinedFrom]);
        }
        if (is_numeric($joinedTo)) {
            $query->where(["SUBSTR(Players.joined, 1, 4) <=" => $joinedTo]);
        }
        if (!isset($searchParams->is_retired) || !$searchParams->is_retired) {
            $query->where(["Players.is_retired" => 0]);
        }

        // データを取得
        return $query->order([
            "Ranks.rank_numeric DESC", "Players.joined", "Players.id"
        ])->contain([
            "PlayerScores" => function ($q) {
                return $q->where(["PlayerScores.target_year" => intval(Time::now()->year)]);
            },
            "Ranks", "Countries", "Organizations"
        ])->all();
    }

    /**
     * LIKE検索用のWHERE句を生成します。
     * 
     * @param string $fieldName
     * @param string $input
     * @return Array
     */
    private function __createLikeParams($fieldName, $input) : Array
    {
        $whereClause = [];
        $params = explode(" ", $input);
        foreach ($params as $param) {
            array_push($whereClause, ["Players.{$fieldName} LIKE" => "%{$param}%"]);
        }
        return $whereClause;
    }
}