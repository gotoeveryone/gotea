<?php
declare(strict_types=1);

namespace Gotea\Model\Table;

use Cake\ORM\Query;
use Cake\Validation\Validator;

/**
 * Notifications Model
 *
 * @method \Gotea\Model\Entity\Notification get($primaryKey, $options = [])
 * @method \Gotea\Model\Entity\Notification newEntity($data = null, array $options = [])
 * @method \Gotea\Model\Entity\Notification[] newEntities(array $data, array $options = [])
 * @method \Gotea\Model\Entity\Notification|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Gotea\Model\Entity\Notification|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Gotea\Model\Entity\Notification patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Gotea\Model\Entity\Notification[] patchEntities($entities, array $data, array $options = [])
 * @method \Gotea\Model\Entity\Notification findOrCreate($search, callable $callback = null, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class NotificationsTable extends AppTable
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

        $this->setTable('notifications');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
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
            ->requirePresence('title')
            ->notEmptyString('title');

        $validator
            ->scalar('content')
            ->requirePresence('content')
            ->notEmptyString('content');

        $validator
            ->boolean('is_draft')
            ->notEmptyString('is_draft');

        $validator
            ->dateTime('published')
            ->requirePresence('published')
            ->notEmptyDateTime('published');

        $validator
            ->boolean('is_permanent')
            ->notEmptyString('is_permanent');

        return $validator;
    }

    /**
     * 公開日の新しい順に全件取得する。
     *
     * @return \Cake\ORM\Query
     */
    public function findAllNewestArrivals(): Query
    {
        // 棋士情報の取得
        return $this->find()->orderDesc('published');
    }
}
