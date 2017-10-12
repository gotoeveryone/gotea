<?php

namespace Gotea\Model\Entity;

use Cake\ORM\TableRegistry;

/**
 * 所属国エンティティ
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
            return [];
        }

        $result = TableRegistry::get('Organizations')->findByCountryId($this->id);
        return $this->organizations = $result;
    }

    /**
     * 国際棋戦かどうかを判定します。
     *
     * @return boolean 国際棋戦ならtrue
     */
    public function isWorlds()
    {
        return !$this->has_title;
    }

    /**
     * 表示用のラベルを取得します。
     *
     * @return string ラベル
     */
    protected function _getLabel()
    {
        return "（{$this->name}棋戦）";
    }
}
