<?php

namespace App\Model\Table;

/**
 * 所属国
 */
class CountriesTable extends AppTable
{
    /**
     * 所属国一覧（ID・名前）を取得します。
     *
     * @param bool $has_title
     * @return array
     */
    public function findLists($hasTitle = false)
    {
		$query = $this->find('list', [
            'keyField' => 'id',
            'valueField' => 'name'
        ])->order(['id']);

        if ($hasTitle) {
            $query->where('has_title', true);
        }

        return $query->toArray();
    }
}
