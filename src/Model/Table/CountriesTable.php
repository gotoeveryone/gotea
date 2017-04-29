<?php

namespace App\Model\Table;

/**
 * 国
 */
class CountriesTable extends AppTable
{
    /**
     * 所属国情報を取得します。
     * 
     * return type
     */
    public function findCountryHasFileToArray()
    {
		return $this->find('list', [
            'keyField' => 'id',
            'valueField' => 'name'
        ])->order(['id'])->toArray();
    }

    /**
     * 所属国情報を取得します。
     * 
     * return type
     */
    public function findCountryBelongToArray()
    {
		return $this->find('list', [
            'keyField' => 'id',
            'valueField' => 'name'
        ])->where([
            'has_title' => true
        ])->order(['id'])->toArray();
    }
}
