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
        $this->primaryKey('ID');
    }

    public function getRanksToArray()
    {
		// 段位プルダウン
//        $ranks = TableRegistry::get('Ranks');
		return $this->find('list', [
            'keyField' => 'ID',
            'valueField' => 'NAME'
        ])->where([
            'PROFESSIONAL_FLAG !=' => 0
        ])->order('ID DESC')->toArray();
    }
}
