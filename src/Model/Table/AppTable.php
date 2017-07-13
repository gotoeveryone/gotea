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
	public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options)
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
     * キー情報をもとに、データを1件取得します。
     *
     * @param array $data
     * @param array $fields
     * @return null|\Cake\ORM\Entity
     */
    public function findByKey(array $data, $fields = [])
    {
        $params = [];
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                return null;
            }
            $params[$field] = $data[$field];
        }
		return $this->find()->where($params)->first();
    }

    /**
     * データを追加します。
     *
     * @param array $data
     * @param array $fields
     * @return \App\Model\Entity\AppEntity|false データが登録できればそのEntity
     */
    protected function _addEntity(array $data, $fields = [])
    {
        // 同一キーのデータがあれば終了
		if ($this->findByKey($data, $fields)) {
            return false;
		}

        // データの登録
        return $this->save($this->newEntity($data));
    }

    /**
     * ランキングデータの取得方法を判定します。
     *
     * @param int $targetYear
     * @return bool
     */
    protected function _isOldRanking(int $targetYear) : bool
    {
        return ($targetYear < 2017);
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
