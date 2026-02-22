<?php
declare(strict_types=1);

namespace Gotea\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\I18n\FrozenDate;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Validation\Validator;
use Gotea\Model\Entity\Player;

/**
 * 棋士
 */
class PlayersTable extends AppTable
{
    /**
     * @inheritDoc
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        // 国
        $this->belongsTo('Countries')
            ->setJoinType('INNER');
        // 段位
        $this->belongsTo('Ranks')
            ->setJoinType('INNER');
        // 組織
        $this->belongsTo('Organizations')
            ->setJoinType('INNER');
        // 昇段情報
        $this->hasMany('PlayerRanks');
        // 棋士成績
        $this->hasMany('PlayerScores');
        // 保持履歴
        $this->hasMany('RetentionHistories');
        // タイトル成績
        $this->hasMany('TitleScoreDetails')
            ->setFinder('scores');
    }

    /**
     * @inheritDoc
     */
    public function validationDefault(Validator $validator): Validator
    {
        return $validator
            ->allowEmptyString('name_other')
            ->allowEmptyString('birthday')
            ->allowEmptyString('joined')
            ->allowEmptyString('joined_month')
            ->allowEmptyString('joined_day')
            ->requirePresence('joined_year', 'create')
            ->requirePresence([
                'country_id', 'organization_id', 'rank_id',
                'name', 'name_english', 'sex',
            ], 'create')
            ->notEmptyString('joined_year')
            ->notEmptyString('country_id')
            ->notEmptyString('organization_id')
            ->notEmptyString('rank_id')
            ->notEmptyString('name')
            ->notEmptyString('name_english')
            ->notEmptyString('sex')
            ->integer('country_id')
            ->integer('organization_id')
            ->integer('rank_id')
            ->integer('joined_year')
            ->integer('joined_month')
            ->integer('joined_day')
            ->range('joined_year', [1, 9999])
            ->range('joined_month', [1, 12])
            ->range('joined_day', [1, 31])
            ->nameEnglish('name_english')
            ->maxLength('name', 20)
            ->maxLength('name_english', 40)
            ->maxLength('name_other', 20)
            ->add('joined_day', 'requireMonth', [
                'rule' => function ($value, $context) {
                    return empty($value) || !empty(Hash::get($context, 'data.joined_month'));
                },
                'message' => __('Month is required when day is selected'),
            ])
            ->date('birthday', 'y/m/d')
            ->date('retired', 'y/m/d');
    }

    /**
     * @inheritDoc
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(
            ['country_id', 'name', 'birthday'],
            __('This player already exists'),
        ));

        return $rules;
    }

    /**
     * @inheritDoc
     */
    public function save(EntityInterface $entity, $options = []): EntityInterface|false
    {
        // 引退フラグがfalseなら引退日を空欄にする
        if (!$entity->is_retired) {
            $entity->retired = null;
        }
        // 互換性維持: 分離カラムから joined(yyyymmdd) を同期
        $entity->joined = $entity->joined_ymd ?? '';
        $new = $entity->isNew();
        $save = parent::save($entity, $options);

        // 新規作成時には昇段情報も登録
        if ($save && $new) {
            // 入段日を登録時段位の昇段日として設定
            $promoted = FrozenDate::parseDate($entity->joined_ymd, 'yyyyMMdd');

            // 入段日が完全な日付だった場合、棋士昇段情報へ登録
            if ($promoted !== null) {
                $playerRanks = TableRegistry::getTableLocator()->get('PlayerRanks');
                $playerRanks->save($playerRanks->newEntity([
                    'player_id' => $entity->id,
                    'rank_id' => $entity->rank_id,
                    'promoted' => $promoted->format('Y/m/d'),
                ]));
            }
        }

        return $save;
    }

