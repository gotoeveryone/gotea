<?php

namespace Gotea\Event;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Utility\Hash;

/**
 * ログインユーザ情報を保持
 */
class LoggedUser implements EventListenerInterface
{
    /**
     * Constructor
     *
     * @param array|ArrayAccess $user 認証ユーザ
     */
    public function __construct($user)
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
        if (($account = Hash::get($this->_user, 'account'))) {
            $options['account'] = $account;
        }
    }
}
