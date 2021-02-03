<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Model\Table;

use Cake\TestSuite\TestCase;
use Cake\Utility\Security;
use Gotea\Model\Table\UsersTable;

/**
 * Gotea\Model\Table\UsersTable Test Case
 */
class UsersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Gotea\Model\Table\UsersTable
     */
    protected $Users;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Users') ? [] : ['className' => UsersTable::class];
        $this->Users = $this->getTableLocator()->get('Users', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Users);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->Users->initialize([]);
        $this->assertEquals($this->Users->getTable(), 'users');
        $this->assertEquals($this->Users->getDisplayField(), 'name');
        $this->assertEquals($this->Users->getPrimaryKey(), 'id');
    }

    /**
     * バリデーション
     *
     * @return void
     */
    public function testValidationDefault()
    {
        // 必須フィールドを埋める
        $params = [
            'account' => 'testuser',
            'name' => 'TestUser',
            'password' => 'testpassword',
        ];

        // success
        $result = $this->Users->newEntity($params);
        $this->assertEmpty($result->getErrors());

        // nonNegativeInteger
        $result = $this->Users->newEntity($params + ['id' => -1]);
        $this->assertNotEmpty($result->getErrors());

        // requirePresence
        foreach ($params as $name => $value) {
            $data = $params;
            unset($data[$name]);
            $result = $this->Users->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // maxLength
        $schemas = [
            'account' => 10,
            'name' => 50,
            'password' => 255,
        ];
        foreach ($schemas as $name => $length) {
            $data = $params;
            $value = Security::randomString($length + 1);
            $data[$name] = $value;
            $result = $this->Users->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // datetime
        $fields = ['last_logged'];
        foreach ($fields as $field) {
            $data = $params;
            $data[$field] = '20200101';
            $result = $this->Users->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
            $data[$field] = 'testtest';
            $result = $this->Users->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
            $data[$field] = '2020-02-03:09';
            $result = $this->Users->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules(): void
    {
        // 必須フィールドを埋める
        $params = [
            'account' => 'testuser',
            'name' => 'TestUser',
            'password' => 'testpassword',
        ];

        /** @var \Gotea\Model\Entity\User $data */
        $data = $this->Users->find()->first();
        $params['account'] = $data->account;
        $result = $this->Users->newEntity($params);
        $this->Users->save($result);
        $this->assertNotEmpty($result->getErrors());
    }
}
