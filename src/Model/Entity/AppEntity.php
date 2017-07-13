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
        'created' => false,
        'modified' => false,
    ];

    /**
     * 所属國を取得します。
     *
     * @param type $country
     * @return App\Model\Entity\Country
     */
    protected function _getCountry($country)
    {
        return ($country ? $country : TableRegistry::get('Countries')->get($this->country_id));
    }

    /**
     * 段位を取得します。
     *
     * @param type $rank
     * @return App\Model\Entity\Rank
     */
    protected function _getRank($rank)
    {
        return ($rank ? $rank : TableRegistry::get('Ranks')->get($this->rank_id));
    }

    /**
     * 所属組織を取得します。
     *
     * @param type $organization
     * @return App\Model\Entity\Organization
     */
    protected function _getOrganization($organization)
    {
        return ($organization ? $organization : TableRegistry::get('Organizations')->get($this->organization_id));
    }
}
