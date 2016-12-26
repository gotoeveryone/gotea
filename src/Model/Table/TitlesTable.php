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
            ->notEmpty('name', __d('default', 'field {0} is required', 'タイトル名'))
            ->notEmpty('name_english', __d('default', 'field {0} is required', 'タイトル名（英語）'))
            ->notEmpty('holding', __d('default', 'field {0} is required', '期'))
            ->numeric('sort_order', __d('default', 'field {0} is numeric value only', '並び順'))
            ->notEmpty('sort_order', __d('default', 'field {0} is required', '並び順'))
            ->notEmpty('html_file_name', __d('default', 'field {0} is required', 'HTMLファイル名'))
            ->notEmpty('html_file_modified', __d('default', 'field {0} is required', 'HTMLファイル修正日'))
            ->date('html_file_modified', 'ymd', '修正日は「yyyy/mm/dd」形式で入力してください。');
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
    public function findTitleWithRelations($id)
    {
		return $this->find()->contain([
            'Countries',
            'RetentionHistories' => function ($q) {
                return $q->order(['RetentionHistories.target_year' => 'DESC']);
            },
            'RetentionHistories.Ranks',
            'RetentionHistories.Titles.Countries',
            'RetentionHistories.Players'
        ])->where(['Titles.id' => $id])->first();
    }
}
