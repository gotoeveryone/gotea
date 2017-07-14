<?php

namespace App\Model\Table;

use Cake\Http\ServerRequest;
use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\ORM\TableRegistry;
use Cake\I18n\Date;
use Cake\Validation\Validator;
use App\Model\Entity\Country;
use App\Model\Entity\Player;

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
            ->date('birthday', 'ymd', $this->getMessage($this->INLALID_FORMAT, ['生年月日', 'yyyy/MM/dd']))
            ->notEmpty('joined', $this->getMessage($this->REQUIRED, '入段日'))
            ->date('joined', 'ymd', $this->getMessage($this->INLALID_FORMAT, ['入段日', 'yyyy/MM/dd']), function($context) {
                return empty($context['data']['id']);
            });
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
            'Countries',
            'Ranks',
            'Organizations',
            'PlayerScores' => function (Query $q) {
                return $q->orderDesc('PlayerScores.target_year');
            },
            'PlayerScores.Ranks',
            'PlayerRanks' => function (Query $q) {
                return $q->orderDesc('Ranks.rank_numeric');
            },
            'PlayerRanks.Ranks',
            'RetentionHistories' => function (Query $q) {
                return $q->order([
                    'RetentionHistories.target_year' => 'DESC',
                    'Titles.country_id' => 'ASC',
                    'Titles.sort_order' => 'ASC'
                ]);
            },
            'RetentionHistories.Titles.Countries',
        ])->where(['Players.id' => $id])->first();
    }

    /**
     * 指定条件に合致した棋士情報を取得します。
     *
     * @param ServerRequest $request
     * @return Query 生成されたクエリ
     */
    public function findPlayersQuery(ServerRequest $request) : Query
    {
        // 棋士情報の取得
        $query = $this->find()->order([
            'Ranks.rank_numeric DESC', 'Players.joined', 'Players.id'
        ])->contain([
            'Ranks', 'Countries', 'Organizations'
        ]);

        // 入力されたパラメータが空でなければ、WHERE句へ追加
        if (is_numeric($countryId = $request->getData('country_id'))) {
            $query->where(['Countries.id' => $countryId]);
        }
        if (is_numeric($organizationId = $request->getData('organization_id'))) {
            $query->where(['Organizations.id' => $organizationId]);
        }
        if (is_numeric($rankId = $request->getData('rank_id'))) {
            $query->where(['Ranks.id' => $rankId]);
        }
        if (($sex = $request->getData('sex'))) {
            $query->where(['Players.sex' => $sex]);
        }
        if (($name = trim($request->getData('name')))) {
            $query->where(['OR' => $this->__createLikeParams('name', $name)]);
        }
        if (($nameEnglish = trim($request->getData('name_english')))) {
            $query->where(['OR' => $this->__createLikeParams('name_english', $nameEnglish)]);
        }
        if (($nameOther = trim($request->getData('name_other')))) {
            $query->where(['OR' => $this->__createLikeParams('name_other', $nameOther)]);
        }
        if (is_numeric(($joinedFrom = $request->getData('joined_from')))) {
            $query->where(['Players.joined >=' => str_pad($joinedFrom, 4, 0, STR_PAD_LEFT).'0101']);
        }
        if (is_numeric(($joinedTo = $request->getData('joined_to')))) {
            $query->where(['Players.joined <=' => str_pad($joinedTo, 4, 0, STR_PAD_LEFT).'1231']);
        }
        if (!($request->getData('is_retired', false))) {
            $query->where(['Players.is_retired' => 0]);
        }

        return $query;
    }

    /**
     * ランキング集計データを取得します。
     *
     * @param Country $country
     * @param int $targetYear
     * @param int $offset
     * @param bool $admin
     * @return Collection ランキング集計データ
     */
    public function findRanking(Country $country, int $targetYear, int $offset, bool $admin)
    {
        // 旧方式
        if ($this->_isOldRanking($targetYear)) {
            return $this->__findOldRanking($country, $targetYear, $offset, $admin);
        }

        $query = $this->find();
        $query->select([
                'id', 'name', 'name_english', 'country_id', 'rank_id', 'sex',
                'win' => 'win.cnt',
                'lose' => $query->func()->coalesce(['lose.cnt' => 'identifier', 0 => 'literal']),
                'draw' => $query->func()->coalesce(['draw.cnt' => 'identifier', 0 => 'literal']),
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

        return $this->__mapped($query->all(), $country, $admin);
    }

    /**
     * データを追加します。
     *
     * @param array $data
     * @return \App\Model\Entity\Player|false データが登録できればそのEntity
     */
    public function add(array $data)
    {
		return $this->_addEntity($data, [
            'country_id', 'name', 'birthday',
        ]);
    }

    /**
     * ランキングモデルを配列に変換します。
     *
     * @param ResultSet $models
     * @param Country $country
     * @param bool $admin
     * @return Collection ランキング
     */
    private function __mapped(ResultSet $models, Country $country, bool $admin)
    {
        $this->__rank = 0;
        $this->__win = 0;

        return $models->map(function($item, $key) use ($country, $admin) {
            $sum = $item->win + $item->lose;
            if ($this->__win !== $item->win) {
                $this->__rank = $key + 1;
                $this->__win = $item->win;
            }

            $row = [
                'rank' => $this->__rank,
                'playerName' => $item->getRankingName($country),
                'winPoint' => (int) $item->win,
                'losePoint' => (int) $item->lose,
                'drawPoint' => (int) $item->draw,
                'winPercentage' => (!$sum ? 0 : round($item->win / $sum, 2)),
            ];

            // 管理者
            if ($admin) {
                $row['playerId'] = $item->id;
                $row['playerNameJp'] = $item->getRankingName($country, true);
                $row['sex'] = $item->sex;
            }

            return $row;
        });
    }

    /**
     * サブクエリを作成します。
     *
     * @param Country $country
     * @param int $targetYear
     * @param string $division
     * @return \Cake\Database\Query
     */
    private function __createSub(Country $country, int $targetYear, string $division)
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

        return $subQuery->innerJoinWith('Players', function(Query $q) use ($country) {
            return $q->where(['Players.country_id' => $country->id]);
        });
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
     * @param bool $admin
     * @return Collection
     */
    private function __findOldRanking(Country $country, int $targetYear, int $offset, bool $admin)
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

        return $this->__mapped($query->all(), $country, $admin);
    }
}
