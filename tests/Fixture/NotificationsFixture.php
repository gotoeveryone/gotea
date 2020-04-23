<?php
namespace Gotea\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * NotificationsFixture
 */
class NotificationsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'サロゲートキー', 'autoIncrement' => true, 'precision' => null],
        'title' => ['type' => 'string', 'length' => 100, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'タイトル', 'precision' => null, 'fixed' => null],
        'content' => ['type' => 'text', 'length' => null, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '本文', 'precision' => null],
        'is_draft' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '1', 'comment' => '下書き', 'precision' => null],
        'published' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '公開日時', 'precision' => null],
        'is_permanent' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '0', 'comment' => '恒久表示フラグ', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '初回登録日時', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '更新日時', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        $this->records = [
            [
                'title' => 'Test Notification',
                'content' => 'This is test notification.',
                'is_draft' => 1,
                'published' => '2019-04-06 12:37:11',
                'created' => '2019-04-06 10:37:11',
                'modified' => '2019-04-06 10:37:11',
            ],
            [
                'title' => 'Test Notification',
                'content' => 'This is test notification.',
                'is_draft' => 1,
                'published' => '2019-04-06 10:37:11',
                'created' => '2019-04-06 10:37:11',
                'modified' => '2019-04-06 10:37:11',
            ],
            [
                'title' => 'Test Notification',
                'content' => 'This is test notification.',
                'is_draft' => 1,
                'published' => '2019-04-06 10:37:11',
                'created' => '2019-04-06 10:37:11',
                'modified' => '2019-04-06 10:37:11',
            ],
            [
                'title' => 'Test Notification',
                'content' => 'This is test notification.',
                'is_draft' => 1,
                'published' => '2019-04-06 11:00:11',
                'created' => '2019-04-06 10:37:11',
                'modified' => '2019-04-06 10:37:11',
            ],
            [
                'title' => 'Test Notification',
                'content' => 'This is test notification.',
                'is_draft' => 1,
                'published' => '2019-04-07 10:37:11',
                'created' => '2019-04-06 10:37:11',
                'modified' => '2019-04-06 10:37:11',
            ],
        ];
        parent::init();
    }
}
