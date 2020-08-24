<?php
declare(strict_types=1);

namespace Gotea\Model\Table;

use Cake\Validation\Validator;

/**
 * TableTemplates Model
 *
 * @method \Gotea\Model\Entity\TableTemplate newEmptyEntity()
 * @method \Gotea\Model\Entity\TableTemplate newEntity(array $data, array $options = [])
 * @method \Gotea\Model\Entity\TableTemplate[] newEntities(array $data, array $options = [])
 * @method \Gotea\Model\Entity\TableTemplate get($primaryKey, $options = [])
 * @method \Gotea\Model\Entity\TableTemplate findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \Gotea\Model\Entity\TableTemplate patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Gotea\Model\Entity\TableTemplate[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \Gotea\Model\Entity\TableTemplate|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Gotea\Model\Entity\TableTemplate saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Gotea\Model\Entity\TableTemplate[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \Gotea\Model\Entity\TableTemplate[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \Gotea\Model\Entity\TableTemplate[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \Gotea\Model\Entity\TableTemplate[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class TableTemplatesTable extends AppTable
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('table_templates');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('title')
            ->maxLength('title', 100)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('content')
            ->requirePresence('content', 'create')
            ->notEmptyString('content');

        return $validator;
    }
}
