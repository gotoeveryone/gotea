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
        ])->toArray();
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
        ])->toArray();
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
//        ])->where(function ($exp, $q) {
//            return $exp->isNotNull('OUTPUT_FILE_NAME');
        ])->order(['Countries.id' => 'ASC'])->select([
            'keyField' => 'Countries.name',
            'valueField' => 'CASE Countries.code_alpha_2 WHEN null THEN CONCAT(Countries.name, \'棋戦\') ELSE CONCAT(Countries.name, \'棋士\') END'
        ])->toArray();
    }
}
