<?php

namespace Gotea\Model\Table;

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
        parent::initialize($config);
        $this->displayField('name');

        // 所属国
        $this->belongsTo('Countries')
            ->setJoinType('INNER');
    }

    /**
     * {@inheritdoc}
     */
    public function validationDefault(Validator $validator)
    {
        return $validator->notEmpty('name', '組織名は必須です。');
    }

    /**
     * 所属書式をID・名前の一覧で取得します。
     *
     * @return \Cake\ORM\Query 生成されたクエリ
     */
    public function findSorted()
    {
        return $this->find('list')->order(['country_id', 'id']);
    }
}
