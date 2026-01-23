<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Gotea\Model\Table\NotificationsTable;

/**
 * Gotea\Model\Table\NotificationsTable Test Case
 */
class NotificationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Gotea\Model\Table\NotificationsTable
     */
    public $Notifications;

    /**
     * Fixtures
     *
     * @var array
     */
    public array $fixtures = [
        'app.Notifications',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Notifications') ? [] : ['className' => NotificationsTable::class];
        $this->Notifications = TableRegistry::getTableLocator()->get('Notifications', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Notifications);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $params = [
            'title' => 'Test Notification',
            'content' => 'This is test notification',
            'published' => '2019-04-06 11:26:37',
        ];

        // success
        $result = $this->Notifications->newEntity($params);
        $this->assertEmpty($result->getErrors());

        // negativeInteger
        $data = $params;
        $data['id'] = -1;
        $result = $this->Notifications->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        // requirePresence
        $keys = array_keys($params);
        foreach ($keys as $name) {
            $data = $params;
            unset($data[$name]);
            $result = $this->Notifications->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // notEmpty
        $keys = array_keys($params);
        foreach ($keys as $name) {
            $data = $params;
            $data[$name] = '';
            $result = $this->Notifications->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // maxLength
        // title
        $data = $params;
        $data['title'] = substr(bin2hex(random_bytes(101)), 0, 101);
        $result = $this->Notifications->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        // boolean
        // is_draft
        $data = $params;
        $data['is_draft'] = 'testtest';
        $result = $this->Notifications->newEntity($data);
        $this->assertNotEmpty($result->getErrors());
        // is_permanent
        $data = $params;
        $data['is_permanent'] = 'testtest';
        $result = $this->Notifications->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        // dateTime
        // published
        $data = $params;
        $data['published'] = 'testtesttest';
        $result = $this->Notifications->newEntity($data);
        $this->assertNotEmpty($result->getErrors());
    }

    /**
     * Test findAllNewestArrivals method
     *
     * @return void
     */
    public function testFindAllNewestArrivals()
    {
        $notifications = $this->Notifications->findAllNewestArrivals();
        $before = '';
        $notifications->all()->each(function ($item) use (&$before) {
            if (!$before) {
                $before = $item->published->getTimestamp();
            } else {
                $this->assertGreaterThanOrEqual($item->published->getTimestamp(), $before);
                $before = $item->published->getTimestamp();
            }
        });
    }
}
