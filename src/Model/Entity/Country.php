<?php
declare(strict_types=1);

namespace Gotea\Model\Entity;

use Cake\ORM\ResultSet;
use Cake\ORM\TableRegistry;

/**
 * 所属国エンティティ
 *
 * @property string $code
 * @property string $name
 * @property string $name_english
 * @property bool $has_title
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \Gotea\Model\Entity\Organization[] $organizations
 */
class Country extends AppEntity
{
    /**
     * @inheritDoc
     */
    protected array $_accessible = [
        'id' => false,
        'code' => true,
        'name' => true,
        'name_english' => true,
        'has_title' => true,
        'created' => false,
        'modified' => false,
    ];

    /**
     * 棋士の所属組織を取得します。
     *
     * @param mixed $value 値
     * @return \Cake\ORM\ResultSet|null 所属組織
     */
    protected function _getOrganizations(mixed $value): ?ResultSet
    {
        if ($value) {
            return $value;
        }

        if (!$this->id) {
            return [];
        }

        $result = TableRegistry::getTableLocator()->get('Organizations')->findByCountryId($this->id);

        return $this->organizations = $result;
    }

    /**
     * 国際棋戦かどうかを判定します。
     *
     * @return bool 国際棋戦ならtrue
     */
    public function isWorlds(): bool
    {
        return !$this->has_title;
    }
}
