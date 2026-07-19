<?php
declare(strict_types=1);

namespace Gotea\Model\Entity;

/**
 * 段位エンティティ
 *
 * @property string $name
 * @property int|null $rank_numeric
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class Rank extends AppEntity
{
    /**
     * @inheritDoc
     */
    protected array $_accessible = [
        'id' => false,
        'name' => true,
        'rank_numeric' => true,
        'created' => false,
        'modified' => false,
    ];
}
