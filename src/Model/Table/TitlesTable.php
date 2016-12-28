<?php

namespace App\Model\Table;

use Cake\Validation\Validator;
use Cake\ORM\Query;

/**
 * タイトル
 */
class TitlesTable extends AppTable {

    /**
	 * 初期設定
     * 
     * @param $config
	 */
    public function initialize(array $config)
    {
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
     * バリデーションルール
     * 
     * @param \App\Model\Table\Validator $validator
     * @return type
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
     * 所属国をもとにタイトルの一覧を取得します。
     * 
     * @param array $data
     * @return type
     */
    public function findTitlesByCountry($data)
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
        if (isset($data['country_id']) && ($countryId = $data['country_id'])) {
            $query->where(['Countries.id' => $countryId]);
        }

        // 有効なタイトルのみ検索
        if (!isset($data['is_closed']) || !$data['is_closed']) {
            $query->where(['Titles.is_closed' => 0]);
        }

        // データを取得
        return $query->orderAsc('Titles.sort_order')->all();
    }

    /**
     * タイトル情報一式を取得
     * 
     * @param type $id
     * @return type
     */
    public function getInner($id)
    {
		return $this->find()->contain([
            'Countries',
            'RetentionHistories' => function (Query $q) {
                return $q->orderDesc('RetentionHistories.target_year');
            },
            'RetentionHistories.Ranks',
            'RetentionHistories.Titles.Countries',
            'RetentionHistories.Players'
        ])->where(['Titles.id' => $id])->first();
    }
}
