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
        $this->primaryKey('COUNTRY_CD');
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
            'keyField' => 'COUNTRY_CD',
            'valueField' => 'COUNTRY_NAME'
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
            'keyField' => 'COUNTRY_CD',
            'valueField' => 'COUNTRY_NAME'
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
        })->order(['Countries.COUNTRY_CD' => 'ASC'])->select([
            'keyField' => 'Countries.COUNTRY_CD',
            'valueField' => 'CASE Countries.COUNTRY_CD WHEN \'99\' THEN CONCAT(Countries.COUNTRY_NAME, \'棋戦\') ELSE CONCAT(Countries.COUNTRY_NAME, \'棋士\') END'
        ])->toArray();
    }
}
