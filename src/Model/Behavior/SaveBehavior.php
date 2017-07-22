<?php

namespace App\Model\Behavior;

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\Network\Session;
use Cake\ORM\Behavior;

/**
 * 保存処理実行時のビヘイビア
 */
class SaveBehavior extends Behavior
{
    /**
     * 保存前処理
     * エンティティに管理項目を設定します。
     *
     * @param Event $event
     * @param EntityInterface $entity
     */
    public function beforeSave(Event $event, EntityInterface $entity)
    {
        // 新規登録時は登録日時を設定
        $nowDate = Time::now();
        $userId = $this->__getLoginUserId();
        if ($entity->isNew()) {
            $entity->created = $nowDate;
            $entity->created_by = $userId;
        }
        $entity->modified = $nowDate;
        $entity->modified_by = $userId;
    }

    /**
     * ログインユーザIDを取得する。
     *
     * @return string ログインユーザID
     */
    private function __getLoginUserId() : string
    {
        $session = new Session();
        $userId = $session->read('Auth.User.userId');
        return !$userId ? str_replace('/', '', ROOT) : $userId;
    }
}
