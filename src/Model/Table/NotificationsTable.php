<?php
namespace Gotea\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
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
 *
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
    public function initialize(array $config)
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
    public function validationDefault(Validator $validator)
    {
        $validator
            ->nonNegativeInteger('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('title')
            ->maxLength('title', 100)
            ->requirePresence('title', 'create')
            ->notEmpty('title');

        $validator
            ->scalar('content')
            ->requirePresence('content', 'create')
            ->notEmpty('content');

        $validator
            ->boolean('is_draft')
            ->requirePresence('is_draft', 'create')
            ->notEmpty('is_draft');

        $validator
            ->dateTime('published')
            ->requirePresence('published', 'create')
            ->notEmpty('published');

        return $validator;
    }

    /**
     * 公開日の新しい順に全件取得する。
     *
     * @return \Cake\ORM\Query
     */
    public function findAllNewestArrivals() : Query
    {
        // 棋士情報の取得
        return $this->find()->orderDesc('published');
    }
}
