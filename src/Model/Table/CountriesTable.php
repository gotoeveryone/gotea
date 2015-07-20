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
}
