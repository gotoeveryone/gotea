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
		return $this->ID;
	}

	// 削除フラグ
	public function getDeleteFlag()
	{
		return $this->DELETE_FLAG;
	}
	public function setDeleteFlag($deleteFlag)
	{
		return $this->DELETE_FLAG = $deleteFlag;
	}

	// 初回登録日時
	public function getCreated()
	{
		return $this->CREATED;
	}
	public function setCreated($created)
	{
		return $this->CREATED = $created;
	}

	// 初回登録者
	public function getCreatedBy()
	{
		return $this->CREATED_BY;
	}
	public function setCreatedBy($createdBy)
	{
		return $this->CREATED_BY = $createdBy;
	}

	// 最終更新日時
	public function getModified()
	{
		return $this->MODIFIED;
	}
	public function setModified($modified)
	{
		return $this->MODIFIED = $modified;
	}

	// 最終更新者
	public function getModifiedBy()
	{
		return $this->MODIFIED_BY;
	}
	public function setModifiedBy($modifiedBy)
	{
		return $this->MODIFIED_BY = $modifiedBy;
	}
}
