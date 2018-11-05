<?php

namespace Gotea\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\ResultSet;
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
            ->add('html_file_name', function ($value) {
                return (bool)preg_match('/^[a-zA-Z0-9\(\)\'\-\/\s]+$/', $value);
            })
            ->maxLength('name', 30)
            ->maxLength('name_english', 30)
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
        if (($countryId = $data['country_id'] ?? '')) {
            $query->where(['Countries.id' => $countryId]);
        }

        // 有効なタイトルのみ検索
        if (!($data['is_closed'] ?? '')) {
            $query->where(['Titles.is_closed' => false]);
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
    public function createEntity($id = null, $data = []) : Title
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
