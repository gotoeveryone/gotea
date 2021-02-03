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
     * Fields
     *
     * @var array
     */
    // phpcs:disable
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'サロゲートキー', 'autoIncrement' => true, 'precision' => null],
        'account' => ['type' => 'string', 'length' => 10, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_bin', 'comment' => 'アカウント', 'precision' => null],
        'name' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_bin', 'comment' => 'ユーザ名', 'precision' => null],
        'password' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_bin', 'comment' => 'パスワード', 'precision' => null],
        'is_admin' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '0', 'comment' => '管理者フラグ', 'precision' => null],
        'last_logged' => ['type' => 'datetime', 'length' => null, 'precision' => null, 'null' => true, 'default' => null, 'comment' => '最終ログイン日時'],
        'created' => ['type' => 'datetime', 'length' => null, 'precision' => null, 'null' => false, 'default' => null, 'comment' => '初回登録日時'],
        'modified' => ['type' => 'datetime', 'length' => null, 'precision' => null, 'null' => false, 'default' => null, 'comment' => '更新日時'],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'account' => ['type' => 'unique', 'columns' => ['account'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_bin'
        ],
    ];
    // phpcs:enable
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
