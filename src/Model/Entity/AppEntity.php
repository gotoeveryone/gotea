<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * アプリケーションの共通エンティティ
 */
class AppEntity extends Entity
{
    // アクセス許可
	protected $_accessible = [
        '*' => true,
        'ID' => false,
    ];

	// ID
	public function getId()
	{
		return $this->get('ID');
	}

	// 削除フラグ
	public function getDeleteFlag()
	{
		return $this->get('DELETE_FLAG');
	}
	public function setDeleteFlag($deleteFlag)
	{
		$this->set('DELETE_FLAG', $deleteFlag);
	}

	// 初回登録日時
	public function getCreated()
	{
		return $this->get('CREATED');
	}
	public function setCreated($created)
	{
		$this->set('CREATED', $created);
	}

	// 初回登録者
	public function getCreatedBy()
	{
		return $this->get('CREATED_BY');
	}
	public function setCreatedBy($createdBy)
	{
		$this->set('CREATED_BY', $createdBy);
	}

	// 最終更新日時
	protected function _getModified()
	{
		return $this->get('MODIFIED');
	}
	protected function _setModified($modified)
	{
		$this->set('MODIFIED', $modified);
	}

	// 最終更新者
	protected function _getModifiedBy()
	{
		return $this->get('MODIFIED_BY');
	}
	protected function _setModifiedBy($modifiedBy)
	{
		$this->set('MODIFIED_BY', $modifiedBy);
	}
}
