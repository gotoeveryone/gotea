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
        'id' => false,
    ];

	// ID
	public function getId()
	{
		return $this->id;
	}

	// 初回登録日時
	public function getCreated()
	{
		return $this->created;
	}
	public function setCreated($created)
	{
		return $this->created = $created;
	}

	// 初回登録者
	public function getCreatedBy()
	{
		return $this->created_by;
	}
	public function setCreatedBy($createdBy)
	{
		return $this->created_by = $createdBy;
	}

	// 最終更新日時
	public function getModified()
	{
		return $this->modified;
	}
	public function setModified($modified)
	{
		return $this->modified = $modified;
	}

	// 最終更新者
	public function getModifiedBy()
	{
		return $this->modified_by;
	}
	public function setModifiedBy($modifiedBy)
	{
		return $this->modified_by = $modifiedBy;
	}
}
