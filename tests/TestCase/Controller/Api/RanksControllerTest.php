<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Controller\Api;

use Cake\TestSuite\IntegrationTestTrait;

/**
 * Gotea\Controller\Api\RanksController Test Case
 */
class RanksControllerTest extends ApiTestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public array $fixtures = [
        'app.Ranks',
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
    public function testIndex()
    {
        $this->get(['_name' => 'api_ranks']);
        $this->assertResponseSuccess();
    }
}
