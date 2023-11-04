<?php
declare(strict_types=1);

use Cake\Auth\DefaultPasswordHasher;
use Migrations\AbstractSeed;

/**
 * Users seed.
 */
class UsersSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     *
     * @return void
     */
    public function run(): void
    {
        $h = new DefaultPasswordHasher();

        $data = [
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

        $table = $this->table('users');
        $table->insert($data)->save();
    }
}
