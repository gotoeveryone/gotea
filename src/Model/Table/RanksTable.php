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
     * @return \Cake\ORM\Query 生成されたクエリ
     */
    public function findProfessional()
    {
		// 段位プルダウン
		return $this->find()->where([
            'rank_numeric IS NOT' => null
        ])->order('rank_numeric DESC')->select(['id', 'name', 'rank_numeric']);
    }
}
