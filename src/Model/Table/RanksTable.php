<?php

namespace App\Model\Table;

/**
 * 段位マスタ
 */
class RanksTable extends AppTable
{
    /**
	 * 初期設定
	 */
    public function initialize(array $config)
    {
        $this->table('M_RANK');
        $this->primaryKey('RANK');
    }
}
