<?php

namespace App\Model\Entity;

use Cake\ORM\TableRegistry;

/**
 * 国エンティティ
 */
class Country extends AppEntity
{
    /**
     * 棋士の所属組織を取得します。
     *
     * @param mixed $value
     * @return \Cake\ORM\ResultSet|null 所属組織
     */
    protected function _getOrganizations($value)
    {
        if ($value) {
            return $value;
        }

        if (!$this->id) {
            return null;
        }

        $result = TableRegistry::get('Organizations')->findByCountryId($this->id);
        return $this->organizations = $result;
    }
}
