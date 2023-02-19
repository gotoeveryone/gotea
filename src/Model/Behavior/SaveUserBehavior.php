<?php
declare(strict_types=1);

namespace Gotea\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;

/**
 * 保存処理実行時のビヘイビア
 */
class SaveUserBehavior extends Behavior
{
    /**
     * 保存前処理
     * エンティティに管理項目を設定します。
     *
     * @param \Cake\Event\Event $event イベント
     * @param \Cake\Datasource\EntityInterface $entity 対象のエンティティ
     * @param \ArrayObject $options オプション
     * @return void
     */
    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options): void
    {
        // ユーザIDを取得
        $userId = $options['account'] ?? null;
        if ($entity->isNew()) {
            $entity->created_by = $userId;
        }
        $entity->modified_by = $userId;
    }
}
