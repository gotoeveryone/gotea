<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Gotea\Model\Table\TableTemplatesTable;

/**
 * Gotea\Model\Table\TableTemplates Test Case
 */
class TableTemplatesTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Gotea\Model\Table\TableTemplates
     */
    protected $TableTemplates;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.TableTemplates',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('TableTemplates') ? [] : ['className' => TableTemplatesTable::class];
        $this->TableTemplates = TableRegistry::getTableLocator()->get('TableTemplates', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->TableTemplates);

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
        ];

        // success
        $result = $this->TableTemplates->newEntity($params);
        $this->assertEmpty($result->getErrors());

        // negativeInteger
        $data = $params;
        $data['id'] = -1;
        $result = $this->TableTemplates->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        // requirePresence
        $keys = array_keys($params);
        foreach ($keys as $name) {
            $data = $params;
            unset($data[$name]);
            $result = $this->TableTemplates->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // notEmpty
        $keys = array_keys($params);
        foreach ($keys as $name) {
            $data = $params;
            $data[$name] = '';
            $result = $this->TableTemplates->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // maxLength
        // title
        $data = $params;
        $data['title'] = substr(bin2hex(random_bytes(101)), 0, 101);
        $result = $this->TableTemplates->newEntity($data);
        $this->assertNotEmpty($result->getErrors());
    }
}
