<?php

namespace App\Model\Table;

use Cake\Validation\Validator;

/**
 * 組織
 */
class OrganizationsTable extends AppTable
{
	/**
	 * 初期設定
     *
     * @param $config
	 */
    public function initialize(array $config)
    {
        $this->displayField('name');
        // 国
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
        return $validator->notEmpty('name', '組織名は必須です。');
    }

    /**
     * キーにID：値に名前を保持する配列形式で取得します。
     *
     * @return array
     */
    public function findToKeyValue()
    {
		return $this->find('list')->order(['id'])->toArray();
    }

    /**
     * 指定の国IDに該当する組織一覧を取得します。
     *
     * @param $countryId
     * @return type
     */
    public function findByCountry($countryId)
    {
		return $this->find()
                ->contain('Countries')
                ->where(['Countries.id' => $countryId])->all();
    }
}
