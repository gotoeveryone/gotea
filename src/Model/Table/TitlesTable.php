<?php

namespace App\Model\Table;

use Cake\Validation\Validator;
use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\Utility\Inflector;
use App\Model\Entity\Title;

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
        $this->hasMany('RetentionHistories', [
            'order' => [
                'id' => 'ASC',
                'RetentionHistories.holding' => 'DESC'
            ]
        ]);
        // 所属国マスタ
        $this->belongsTo('Countries');
    }

    /**
     * {@inheritdoc}
     */
    public function validationDefault(Validator $validator)
    {
        return $validator
            ->notEmpty('name', $this->getMessage($this->REQUIRED, 'タイトル名'))
            ->notEmpty('name_english', $this->getMessage($this->REQUIRED, 'タイトル名（英語）'))
            ->add('name_english', 'default', [
                'rule' => [$this, 'alphaNumeric'],
                'message' => $this->getMessage($this->ALPHA_NUMERIC, 'タイトル名（英語）')
            ])
            ->notEmpty('holding', $this->getMessage($this->REQUIRED, '期'))
            ->numeric('holding', $this->getMessage($this->NUMERIC, '期'))
            ->notEmpty('sort_order',$this->getMessage($this->REQUIRED, '並び順'))
            ->numeric('sort_order', $this->getMessage($this->NUMERIC, '並び順'))
            ->notEmpty('html_file_name', $this->getMessage($this->REQUIRED, 'HTMLファイル名'))
            ->add('html_file_name', 'default', [
                'rule' => [$this, 'alphaNumeric'],
                'message' => $this->getMessage($this->ALPHA_NUMERIC, 'HTMLファイル名')
            ])
            ->notEmpty('html_file_modified', $this->getMessage($this->REQUIRED, 'HTMLファイル修正日'))
            ->date('html_file_modified', ['ymd'], $this->getMessage($this->INLALID_FORMAT, ['修正日', 'yyyy/mm/dd']));
    }

    /**
     * タイトル情報を取得します。
     *
     * @param array $data
     * @return \Cake\ORM\Query 生成したクエリ
     */
    public function findTitlesQuery($data = [])
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
     * @return \App\Model\Entity\Title
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
