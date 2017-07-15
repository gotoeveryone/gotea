<?php

namespace App\Model\Table;

use Cake\Validation\Validator;

/**
 * 組織
 */
class OrganizationsTable extends AppTable
{
    /**
     * {@inheritdoc}
     */
    public function initialize(array $config)
    {
        $this->displayField('name');
        // 国
        $this->belongsTo('Countries');
    }

    /**
     * {@inheritdoc}
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
}
