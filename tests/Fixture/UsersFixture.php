<?php
declare(strict_types=1);

namespace Gotea\Test\Fixture;

use Cake\Auth\DefaultPasswordHasher;
use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 */
class UsersFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $h = new DefaultPasswordHasher();

        $this->records = [
            [
                'account' => 'testuser',
                'name' => 'Test User',
                'password' => $h->hash('password'),
                'is_admin' => 0,
                'created' => '2021-02-03 08:49:55',
                'modified' => '2021-02-03 08:49:55',
            ],
            [
                'account' => 'adminuser',
                'name' => 'Test Admin User',
                'password' => $h->hash('password'),
                'is_admin' => 1,
                'created' => '2021-02-03 08:49:55',
                'modified' => '2021-02-03 08:49:55',
            ],
        ];
        parent::init();
    }
}
