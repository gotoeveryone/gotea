<?php

namespace App\Model\Table;

/**
 * 段位
 */
class RanksTable extends AppTable
{
    public function getRanksToArray()
    {
		// 段位プルダウン
		return $this->find('list', [
            'keyField' => 'id',
            'valueField' => 'name'
        ])->where([
            'rank_numeric IS NOT' => null
        ])->order('rank_numeric DESC')->toArray();
    }
}
