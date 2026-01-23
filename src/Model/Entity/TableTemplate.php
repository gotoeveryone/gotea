<?php
declare(strict_types=1);

namespace Gotea\Model\Entity;

/**
 * TableTemplate Entity
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property \Cake\I18n\FrozenTime $created
 * @property string $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $modified_by
 */
class TableTemplate extends AppEntity
{
    /**
     * @inheritDoc
     */
    protected array $_accessible = [
        'title' => true,
        'content' => true,
        'created',
        'modified' => true,
    ];

    /**
     * @inheritDoc
     */
    protected array $_hidden = [
        'created_by',
        'modified_by',
    ];
}
