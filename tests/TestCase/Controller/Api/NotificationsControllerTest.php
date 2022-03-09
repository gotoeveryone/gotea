<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Controller\Api;

/**
 * Gotea\Controller\Api\NotificationsController Test Case
 *
 * @uses \Gotea\Controller\Api\NotificationsController
 */
class NotificationsControllerTest extends ApiTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Notifications',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->createSession();
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->get('/api/notifications?limit=2');
        $this->assertResponseSuccess();
        $this->assertNotEquals($this->getEmptyResponse(), $this->_getBodyAsString());
        $response = json_decode($this->_getBodyAsString())->response;
        $this->assertEquals($response->total, 5);
        $this->assertEquals(count($response->items), 2);
    }
}
