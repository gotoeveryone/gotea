<?php

namespace App\Model\Table;

use Cake\Validation\Validator;

/**
 * タイトルマスタ
 */
class TitlesTable extends AppTable {

    /**
	 * 初期設定
     * 
     * @param $config
	 */
    public function initialize(array $config)
    {
        $this->table('M_TITLE');
        $this->primaryKey('ID');
        // タイトル保持情報
        $this->hasMany('TitleRetains', [
            'foreignKey' => 'TITLE_ID',
            'order' => [
                'TitleRetains.TITLE_ID' => 'ASC',
                'TitleRetains.HOLDING' => 'DESC'
            ]
        ]);
        // 所属国マスタ
        $this->belongsTo('Countries', [
            'foreignKey' => 'COUNTRY_ID',
            'joinType' => 'INNER'
        ]);
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
            ->notEmpty('NAME', 'タイトル名は必須です。')
            ->notEmpty('NAME_ENGLISH', 'タイトル名（英語）は必須です。')
            ->notEmpty('HOLDING', '期は必須です。')
            ->notEmpty('SORT_ORDER', '並び順は必須です。')
            ->notEmpty('HTML_FILE_NAME', 'HTMLファイル名は必須です。')
            ->notEmpty('HTML_FILE_MODIFIED', '修正日は必須です。')
            ->add('HTML_FILE_MODIFIED', [
                'valid' => [
                    'rule' => ['date', 'ymd'],
                    'message' => '修正日は「yyyy/MM/dd」形式で入力してください。'
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
            'TitleRetains',
            'TitleRetains.Titles' => function ($q) {
                return $q->where(['TitleRetains.HOLDING = Titles.HOLDING']);
            },
            'TitleRetains.Players',
            'TitleRetains.Ranks'
        ])->where([
            'Titles.COUNTRY_ID' => $countryId
        ]);

        // 有効なタイトルのみ検索
        if ($notSearchEndTitles === 'false') {
            $query->where(['Titles.DELETE_FLAG' => 0]);
        }

        // データを取得
        return $query->order(['Titles.SORT_ORDER' => 'ASC'])->all();
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
            'TitleRetains' => function ($q) {
                return $q->order(['TitleRetains.TARGET_YEAR' => 'DESC']);
            },
            'TitleRetains.Ranks',
            'TitleRetains.Titles.Countries',
            'TitleRetains.Players'
        ])->where(['Titles.ID' => $id])->first();
    }
}
