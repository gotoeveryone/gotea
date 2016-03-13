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
     * タイトル情報一式を取得
     * 
     * @param type $id
     * @return type
     */
    public function findTitleAllRelations($id)
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
