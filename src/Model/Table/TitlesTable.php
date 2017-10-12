<?php

namespace Gotea\Model\Table;

use Cake\Validation\Validator;
use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\Utility\Inflector;
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
            ->notEmpty(['name', 'name_english', 'holding',
                'sort_order', 'html_flle_name', 'html_file_modified'])
            ->alphaNumeric('name_english')
            ->numeric('holding')
            ->numeric('sort_order')
            ->alphaNumeric('html_file_name')
            ->date('html_file_modified', 'ymd');
    }

    /**
     * タイトル情報を取得します。
     *
     * @param array $data
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
     * @param array $data
     * @return \Gotea\Model\Entity\Title
     */
    public function createEntity($data = []) : Title
    {
        $id = $data['id'] ?? null;

        $properties = [];
        foreach ($data as $key => $value) {
            $name = Inflector::underscore($key);
            $properties[$name] = $value;
        }

        if ($id) {
            $title = $this->get($id);
            return $this->patchEntity($title, $properties);
        }

        return $this->newEntity($properties);
    }
}
