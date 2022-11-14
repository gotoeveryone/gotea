<?php
declare(strict_types=1);

namespace Gotea\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\Validation\Validator;
use Gotea\Model\Entity\Title;

/**
 * タイトル
 */
class TitlesTable extends AppTable
{
    /**
     * @inheritDoc
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setDisplayField('name');

        // タイトル保持情報
        $this->hasMany('RetentionHistories')
            ->setSort([
                'RetentionHistories.target_year' => 'DESC',
                'RetentionHistories.holding' => 'DESC',
            ]);
        // 所属国マスタ
        $this->belongsTo('Countries')
            ->setJoinType('INNER');
    }

    /**
     * @inheritDoc
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('country_id')
            ->requirePresence('country_id', 'create')
            ->notEmptyString('country_id');

        $validator
            ->scalar('name')
            ->maxLength('name', 30)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('name_english')
            ->maxLength('name_english', 60)
            ->requirePresence('name_english', 'create')
            ->notEmptyString('name_english')
            ->nameEnglish('name_english');

        $validator
            ->integer('holding')
            ->maxLength('holding', 3)
            ->requirePresence('holding', 'create')
            ->notEmptyString('holding');

        $validator
            ->integer('sort_order')
            ->maxLength('sort_order', 2)
            ->requirePresence('sort_order', 'create')
            ->notEmptyString('sort_order');

        $validator
            ->integer('html_file_holding')
            ->maxLength('html_file_holding', 3)
            ->allowEmptyString('html_file_holding');

        $validator
            ->scalar('html_file_name')
            ->maxLength('html_file_name', 30)
            ->requirePresence('html_file_name', 'create')
            ->notEmptyString('html_file_name')
            ->add('html_file_name', 'custom', [
                'rule' => function ($value) {
                    return (bool)preg_match('/^[a-zA-Z0-9\-]+$/', $value);
                },
            ]);

        $validator
            ->date('html_file_modified', ['y/m/d'])
            ->requirePresence('html_file_modified', 'create')
            ->notEmptyDate('html_file_modified');

        $validator
            ->scalar('remarks')
            ->maxLength('remarks', 500)
            ->allowEmptyString('remarks');

        $validator
            ->boolean('is_team')
            ->notEmptyString('is_team');

        $validator
            ->boolean('is_closed')
            ->notEmptyString('is_closed');

        $validator
            ->boolean('is_output')
            ->notEmptyString('is_output');

        $validator
            ->boolean('is_official')
            ->notEmptyString('is_official');

        return $validator;
    }

    /**
     * @inheritDoc
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['country_id'], 'Countries'));

        return $rules;
    }

    /**
     * IDに合致する棋士と関連データを取得します。
     *
     * @param int $id 検索キー
     * @return \Gotea\Model\Entity\Title 棋士と関連データ
     * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException
     */
    public function findByIdWithRelation(int $id): Title
    {
        return $this->get($id, [
            'contain' => [
                'Countries',
                'RetentionHistories',
                'RetentionHistories.Players',
                'RetentionHistories.Players.PlayerRanks.Ranks',
                'RetentionHistories.Countries',
            ],
        ]);
    }

    /**
     * 有効なタイトルをID・名前のリストで取得します。
     *
     * @return \Cake\ORM\Query 生成されたクエリ
     */
    public function findSortedList(): Query
    {
        return $this->find('list')->order(['country_id', 'id']);
    }

    /**
     * タイトル情報を取得します。
     *
     * @param array $data パラメータ
     * @return \Cake\ORM\Query 生成したクエリ
     */
    public function findTitles(array $data = []): Query
    {
        $query = $this->find()->contain([
            'Countries',
            'RetentionHistories' => function (Query $q) {
                return $q->where(['RetentionHistories.holding = Titles.holding']);
            },
            'RetentionHistories.Titles',
            'RetentionHistories.Players',
            'RetentionHistories.Players.PlayerRanks.Ranks',
            'RetentionHistories.Countries',
        ]);

        // 所属国があれば条件追加
        $countryId = Hash::get($data, 'country_id', '');
        if ($countryId) {
            $query->where(['Countries.id' => $countryId]);
        }

        // 有効なタイトルのみ検索
        if (!Hash::get($data, 'search_closed', false)) {
            $query->where(['Titles.is_closed' => false]);
        }

        // 出力対象のみ検索
        if (!Hash::get($data, 'search_non_output', false)) {
            $query->where(['Titles.is_output' => true]);
        }

        // データを取得
        return $query->order(['Titles.country_id', 'Titles.is_closed', 'Titles.sort_order']);
    }

    /**
     * 配列からモデルデータを生成します。
     *
     * @param int|null $id ID
     * @param array $data パラメータ
     * @return \Gotea\Model\Entity\Title
     */
    public function createEntity(?int $id = null, array $data = []): Title
    {
        $properties = [];
        foreach ($data as $key => $value) {
            $name = Inflector::underscore($key);
            $properties[$name] = $value;
        }

        if ($id) {
            return $this->patchEntity($this->get($id), $properties);
        }

        return $this->newEntity($properties);
    }
}
