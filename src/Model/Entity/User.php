<?php
declare(strict_types=1);

namespace Gotea\Model\Entity;

use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property string $account
 * @property string $name
 * @property string $password
 * @property bool $is_admin
 * @property \Cake\I18n\FrozenTime $last_logged
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class User extends Entity
{
    /**
     * @inheritDoc
     */
    protected array $_accessible = [
        'account' => true,
        'name' => true,
        'password' => true,
        'is_admin' => true,
        'last_logged' => true,
        'created' => true,
        'modified' => true,
    ];

    /**
     * @inheritDoc
     */
    protected array $_hidden = [
        'password',
    ];
}