    /**
     * 指定条件に合致した棋士情報を取得します。
     *
     * @param array $data パラメータ
     * @return \Cake\ORM\Query 生成されたクエリ
     */
    public function findPlayers(array $data): Query
    {
        // 棋士情報の取得
        $query = $this->find()->orderBy([
            'Ranks.rank_numeric DESC',
            'Players.joined_year',
            'Players.joined_month',
            'Players.joined_day',
            'Players.id',
        ])->contain([
            'Ranks', 'Countries', 'Organizations', 'TitleScoreDetails',
        ]);

        // 入力されたパラメータが空でなければ、WHERE句へ追加
        $countryId = Hash::get($data, 'country_id');
        if ($countryId) {
            $query->where(['Countries.id' => $countryId]);
        }

        $organizationId = Hash::get($data, 'organization_id');
        if ($organizationId) {
            $query->where(['Organizations.id' => $organizationId]);
        }

        $rankId = Hash::get($data, 'rank_id');
        if ($rankId) {
            $query->where(['Ranks.id' => $rankId]);
        }

        $sex = Hash::get($data, 'sex');
        if ($sex) {
            $query->where(['Players.sex' => $sex]);
        }

        $name = trim(Hash::get($data, 'name', ''));
        if ($name) {
            $query->where(['OR' => $this->createLikeParams('name', $name)]);
        }

        $nameEnglish = trim(Hash::get($data, 'name_english', ''));
        if ($nameEnglish) {
            $query->where(['OR' => $this->createLikeParams('name_english', $nameEnglish)]);
        }

        $nameOther = trim(Hash::get($data, 'name_other', ''));
        if ($nameOther) {
            $query->where(['OR' => $this->createLikeParams('name_other', $nameOther)]);
        }

        $joinedFrom = Hash::get($data, 'joined_from');
        if ($joinedFrom > 0) {
            $query->where(['Players.joined_year >=' => $joinedFrom]);
        }

        $joinedTo = Hash::get($data, 'joined_to');
        if ($joinedTo) {
            $query->where(['Players.joined_year <=' => $joinedTo]);
        }

        if (!Hash::get($data, 'is_retired', false)) {
            $query->where(['Players.is_retired' => 0]);
        }

        // ソート指定があれば適用
        $fields = [
            'id',
            'joined',
            'country_id',
            'organization_id',
            'rank_id',
        ];
        $sort = Hash::get($data, 'sort');
        $direction = Hash::get($data, 'direction', 'asc');
        if ($sort && in_array(strtolower($sort), $fields, true)) {
            $direction = in_array(strtolower($direction), ['asc', 'desc'], true) ? $direction : 'asc';
            if (strtolower($sort) === 'joined') {
                $query->orderBy([
                    'Players.joined_year' => $direction,
                    'Players.joined_month' => $direction,
                    'Players.joined_day' => $direction,
                ], true);
            } else {
                $query->orderBy(["Players.{$sort}" => $direction], true);
            }
        }

        return $query;
    }

    /**
     * 段位ごとの棋士数を取得します。
     *
     * @param int $countryId 所属国ID
     * @param int|null $organizationId 所属組織ID
     * @param bool|null $includeRetired 退役者を検索するか
     * @return \Cake\ORM\Query 生成されたクエリ
     */
    public function findRanksCount(int $countryId, ?int $organizationId = null, ?bool $includeRetired = false): Query
    {
        $query = $this->find();

        if ($organizationId) {
            $query->where(['organization_id' => $organizationId]);
        }

        if (!$includeRetired) {
            $query->where(['is_retired' => false]);
        }

        return $query
            ->contain('Ranks')
            ->where(['country_id' => $countryId])
            ->select([
                'id' => 'Ranks.id',
                'rank_numeric' => 'Ranks.rank_numeric',
                'name' => 'Ranks.name',
                'count' => $query->func()->count('*'),
            ])
            ->groupBy(['Ranks.id', 'Ranks.rank_numeric', 'Ranks.name'])
            ->orderByDesc('Ranks.rank_numeric');
    }

    /**
     * IDに合致する棋士と関連データを取得します。
     *
     * @param int $id 検索キー
     * @return \Gotea\Model\Entity\Player 棋士と関連データ
     * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException
     */
    public function findByIdWithRelation(int $id): Player
    {
        return $this->get($id, contain: [
            'Countries',
            'Ranks',
            'TitleScoreDetails' => function (Query $q) {
                return $q->orderByDesc('target_year');
            },
            'PlayerRanks' => function (Query $q) {
                return $q->orderByDesc('promoted');
            },
            'PlayerRanks.Ranks',
        ]);
    }

    /**
     * 名前・所属国IDに該当する棋士データを1件取得します。
     *
     * @param array $names 名前
     * @param int $countryId 所属国ID
     * @return \Gotea\Model\Entity\Player
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function findRankByNamesAndCountries(array $names, int $countryId): Player
    {
        return $this->find()
            ->contain('Ranks')
            ->where([
                'Players.name in' => $names,
                'Players.country_id' => $countryId,
            ])
            ->firstOrFail();
    }

    /**
     * LIKE検索用のWHERE句を生成します。
     *
     * @param string $fieldName フィールド名
     * @param string $input 入力値
     * @return array
     */
    private function createLikeParams(string $fieldName, string $input): array
    {
        return collection(explode(' ', $input))
            ->map(function ($param) use ($fieldName) {
                return ["Players.{$fieldName} LIKE" => "%{$param}%"];
            })->toArray();
    }
}
