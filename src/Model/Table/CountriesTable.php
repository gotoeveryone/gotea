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

    /**
     * 所属国情報を取得します。
     * 
     * return type
     */
    public function findCountryHasFileToArrayWithSuffix()
    {
        return $this->find('list', [
            'keyField' => 'keyField',
            'valueField' => 'valueField'
        ])->order(['id' => 'ASC'])->select([
            'keyField' => 'name',
            'valueField' => "CASE code_alpha_2 WHEN 'wr' THEN CONCAT(name, '棋戦') ELSE CONCAT(name, '棋士') END"
        ])->toArray();
    }
}
