<?php

namespace App\Model\Table;

/**
 * 段位
 */
class RanksTable extends AppTable
{
    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        $this->displayField('name');
    }

    /**
     * 段位のID・名前を一覧で取得します。
     *
     * @return array
     */
    public function findToKeyValue()
    {
		// 段位プルダウン
		return $this->find('list')->where([
            'rank_numeric IS NOT' => null
        ])->order('rank_numeric DESC')->toArray();
    }
}
