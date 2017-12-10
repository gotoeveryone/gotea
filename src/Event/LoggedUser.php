<?php

namespace Gotea\Event;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;

/**
 * ログインユーザ情報を保持
 */
class LoggedUser implements EventListenerInterface
{
    /**
     * Constructor
     *
     * @param array $user 認証ユーザ
     */
    public function __construct(array $user)
    {
        $this->_user = $user;
    }

    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return [
            'Model.beforeSave' => [
                'callable' => 'beforeSave',
                'priority' => -1,
            ],
        ];
    }

    /**
     * モデルの保存前に処理
     *
     * @param \Cake\Event\Event $event イベント
     * @param \Cake\Datasource\EntityInterface $entity 対象のエンティティ
     * @param \ArrayObject $options オプション
     * @return void
     */
    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        if (empty($options['account'])
            && ($account = $this->_user['account'] ?? null)) {
            $options['account'] = $account;
        }
    }
}
