<?php

namespace App\Model\Table;

use ArrayObject;
use Cake\Event\Event;
use Cake\ORM\Table;
use Cake\Datasource\EntityInterface;
use Cake\I18n\Time;
use Cake\Network\Session;

/**
 * アプリケーションの共通テーブル
 */
class AppTable extends Table
{
	/**
	 * 保存前処理
     * エンティティに管理項目を設定します。
     * 
     * @param Event $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @param type $operation
     */
	public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options) {
		// 新規登録時は登録日時を設定
		$nowDate = Time::now();
		$userId = $this->__getLoginUserId();
		if (!$entity->isNew() === false) {
			$entity->setCreated($nowDate);
			$entity->setCreatedBy($userId);
		}
		$entity->setModified($nowDate);
		$entity->setModifiedBy($userId);
	}

    /**
     * ログインユーザIDを取得する。
     * 
     * @return ログインユーザID
     */
	private function __getLoginUserId() {
	    $session = new Session();
        $userId = $session->read('Auth.User.userId');
        return !$userId ? 'IgoApp' : $userId;
	}
}
