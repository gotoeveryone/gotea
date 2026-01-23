<?php
namespace Gotea\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * NotificationsFixture
 */
class NotificationsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'title' => 'Test Notification',
                'content' => 'This is test notification.',
                'is_draft' => 1,
                'published' => '2019-04-06 12:37:11',
                'created' => '2019-04-06 10:37:11',
                'modified' => '2019-04-06 10:37:11',
            ],
            [
                'id' => 2,
                'title' => 'Test Notification',
                'content' => 'This is test notification.',
                'is_draft' => 1,
                'published' => '2019-04-06 10:37:11',
                'created' => '2019-04-06 10:37:11',
                'modified' => '2019-04-06 10:37:11',
            ],
            [
                'id' => 3,
                'title' => 'Test Notification',
                'content' => 'This is test notification.',
                'is_draft' => 1,
                'published' => '2019-04-06 10:37:11',
                'created' => '2019-04-06 10:37:11',
                'modified' => '2019-04-06 10:37:11',
            ],
            [
                'id' => 4,
                'title' => 'Test Notification',
                'content' => 'This is test notification.',
                'is_draft' => 1,
                'published' => '2019-04-06 11:00:11',
                'created' => '2019-04-06 10:37:11',
                'modified' => '2019-04-06 10:37:11',
            ],
            [
                'id' => 5,
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
