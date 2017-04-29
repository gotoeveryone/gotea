<?php

namespace App\Model\Table;

use App\Model\Entity\Country;
use App\Model\Entity\Player;
use Cake\ORM\Query;
use Cake\I18n\Date;
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
        // タイトル成績
        $this->hasMany('WinDetails', [
            'joinType' => 'INNER',
            'className' => 'TitleScoreDetails',
            'conditions' => [
                'WinDetails.division' => '勝'
            ]
        ]);
        $this->hasMany('LoseDetails', [
            'joinType' => 'LEFT',
            'className' => 'TitleScoreDetails',
            'conditions' => [
                'LoseDetails.division' => '敗'
            ]
        ]);
        $this->hasMany('DrawDetails', [
            'joinType' => 'LEFT',
            'className' => 'TitleScoreDetails',
            'conditions' => [
                'DrawDetails.division' => '分'
            ]
        ]);
        $this->hasMany('WorldWinDetails', [
            'joinType' => 'INNER',
            'className' => 'TitleScoreDetails',
            'conditions' => [
                'WorldWinDetails.division' => '勝'
            ]
        ]);
        $this->hasMany('WorldLoseDetails', [
            'joinType' => 'LEFT',
            'className' => 'TitleScoreDetails',
            'conditions' => [
                'WorldLoseDetails.division' => '敗',
            ]
        ]);
        $this->hasMany('WorldDrawDetails', [
            'joinType' => 'LEFT',
            'className' => 'TitleScoreDetails',
            'conditions' => [
                'WorldDrawDetails.division' => '分'
            ]
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
            ->allowEmpty(['name_other', 'birthday'])
            ->notEmpty('name', $this->getMessage($this->REQUIRED, '棋士名'))
            ->maxLength('name', [0, 20], $this->getMessage($this->MAX_LENGTH, ['棋士名', 20]))
            ->notEmpty('name_english', $this->getMessage($this->REQUIRED, '棋士名（英語）'))
            ->maxLength('name_english', 40, $this->getMessage($this->MAX_LENGTH, ['棋士名（英語）', 40]))
            ->add('name_english', 'default', [
                'rule' => [$this, 'alphaNumeric'],
                'message' => $this->getMessage($this->ALPHA_NUMERIC, '棋士名（英語）')
            ])
            ->maxLength('name_other', 20, $this->getMessage($this->MAX_LENGTH, ['棋士名（その他）', 20]))
            ->date('birthday', 'ymd', $this->getMessage($this->MAX_LENGTH, ['生年月日', 'yyyy/MM/dd']))
            ->notEmpty('joined', $this->getMessage($this->REQUIRED, '入段日'));
    }

    /**
     * 棋士とそれに紐づく棋士成績を取得します。
     * 
     * @param type $id
     * @return type 棋士とそれに紐づく棋士成績
     */
    public function findPlayerWithScores($id)
    {
        return $this->find()->contain(['PlayerScores' => function(Query $q) {
            return $q->orderDesc('PlayerScores.target_year');
        }])->where(['id' => $id])->first();
    }

    /**
     * 棋士情報に関する一式を取得します。
     * 
     * @param type $id
     * @return type 棋士情報
     */
    public function getInner($id)
    {
		return $this->find()->contain([
            'WinDetails' => function(Query $q) {
                return $q->select(['player_id', 'year' => 'YEAR(started)', 'cnt' => 'count(*)'])
                    ->contain(['TitleScores'])->group(['WinDetails.player_id', 'YEAR(started)']);
            },
            'LoseDetails' => function(Query $q) {
                return $q->select(['player_id', 'year' => 'YEAR(started)', 'cnt' => 'count(*)'])
                    ->contain(['TitleScores'])->group(['LoseDetails.player_id', 'YEAR(started)']);
            },
            'DrawDetails' => function(Query $q) {
                return $q->select(['player_id', 'year' => 'YEAR(started)', 'cnt' => 'count(*)'])
                    ->contain(['TitleScores'])->group(['DrawDetails.player_id', 'YEAR(started)']);
            },
            'WorldWinDetails' => function(Query $q) {
                return $q->select(['player_id', 'year' => 'YEAR(started)', 'cnt' => 'count(*)'])
                    ->contain(['TitleScores' => function(Query $q) {
                        return $q->where(['is_world' => true]);
                    }])->group(['WorldWinDetails.player_id', 'YEAR(started)']);
            },
            'WorldLoseDetails' => function(Query $q) {
                return $q->select(['player_id', 'year' => 'YEAR(started)', 'cnt' => 'count(*)'])
                    ->contain(['TitleScores' => function(Query $q) {
                        return $q->where(['is_world' => true]);
                    }])->group(['WorldLoseDetails.player_id', 'YEAR(started)']);
            },
            'WorldDrawDetails' => function(Query $q) {
                return $q->select(['player_id', 'year' => 'YEAR(started)', 'cnt' => 'count(*)'])
                    ->contain(['TitleScores' => function(Query $q) {
                        return $q->where(['is_world' => true]);
                    }])->group(['WorldDrawDetails.player_id', 'YEAR(started)']);
            },
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
     * @param array $data
     * @param boolean $isCount
     * @return Player|int 棋士情報一覧|件数
     */
    public function findPlayers(array $data, $isCount = false)
    {
        // 棋士情報の取得
        $query = $this->find()->order([
            'Ranks.rank_numeric DESC', 'Players.joined', 'Players.id'
        ])->contain([
            'PlayerScores' => function (Query $q) {
                return $q->where(['PlayerScores.target_year' => intval(Date::now()->year)]);
            },
            'Ranks', 'Countries', 'Organizations'
        ]);

        // 入力されたパラメータが空でなければ、WHERE句へ追加
        if (isset($data['country_id']) && ($countryId = $data['country_id'])) {
            $query->where(['Countries.id' => $countryId]);
        }
        if (isset($data['organization_id']) && ($organizationId = $data['organization_id'])) {
            $query->where(['Organizations.id' => $organizationId]);
        }
        if (isset($data['rank_id']) && ($rankId = $data['rank_id'])) {
            $query->where(['Ranks.id' => $rankId]);
        }
        if (isset($data['sex']) && ($sex = $data['sex'])) {
            $query->where(['Players.sex' => $sex]);
        }
        if (isset($data['name']) && ($name = trim($data['name']))) {
            $query->where(['OR' => $this->__createLikeParams('name', $name)]);
        }
        if (isset($data['name_english']) && ($nameEnglish = trim($data['name_english']))) {
            $query->where(['OR' => $this->__createLikeParams('name_english', $nameEnglish)]);
        }
        if (isset($data['name_other']) && ($nameOther = trim($data['name_other']))) {
            $query->where(['OR' => $this->__createLikeParams('name_other', $nameOther)]);
        }
        if (isset($data['joined_from']) && is_numeric(($joinedFrom = $data['joined_from']))) {
            $query->where(['SUBSTR(Players.joined, 1, 4) >=' => $joinedFrom]);
        }
        if (isset($data['joined_to']) && is_numeric(($joinedTo = $data['joined_to']))) {
            $query->where(['SUBSTR(Players.joined, 1, 4) <=' => $joinedTo]);
        }
        if (!isset($data['is_retired']) || !$data['is_retired']) {
            $query->where(['Players.is_retired' => 0]);
        }

        if ($isCount) {
            return $query->count();
        }

        // データを取得
        return $query->all();
    }

    /**
     * ランキング集計データを取得します。
     * 
     * @param \App\Model\Entity\Country $country
     * @param int $targetYear
     * @param int $offset
     * @return object ランキング集計データ
     */
    public function findRanking(Country $country, int $targetYear, int $offset)
    {
        $query = $this->find()
                ->select([
                    'id', 'name', 'name_english', 'country_id', 'rank_id', 'sex',
                    'win' => 'win.cnt', 'lose' => 'coalesce(lose.cnt, 0)', 'draw' => 'coalesce(draw.cnt, 0)'])
                ->innerJoin(['win' => $this->__createSub($country, $targetYear, '勝')], ['id = win.player_id'])
                ->leftJoin(['lose' => $this->__createSub($country, $targetYear, '敗')], ['id = lose.player_id'])
                ->leftJoin(['draw' => $this->__createSub($country, $targetYear, '分')], ['id = draw.player_id'])
                ->where(['win.cnt >= ' => $this->query()->select(['cnt' => 'coalesce(sum(target.cnt), 1)'])
                        ->from(['target' => $this->__createSub($country, $targetYear, '勝')->orderDesc('cnt')->limit(1)->offset($offset - 1)])])
                ->orderDesc('win')->order(['lose', 'joined']);

        if ($country->has_title) {
            $query->where(['country_id' => $country->id]);
        }

        return $query->all();
    }

    /**
     * ランキングモデルを配列に変換します。
     * 
     * @param type $models
     * @param bool $isJp
     * @return array
     */
    public function toRankingArray($models, $isJp) : array
    {
        $res = [];
        $rank = 0;
        $win = 0;
        foreach ($models as $key => $model) {
            $sum = $model->win + $model->lose;
            if ($win !== $model->win) {
                $rank = $key + 1;
                $win = $model->win;
            }

            $row = [
                'rank' => $rank,
                'playerName' => $model->name_english.'('.$model->rank->rank_numeric.' dan)',
                'winPoint' => (int) $model->win,
                'losePoint' => (int) $model->lose,
                'drawPoint' => (int) $model->draw,
                'winPercentage' => (!$sum ? 0 : round($model->win / $sum, 2)),
            ];

            if ($isJp) {
                $row['playerId'] = $model->id;
                $row['playerNameJp'] = $model->getNameWithRank();
                $row['sex'] = $model->sex;
            }
            $res[] = $row;
        }

        return $res;
    }

    /**
     * サブクエリを作成します。
     * 
     * @param \App\Model\Table\App\Model\Entity\Country $country
     * @param int $targetYear
     * @param string $division
     * @return \Cake\Database\Query
     */
    private function __createSub(Country $country, int $targetYear, string $division) : \Cake\Database\Query
    {
        $titleScoreDetails = \Cake\ORM\TableRegistry::get('TitleScoreDetails');
        $subQuery = $titleScoreDetails->find()
                ->select(['player_id' => 'player_id', 'cnt' => 'count(*)'])
                ->contain([
                    'TitleScores' => function(\Cake\Database\Query $q) use ($country, $targetYear) {
                        return $q->where(['TitleScores.country_id' => $country->id])->orWhere(['is_world' => true])->where(['YEAR(started)' => $targetYear]);
                    }
                ])
                ->where(['division' => $division])->group('player_id');

        if (!$country->has_title) {
            return $subQuery;
        }

        $subQuery->innerJoinWith('Players', function(\Cake\Database\Query $q) use ($country) {
            return $q->where(['Players.country_id' => $country->id]);
        });

        return $subQuery;
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