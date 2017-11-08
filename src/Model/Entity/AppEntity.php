<?php

namespace Gotea\Model\Entity;

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
     * {@inheritDoc}
     */
    public function &get($property)
    {
        $value = parent::get($property);
        // 配列ならCollectionに変換
        if (is_array($value)) {
            $value = collection($value);
        }

        return $value;
    }

    /**
     * 所属國を取得します。
     *
     * @param mixed $value 値
     * @return Country
     */
    protected function _getCountry($value)
    {
        if ($value) {
            return $value;
        }

        $result = TableRegistry::get('Countries')->get($this->country_id);

        return $this->country = $result;
    }

    /**
     * 段位を取得します。
     *
     * @param mixed $value 値
     * @return Rank
     */
    protected function _getRank($value)
    {
        if ($value) {
            return $value;
        }

        $result = TableRegistry::get('Ranks')->get($this->rank_id);

        return $this->rank = $result;
    }
}
