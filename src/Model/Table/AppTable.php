<?php

namespace App\Model\Table;

use App\Validation\MyValidationTrait;
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
    use MyValidationTrait;

    /**
     * {@inheritdoc}
     */
    public function patchEntity(EntityInterface $entity, array $data, array $options = [])
    {
        // 基本はバリデーションしない
        $options['validate'] = false;
        return parent::patchEntity($entity, $data, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function save(EntityInterface $entity, $options = [])
    {
        // 基本はバリデーションしない
        $options['validate'] = false;
        return parent::save($entity, $options);
    }

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
			$entity->created = $nowDate;
			$entity->created_by = $userId;
		}
		$entity->modified = $nowDate;
		$entity->modified_by = $userId;
	}

    /**
     * ログインユーザIDを取得する。
     * 
     * @return ログインユーザID
     */
	private function __getLoginUserId() {
	    $session = new Session();
        $userId = $session->read('Auth.User.userId');
        return !$userId ? str_replace('/', '', ROOT) : $userId;
	}
}
