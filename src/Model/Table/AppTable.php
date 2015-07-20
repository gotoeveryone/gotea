<?php

namespace App\Model\Table;

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
	 * エンティティに管理項目を保存します。
	 */
	public function save(EntityInterface $entity, $options = []) {
		// 新規登録時は登録日時を設定
		$nowDate = Time::now();
		$userId = $this->__getLoginUserId();
		//$userId = $this->_getLoginUserId();
		if (!$entity->isNew() === false) {
			$entity->CREATED = $nowDate;
			$entity->CREATED_BY = $userId;
		}
		$entity->MODIFIED = $nowDate;
		$entity->MODIFIED_BY = $userId;

		return parent::save($entity, $options);
	}

	public function updateModel($exist = false, $data = null, $conditions = null) {

		// 新規登録時は登録日時を設定
		$nowDate = date('Y-m-d H:i:s');
		$userId = $this->__getLoginUserId();
		if (!$exist) {
			$data += array('CREATED' => "'".$nowDate."'");
			$data += array('CREATED_BY' => "'".$userId."'");
		}
		$data += array('MODIFIED' => "'".$nowDate."'");
		$data += array('MODIFIED_BY' => "'".$userId."'");

		return $this->updateAll($data, $conditions);
	}

    /**
     * ログインユーザIDを取得する。
     * 
     * @return ログインユーザID
     */
	private function __getLoginUserId() {
	    $session = new Session();
        return $session->read('Auth.User.userid') || 'IgoApp';
	}
}
