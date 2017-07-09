<?php

namespace App\Model\Table;

use App\Model\Entity\Country;
use App\Model\Entity\Player;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
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
        // 昇段情報
        $this->hasMany('PlayerRanks');
        // 棋士成績
        $this->hasMany('PlayerScores');
        // 保持履歴
        $this->hasMany('RetentionHistories');
        // タイトル成績
        $this->hasMany('WinDetails', [
            'className' => 'TitleScoreDetails',
            'conditions' => [
                'WinDetails.division' => '勝'
            ]
        ]);
        $this->hasMany('LoseDetails', [
            'className' => 'TitleScoreDetails',
            'conditions' => [
                'LoseDetails.division' => '敗'
            ]
        ]);
        $this->hasMany('DrawDetails', [
            'className' => 'TitleScoreDetails',
            'conditions' => [
                'DrawDetails.division' => '分'
            ]
        ]);
        $this->hasMany('WorldWinDetails', [
            'className' => 'TitleScoreDetails',
            'conditions' => [
                'WorldWinDetails.division' => '勝'
            ]
        ]);
        $this->hasMany('WorldLoseDetails', [
            'className' => 'TitleScoreDetails',
            'conditions' => [
                'WorldLoseDetails.division' => '敗',
            ]
        ]);
        $this->hasMany('WorldDrawDetails', [
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
     * @param int $id
     * @return Player|null 棋士とそれに紐づく棋士成績
     */
    public function findPlayerWithScores(int $id)
    {
        return $this->find()->contain(['PlayerScores' => function(Query $q) {
            return $q->orderDesc('PlayerScores.target_year');
        }])->where(['id' => $id])->first();
    }

    /**
     * 棋士情報に関する一式を取得します。
     *
     * @param int $id
     * @return Player|null 棋士情報
     */
    public function findWithRelations(int $id)
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
            'PlayerRanks.Ranks' => function (Query $q) {
                return $q->orderDesc('Ranks.rank_numeric');
            },
            'RetentionHistories.Titles.Countries' => function (Query $q) {
                return $q->order([
                    'RetentionHistories.target_year' => 'DESC',
                    'Titles.country_id' => 'ASC',
                    'Titles.sort_order' => 'ASC'
                ]);
            },
        ])->where(['Players.id' => $id])->first();
    }

    /**
     * 指定条件に合致した棋士情報を取得します。
     *
     * @param array $data
     * @return Query 生成されたクエリ
     */
    public function findPlayersQuery(array $data) : Query
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
        if (($countryId = $data['country_id'] ?? '')) {
            $query->where(['Countries.id' => $countryId]);
        }
        if (($organizationId = $data['organization_id'] ?? '')) {
            $query->where(['Organizations.id' => $organizationId]);
        }
        if (($rankId = $data['rank_id'] ?? '') !== '') {
            $query->where(['Ranks.id' => $rankId]);
        }
        if (($sex = $data['sex'] ?? '')) {
            $query->where(['Players.sex' => $sex]);
        }
        if (($name = trim($data['name'] ?? ''))) {
            $query->where(['OR' => $this->__createLikeParams('name', $name)]);
        }
        if (($nameEnglish = trim($data['name_english'] ?? ''))) {
            $query->where(['OR' => $this->__createLikeParams('name_english', $nameEnglish)]);
        }
        if (($nameOther = trim($data['name_other'] ?? ''))) {
            $query->where(['OR' => $this->__createLikeParams('name_other', $nameOther)]);
        }
        if (is_numeric(($joinedFrom = $data['joined_from'] ?? ''))) {
            $query->where(['SUBSTR(Players.joined, 1, 4) >=' => $joinedFrom]);
        }
        if (is_numeric(($joinedTo = $data['joined_to'] ?? ''))) {
            $query->where(['SUBSTR(Players.joined, 1, 4) <=' => $joinedTo]);
        }
        if (!($data['is_retired'] ?? false)) {
            $query->where(['Players.is_retired' => 0]);
        }

        // クエリを返却
        return $query;
    }

    /**
     * ランキング集計データを取得します。
     *
     * @param \App\Model\Entity\Country $country
     * @param int $targetYear
     * @param int $offset
     * @return array ランキング集計データ
     */
    public function findRanking(Country $country, int $targetYear, int $offset)
    {
        // 旧方式
        if ($this->_isOldRanking($targetYear)) {
            return $this->__findOldRanking($country, $targetYear, $offset);
        }

        $query = $this->find();
        $query->select([
                'id', 'name', 'name_english', 'country_id', 'rank_id', 'sex',
                'win' => 'win.cnt',
                'lose' => $query->func()->coalesce(['lose.cnt' => 'identifier', 0]),
                'draw' => $query->func()->coalesce(['draw.cnt' => 'identifier', 0]),
            ])
            ->innerJoin(['win' => $this->__createSub($country, $targetYear, '勝')], ['id = win.player_id'])
            ->leftJoin(['lose' => $this->__createSub($country, $targetYear, '敗')], ['id = lose.player_id'])
            ->leftJoin(['draw' => $this->__createSub($country, $targetYear, '分')], ['id = draw.player_id'])
            ->where(['win.cnt >= ' =>
                $this->query()->select([
                    'cnt' => 'coalesce(sum(target.cnt), 1)'
                ])->from(['target' => $this->__createSub($country, $targetYear, '勝')
                ->orderDesc('cnt')->limit(1)->offset($offset - 1)])])
            ->orderDesc('win')->order(['lose', 'joined']);

        if ($country->has_title) {
            $query->where(['country_id' => $country->id]);
        }

        return $query->all();
    }

    /**
     * ランキングモデルを配列に変換します。
     *
     * @param Country $country
     * @param \Cake\ORM\ResultSet $models
     * @param bool $showJp
     * @return array ランキングモデルの配列
     */
    public function toRankingArray(Country $country, $models, $showJp) : array
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
                'playerName' => $model->getRankingName($country),
                'winPoint' => (int) $model->win,
                'losePoint' => (int) $model->lose,
                'drawPoint' => (int) $model->draw,
                'winPercentage' => (!$sum ? 0 : round($model->win / $sum, 2)),
            ];

            // 日本語の情報を表示するか
            if ($showJp) {
                $row['playerId'] = $model->id;
                $row['playerNameJp'] = $model->getRankingName($country, true);
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
    private function __createSub(Country $country, int $targetYear, string $division) : Query
    {
        $titleScoreDetails = TableRegistry::get('TitleScoreDetails');
        $subQuery = $titleScoreDetails->find()
                ->select(['player_id' => 'player_id', 'cnt' => 'count(*)'])
                ->contain([
                    'TitleScores' => function(Query $q) use ($country, $targetYear) {
                        // タイトルがない所属国の場合、国際棋戦のみ対象とする
                        if (!$country->has_title) {
                            $q->where(['is_world' => true]);
                        }
                        return $q->where(['YEAR(started)' => $targetYear]);
                    }
                ])
                ->where(['division' => $division])->group('player_id');

        if (!$country->has_title) {
            return $subQuery;
        }

        $subQuery->innerJoinWith('Players', function(Query $q) use ($country) {
            return $q->where(['Players.country_id' => $country->id]);
        });

        return $subQuery;
    }

    /**
     * LIKE検索用のWHERE句を生成します。
     *
     * @param string $fieldName
     * @param string $input
     * @return array
     */
    private function __createLikeParams(string $fieldName, string $input) : array
    {
        $whereClause = [];
        $params = explode(" ", $input);
        foreach ($params as $param) {
            array_push($whereClause, ["Players.{$fieldName} LIKE" => "%{$param}%"]);
        }
        return $whereClause;
    }

    /**
     * ランキング集計データを取得します。
     * ※2016年以前の集計です。
     *
     * @param Country $country
     * @param int $targetYear
     * @param int $offset
     * @return void
     */
    private function __findOldRanking(Country $country, int $targetYear, int $offset)
    {
        $suffix = ($country->has_title ? '' : '_world');

        // サブクエリ
        $scores = TableRegistry::get('PlayerScores');
        $subQuery = $scores->find()
                ->select('PlayerScores.win_point'.$suffix)
                ->innerJoinWith('Players')->innerJoinWith('Players.Countries')
                ->where([
                    'PlayerScores.target_year' => $targetYear,
                ])->orderDesc('PlayerScores.win_point'.$suffix)->order('PlayerScores.lose_point'.$suffix)
                ->limit(1)->offset($offset - 1);

        if ($country->has_title) {
            $subQuery->where(['Countries.id' => $country->id]);
        }

        $query = $this->find()
            ->innerJoinWith('PlayerScores')
            ->select([
                'Players.id', 'Players.name', 'Players.name_english', 'Players.sex',
                'win' => 'PlayerScores.win_point'.$suffix,
                'lose' => 'PlayerScores.lose_point'.$suffix,
                'draw' => 'PlayerScores.draw_point'.$suffix,
                'Countries.name', 'Ranks.rank_numeric', 'Ranks.name'])
            ->contain([
                'Countries',
                'Ranks',
            ])->where(function($exp, $q) use ($subQuery, $suffix) {
                return $exp->gte('PlayerScores.win_point'.$suffix, $subQuery);
            })->where([
                'PlayerScores.target_year' => $targetYear,
            ])->orderDesc('PlayerScores.win_point'.$suffix)
            ->order('PlayerScores.lose_point'.$suffix)
            ->orderDesc('Ranks.rank_numeric')
            ->order('Players.joined');

        if ($country->has_title) {
            $query->where(['country_id' => $country->id]);
        }

        return $query->all();
    }
}
