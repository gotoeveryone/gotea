<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * アプリケーションの共通エンティティ
 */
class AppEntity extends Entity
{
    // アクセス許可
	protected $_accessible = [
        '*' => true,
        'id' => false,
    ];

    /**
     * 所属國を取得します。
     * 
     * @return App\Model\Entity\Country
     */
    protected function _getCountry()
    {
        if (!isset($this->_virtual['country'])) {
            $countries = TableRegistry::get('Countries');
            $this->_virtual['country'] = $countries->get($this->country_id);
        }
        return $this->_virtual['country'];
    }

    /**
     * 段位を取得します。
     * 
     * @return App\Model\Entity\Rank
     */
    protected function _getRank()
    {
        if (!isset($this->_virtual['rank'])) {
            $ranks = TableRegistry::get('Ranks');
            $this->_virtual['rank'] = $ranks->get($this->rank_id);
        }
        return $this->_virtual['rank'];
    }

    /**
     * 所属組織を取得します。
     * 
     * @return App\Model\Entity\Organization
     */
    protected function _getOrganization()
    {
        if (!isset($this->_virtual['organization'])) {
            $organizations = TableRegistry::get('Organizations');
            $this->_virtual['organization'] = $organizations->get($this->organization_id);
        }
        return $this->_virtual['organization'];
    }
}
