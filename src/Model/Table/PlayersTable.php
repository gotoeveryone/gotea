<?php

namespace App\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\Http\ServerRequest;
use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;
use Cake\I18n\Date;
use Cake\Validation\Validator;
use App\Model\Entity\Country;
use App\Model\Entity\Player;
use App\Utility\CalculatorTrait;

/**
 * 棋士
 */
class PlayersTable extends AppTable
{
    use CalculatorTrait;

    /**
     * {@inheritdoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

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
     * {@inheritdoc}
     */
    public function validationDefault(Validator $validator)
    {
        return $validator
            ->allowEmpty(['name_other', 'birthday'])
            ->notEmpty(['name', 'name_english', 'joined'])
            ->maxLength('name', 20)
            ->maxLength('name_english', 40)
            ->alphaNumeric('name_english')
            ->maxLength('name_other', 20)
            ->date('birthday', 'ymd')
            ->date('retired', 'ymd')
            ->date('joined', 'ymd', null, 'create');
    }

    /**
     * {@inheritdoc}
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(
            ['country_id', 'name', 'birthday'],
            '棋士情報がすでに存在します。'
        ));

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function save(EntityInterface $entity, $options = [])
    {
        // 引退フラグがfalseなら引退日を空欄にする
        if (!$entity->is_retired) {
            $entity->retired = null;
        }
        $save = parent::save($entity, $options);

        // 新規作成時には昇段情報も登録
        if ($save && $entity->isNew()) {
            // 入段日を登録時段位の昇段日として設定
            $promoted = Date::parseDate($entity->joined, 'yyyyMMdd');

            // 棋士昇段情報へ登録
            $playerRanks = TableRegistry::get('PlayerRanks');
            $playerRanks->save($playerRanks->newEntity([
                'player_id' => $entity->id,
                'rank_id' => $entity->rank_id,
                'promoted' => $promoted->format('Y/m/d'),
            ]));
        }

        return $save;
    }

    /**
     * IDから棋士情報を1件取得します。
     *
     * @param int $id
     * @return \App\Model\Entity\Player
     */
    public function findOne(int $id)
    {
        return $this->findById($id)
            ->contain(['Countries', 'Ranks'])
            ->firstOrFail();
    }

    /**
     * 指定条件に合致した棋士情報を取得します。
     *
     * @param ServerRequest $request
     * @return \Cake\ORM\Query 生成されたクエリ
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
            $query->where(['SUBSTR(Players.joined, 1, 4) >=' => sprintf('%04d', $joinedFrom)]);
        }
        if (is_numeric(($joinedTo = $request->getData('joined_to')))) {
            $query->where(['SUBSTR(Players.joined, 1, 4) <=' => sprintf('%04d', $joinedTo)]);
        }
        if (!($request->getData('is_retired', false))) {
            $query->where(['Players.is_retired' => 0]);
        }

        return $query;
    }

    /**
     * 段位ごとの棋士数を取得します。
     *
     * @param int $countryId
     * @param string $countryName
     * @return \Cake\ORM\Query 生成されたクエリ
     */
    public function findRanksCount($countryId = null, $countryName = null)
    {
        $query = $this->find()->contain('Ranks')->where(['is_retired' => false]);

        if ($countryId) {
            $query->where(['country_id' => $countryId]);
        }

        if ($countryName) {
            $query->innerJoinWith('Countries', function(Query $q) use ($countryName) {
                return $q->where(['Countries.name' => $countryName]);
            });
        }

        return $query->select([
            'rank' => 'Ranks.rank_numeric',
            'name' => 'Ranks.name',
            'count' => $query->func()->count('*')
        ])->group('Ranks.name')->orderDesc('Ranks.rank_numeric');
    }

