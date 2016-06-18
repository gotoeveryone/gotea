<?php

namespace App\Model\Table;

use Cake\Validation\Validator;

/**
 * 組織
 */
class OrganizationsTable extends AppTable
{
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
     * 組織情報を取得します。
     * 
     * return type
     */
    public function findOrganizationsToArray()
    {
		return $this->find('list', [
            'keyField' => 'id',
            'valueField' => 'name'
        ])->order(['id'])->toArray();
    }
}
