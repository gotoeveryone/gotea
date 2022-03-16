<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Controller\Api;

/**
 * Gotea\Controller\Api\TableTemplatesController Test Case
 *
 * @uses \Gotea\Controller\Api\TableTemplatesController
 */
class TableTemplatesControllerTest extends ApiTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.TableTemplates',
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
        $this->get('/api/table-templates?limit=1');
        $this->assertResponseSuccess();
        $this->assertNotEquals($this->getEmptyResponse(), $this->_getBodyAsString());
        $response = json_decode($this->_getBodyAsString())->response;
        $this->assertEquals($response->total, 2);
        $this->assertEquals(count($response->items), 1);
    }
}