    /**
     * ランキング集計データを取得します。
     *
     * @param Country $country
     * @param int $targetYear
     * @param int $offset
     * @param bool $withJa
     * @return \Cake\Collection\Collection ランキング
     */
    public function findRanking(Country $country, int $targetYear, int $offset, bool $withJa)
    {
        // 旧方式
        if ($this->_isOldRanking($targetYear)) {
            return $this->__findOldRanking($country, $targetYear, $offset, $withJa);
        }

        $query = $this->find();
        $query->select([
                'id', 'name', 'name_english', 'country_id', 'rank_id', 'sex',
                'win' => 'win.cnt',
                'lose' => $query->func()->coalesce(['lose.cnt' => 'identifier', 0 => 'literal']),
                'draw' => $query->func()->coalesce(['draw.cnt' => 'identifier', 0 => 'literal']),
            ])->select($this->Countries)->select($this->Ranks)
            ->innerJoin(['win' => $this->__createSub($country, $targetYear, '勝')], ['id = win.player_id'])
            ->leftJoin(['lose' => $this->__createSub($country, $targetYear, '敗')], ['id = lose.player_id'])
            ->leftJoin(['draw' => $this->__createSub($country, $targetYear, '分')], ['id = draw.player_id'])
            ->contain(['Countries', 'Ranks'])
            ->where(['win.cnt >= ' =>
                $this->query()->select([
                    'cnt' => 'coalesce(sum(target.cnt), 1)'
                ])->from(['target' => $this->__createSub($country, $targetYear, '勝')
                ->orderDesc('cnt')->limit(1)->offset($offset - 1)])])
            ->orderDesc('win')->order(['lose', 'joined']);

        if (!$country->isWorlds()) {
            $query->where(['country_id' => $country->id]);
        }

        return $this->__mapped($query->all(), $country->isWorlds(), $withJa);
    }

    /**
     * ランキングモデルを配列に変換します。
     *
     * @param ResultSet $models
     * @param bool $isWorlds 国際棋戦かどうか
     * @param bool $withJa 日本語情報を表示するかどうか
     * @return \Cake\Collection\Collection ランキング
     */
    private function __mapped(ResultSet $models, bool $isWorlds, bool $withJa)
    {
        $rank = 0;
        $win = 0;

        return $models->map(function ($item, $key) use ($isWorlds, $withJa, &$rank, &$win) {
            $sum = $item->win + $item->lose;
            if ($win !== $item->win) {
                $rank = $key + 1;
                $win = $item->win;
            }

            $row = [
                'rank' => $rank,
                'name' => $item->getRankingName($isWorlds, $withJa),
                'win' => (int) $item->win,
                'lose' => (int) $item->lose,
                'draw' => (int) $item->draw,
                'percentage' => $this->percent($item->win, $item->lose),
            ];

            // 日本語出力あり
            if ($withJa) {
                $row['id'] = $item->id;
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
     * @return Query
     */
    private function __createSub(Country $country, int $targetYear, string $division)
    {
        $titleScoreDetails = TableRegistry::get('TitleScoreDetails');
        $subQuery = $titleScoreDetails->find()
                ->select(['player_id' => 'player_id', 'cnt' => 'count(*)'])
                ->contain([
                    'TitleScores' => function (Query $q) use ($country, $targetYear) {
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

        return $subQuery->innerJoinWith('Players', function (Query $q) use ($country) {
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
    private function __createLikeParams(string $fieldName, string $input)
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
     * @param bool $withJa
     * @return \Cake\Collection\Collection
     */
    private function __findOldRanking(Country $country, int $targetYear, int $offset, bool $withJa)
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
            ->innerJoinWith('PlayerScores.Ranks')
            ->select([
                'Players.id', 'Players.name', 'Players.name_english', 'Players.sex',
                'win' => 'PlayerScores.win_point'.$suffix,
                'lose' => 'PlayerScores.lose_point'.$suffix,
                'draw' => 'PlayerScores.draw_point'.$suffix,
                'country_name' => 'Countries.name', 'country_name_english' => 'Countries.name_english',
                'rank_name' => 'Ranks.name', 'rank_numeric' => 'Ranks.rank_numeric',
            ])->contain([
                'Countries',
                'PlayerScores.Ranks'
            ])->where(function ($exp, $q) use ($subQuery, $suffix) {
                return $exp->gte('PlayerScores.win_point'.$suffix, $subQuery);
            })->where([
                'PlayerScores.target_year' => $targetYear,
            ])->orderDesc('PlayerScores.win_point'.$suffix)
            ->order('PlayerScores.lose_point'.$suffix)
            ->orderDesc('Ranks.rank_numeric')
            ->order('Players.joined');

        if (!$country->isWorlds()) {
            $query->where(['country_id' => $country->id]);
        }

        return $this->__mapped($query->all(), $country->isWorlds(), $withJa);
    }
}
