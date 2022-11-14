<?php
declare(strict_types=1);

namespace Gotea\Model\Table;

use Cake\ORM\Query;
use Cake\Validation\Validator;

/**
 * 組織
 */
class OrganizationsTable extends AppTable
{
    /**
     * @inheritDoc
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setDisplayField('name');

        // 所属国
        $this->belongsTo('Countries')
            ->setJoinType('INNER');
    }

    /**
     * @inheritDoc
     */
    public function validationDefault(Validator $validator): Validator
    {
        return $validator->notEmptyString('name', '組織名は必須です。');
    }

    /**
     * 所属書式をID・名前の一覧で取得します。
     *
     * @return \Cake\ORM\Query 生成されたクエリ
     */
    public function findSorted(): Query
    {
        return $this->find('list')->order(['country_id', 'id']);
    }
}
