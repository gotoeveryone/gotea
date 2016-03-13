<?php

namespace App\Model\Table;

/**
 * 所属国マスタ
 */
class CountriesTable extends AppTable
{
    /**
	 * 初期設定
	 */
    public function initialize(array $config)
    {
        $this->table('M_COUNTRY');
        $this->primaryKey('ID');
//        $this->entityClass('App\Model\Entity\Country');
    }

    /**
     * 所属国情報を取得します。
     * 
     * return type
     */
    public function findCountryHasFileToArray()
    {
		return $this->find('list', [
            'keyField' => 'ID',
            'valueField' => 'NAME'
        ])->where([
            'Countries.OUTPUT_FILE_NAME IS NOT' => null
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
            'keyField' => 'ID',
            'valueField' => 'NAME'
        ])->where([
            'BELONG_FLAG ' => 1
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
        ])->where(function ($exp, $q) {
            return $exp->isNotNull('OUTPUT_FILE_NAME');
        })->order(['Countries.ID' => 'ASC'])->select([
            'keyField' => 'Countries.ID',
            'valueField' => 'CASE Countries.WORLD_FLAG WHEN 1 THEN CONCAT(Countries.NAME, \'棋戦\') ELSE CONCAT(Countries.NAME, \'棋士\') END'
        ])->toArray();
    }
}
