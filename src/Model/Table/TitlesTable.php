<?php

namespace Gotea\Model\Table;

use Cake\ORM\Query;
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
     * {@inheritdoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->setDisplayField('name');

        // タイトル保持情報
        $this->hasMany('RetentionHistories')
            ->setSort([
                'RetentionHistories.target_year' => 'DESC',
                'RetentionHistories.holding' => 'DESC'
            ]);
        // 所属国マスタ
        $this->belongsTo('Countries')
            ->setJoinType('INNER');
    }

    /**
     * {@inheritdoc}
     */
    public function validationDefault(Validator $validator)
    {
        return $validator
            ->requirePresence([
                'country_id', 'name', 'name_english', 'holding',
                'sort_order', 'html_file_name', 'html_file_modified',
            ], 'create')
            ->notEmpty([
                'name', 'name_english', 'holding',
                'html_file_name', 'html_file_modified',
            ])
            ->integer('holding')
            ->integer('sort_order')
            ->alphaNumeric('name_english')
            ->add('html_file_name', 'custom', [
                'rule' => function ($value) {
                    return (bool)preg_match('/^[a-zA-Z0-9\(\)\'\-\/\s]+$/', $value);
                },
            ])
            ->maxLength('name', 30)
            ->maxLength('name_english', 60)
            ->maxLength('html_file_name', 30)
            ->date('html_file_modified', 'y/m/d');
    }

    /**
     * IDに合致する棋士と関連データを取得します。
     *
     * @param int $id 検索キー
     * @return \Gotea\Model\Entity\Title 棋士と関連データ
     * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException
     */
    public function findByIdWithRelation(int $id)
    {
        return $this->get($id, [
            'contain' => [
                'Countries',
                'RetentionHistories',
            ],
        ]);
    }

    /**
     * 有効なタイトルをID・名前のリストで取得します。
     *
     * @return \Cake\ORM\Query 生成されたクエリ
     */
    public function findSortedList()
    {
        return $this->find('list')->order(['country_id', 'id']);
    }

    /**
     * タイトル情報を取得します。
     *
     * @param array $data パラメータ
     * @return \Cake\ORM\Query 生成したクエリ
     */
    public function findTitles($data = [])
    {
        $query = $this->find()->contain([
            'Countries',
            'RetentionHistories' => function (Query $q) {
                return $q->where(['RetentionHistories.holding = Titles.holding']);
            },
            'RetentionHistories.Titles',
            'RetentionHistories.Players',
            'RetentionHistories.Ranks'
        ]);

        // 所属国があれば条件追加
        if (($countryId = Hash::get($data, 'country_id', ''))) {
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
        return $query->order(['Titles.country_id', 'Titles.sort_order']);
    }

    /**
     * 配列からモデルデータを生成します。
     *
     * @param int|null $id ID
     * @param array $data パラメータ
     * @return \Gotea\Model\Entity\Title
     */
    public function createEntity($id = null, $data = []): Title
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
