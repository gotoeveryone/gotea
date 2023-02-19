<?php
declare(strict_types=1);

namespace Gotea\Event;

use ArrayAccess;
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
     * @param \Gotea\Event\ArrayAccess|array $user 認証ユーザ
     */
    public function __construct(array|ArrayAccess $user)
    {
        $this->_user = $user;
    }

    /**
     * @inheritDoc
     */
    public function implementedEvents(): array
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
    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options): void
    {
        $account = Hash::get($this->_user, 'account');
        if ($account) {
            $options['account'] = $account;
        }
    }
}
