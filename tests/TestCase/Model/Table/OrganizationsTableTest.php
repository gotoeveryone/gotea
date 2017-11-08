<?php
namespace Gotea\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Gotea\Model\Table\OrganizationsTable;

/**
 * Gotea\Model\Table\OrganizationsTable Test Case
 */
class OrganizationsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Gotea\Model\Table\OrganizationsTable
     */
    public $Organizations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.countries',
        'app.organizations',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Organizations') ? [] : ['className' => OrganizationsTable::class];
        $this->Organizations = TableRegistry::get('Organizations', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Organizations);

        parent::tearDown();
    }

    /**
     * バリデーションテスト（失敗）
     *
     * @return void
     */
    public function testValidationError()
    {
        $entity = $this->Organizations->newEntity([
            'country_id' => 1,
            'name' => null,
        ]);

        // エラー有り
        $this->assertNotEmpty($entity->getErrors());
    }

    /**
     * バリデーションテスト（成功）
     *
     * @return void
     */
    public function testValidationSuccess()
    {
        $entity = $this->Organizations->newEntity([
            'country_id' => 1,
            'name' => 'テスト',
        ]);

        // エラー無し
        $this->assertEmpty($entity->getErrors());
    }

    /**
     * データが取得できたかどうか
     *
     * @return void
     */
    public function testFindSorted()
    {
        $entities = $this->Organizations->findSorted();
        $this->assertInternalType('array', $entities->toArray());
        $this->assertNotNull($entities);
    }
}
