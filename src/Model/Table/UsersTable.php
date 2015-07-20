<?php

namespace App\Model\Table;

/**
 * ユーザ情報
 */
class UsersTable extends AppTable {

	/**
	 * 初期設定
     * 
     * @param $config
	 */
    public function initialize(array $config)
    {
        $this->table('M_USER');
        $this->primaryKey('ID');
//        $this->entityClass('App\Model\Entity\User');
    }
}
