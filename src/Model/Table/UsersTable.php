<?php

namespace App\Model\Table;

/**
 * ユーザ
 */
class UsersTable extends AppTable
{
	/**
	 * 初期設定
     * 
     * @param $config
	 */
    public function initialize(array $config)
    {
        // ビューを参照しているため、主キーを明示的に指定
        $this->primaryKey('id');
    }
}
