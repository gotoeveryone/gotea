<?php
declare(strict_types=1);

namespace Gotea\Model\Entity;

use Cake\ORM\TableRegistry;

/**
 * 所属国の操作クラス
 */
trait CountryTrait
{
    /**
     * 所属國を取得します。
     *
     * @param \Gotea\Model\Entity\Country|null $country 所属国オブジェクト
     * @return \Gotea\Model\Entity\Country|null
     */
    protected function _getCountry(?Country $country): ?Country
    {
        if ($country) {
            return $country;
        }

        if (!$this->country_id) {
            return null;
        }

        $result = TableRegistry::getTableLocator()->get('Countries')->get($this->country_id);

        return $this->country = $result;
    }
}
