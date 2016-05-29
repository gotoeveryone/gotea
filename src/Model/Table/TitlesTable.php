<?php

namespace App\Model\Table;

use Cake\Validation\Validator;

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
        $this->hasMany('ArquisitionHistories', [
            'order' => [
                'id' => 'ASC',
                'ArquisitionHistories.holding' => 'DESC'
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
            ->notempty('name', 'タイトル名は必須です。')
            ->notempty('name_english', 'タイトル名（英語）は必須です。')
            ->notempty('holding', '期は必須です。')
            ->notempty('sort_order', '並び順は必須です。')
            ->notempty('html_file_name', 'htmlファイル名は必須です。')
            ->notempty('html_file_modified', '修正日は必須です。')
            ->add('html_file_modified', [
                'valid' => [
                    'rule' => ['date', 'ymd'],
                    'message' => '修正日は「yyyy/mm/dd」形式で入力してください。'
                ]
            ]);
    }

    /**
     * 所属国をもとにタイトルの一覧を取得します。
     * 
     * @param type $countryId
     * @param type $notSearchEndTitles
     * @return type
     */
    public function findTitlesByCountry($countryId, $notSearchEndTitles = null)
    {
        $query = $this->find()->contain([
            'Countries',
            'ArquisitionHistories',
            'ArquisitionHistories.Titles' => function ($q) {
                return $q->where(['ArquisitionHistories.holding = Titles.holding']);
            },
            'ArquisitionHistories.Players',
            'ArquisitionHistories.Ranks'
        ])->where([
            'Countries.id' => $countryId
        ]);

        // 有効なタイトルのみ検索
        if ($notSearchEndTitles === 'false') {
            $query->where(['Titles.is_closed' => 0]);
        }

        // データを取得
        return $query->order(['Titles.sort_order' => 'ASC'])->all();
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
            'ArquisitionHistories' => function ($q) {
                return $q->order(['ArquisitionHistories.target_year' => 'DESC']);
            },
            'ArquisitionHistories.Ranks',
            'ArquisitionHistories.Titles.Countries',
            'ArquisitionHistories.Players'
        ])->where(['Titles.id' => $id])->first();
    }
}
