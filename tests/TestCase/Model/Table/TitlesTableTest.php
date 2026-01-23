<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Model\Table;

use Cake\I18n\FrozenDate;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Inflector;
use Cake\Utility\Security;
use Gotea\Model\Table\TitlesTable;

/**
 * タイトルモデルのテストケース
 *
 * @property \Gotea\Model\Table\TitlesTable $Titles
 */
class TitlesTableTest extends TestCase
{
    /**
     * タイトルモデル
     *
     * @var \Gotea\Model\Table\TitlesTable
     */
    public $Titles;

    /**
     * Fixtures
     *
     * @var array
     */
    public array $fixtures = [
        'app.Titles',
        'app.RetentionHistories',
        'app.Players',
        'app.Countries',
        'app.Ranks',
        'app.PlayerRanks',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Titles') ? [] : ['className' => TitlesTable::class];
        $this->Titles = TableRegistry::getTableLocator()->get('Titles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Titles);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertEquals($this->Titles->getTable(), 'titles');
        $this->assertEquals($this->Titles->getDisplayField(), 'name');
        $this->assertEquals($this->Titles->getPrimaryKey(), 'id');

        // Association
        $associations = collection($this->Titles->associations());
        $compare = ['Countries', 'RetentionHistories'];
        $this->assertEquals($associations->count(), count($compare));
        $associations->each(function ($a) use ($compare) {
            $this->assertTrue(in_array($a->getName(), $compare, true));
        });
    }

    /**
     * バリデーション
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $params = [
            'country_id' => 1,
            'name' => '12345678901234567890',
            'name_english' => '123456789012345678901234567890',
            'holding' => 1,
            'sort_order' => 1,
            'html_file_name' => 'test-1',
            'html_file_modified' => '2018/01/01',
        ];

        // success
        $result = $this->Titles->newEntity($params);
        $this->assertEmpty($result->getErrors());

        // requirePresence
        foreach ($params as $name => $value) {
            $data = $params;
            unset($data[$name]);
            $result = $this->Titles->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // notEmpty
        $names = [
            'country_id',
            'name',
            'name_english',
            'holding',
            'sort_order',
            'html_file_name',
            'html_file_modified',
            'is_team',
            'is_closed',
            'is_output',
            'is_official',
        ];
        foreach ($params as $name => $value) {
            $data = $params;
            unset($data[$name]);
            $result = $this->Titles->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // integer
        $names = ['country_id', 'holding', 'sort_order', 'html_file_holding'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = '1a';
            $result = $this->Titles->newEntity($data);
            $this->assertNotEmpty($result->getErrors());

            $data[$name] = 'test';
            $result = $this->Titles->newEntity($data);
            $this->assertNotEmpty($result->getErrors());

            $data[$name] = '0.5';
            $result = $this->Titles->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // alphaNumeric
        $names = ['name_english'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = 'テスト';
            $result = $this->Titles->newEntity($data);
            $this->assertNotEmpty($result->getErrors());

            $data[$name] = 'test/1';
            $result = $this->Titles->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // maxLength
        $names = [
            'name' => 30,
            'name_english' => 60,
            'holding' => 3,
            'sort_order' => 2,
            'html_file_name' => 30,
            'html_file_holding' => 3,
            'remarks' => 500,
        ];
        foreach ($names as $name => $length) {
            $data = $params;
            $data[$name] = Security::randomString($length + 1);
            $result = $this->Titles->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // date
        $names = ['html_file_modified'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = '20180101';
            $result = $this->Titles->newEntity($data);
            $this->assertNotEmpty($result->getErrors());

            $data[$name] = 'testtest';
            $result = $this->Titles->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // boolean
        $names = ['is_team', 'is_closed', 'is_output', 'is_official'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = '0.5';
            $result = $this->Titles->newEntity($data);
            $this->assertNotEmpty($result->getErrors());

            $data[$name] = 'test';
            $result = $this->Titles->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // allowEmptyString
        $names = ['remarks', 'html_file_holding'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = '';
            $result = $this->Titles->newEntity($data);
            $this->assertEmpty($result->getErrors());
        }

        // id
        $data = $params;
        $data['id'] = '';
        $exist = $this->Titles->get(1);
        $result = $this->Titles->patchEntity($exist, $data);
        $this->assertNotEmpty($result->getErrors());

        // custom (ascii value only)
        $data = $params;
        $data['html_file_name'] = 'テスト';
        $result = $this->Titles->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        $data['html_file_name'] = '/test';
        $result = $this->Titles->newEntity($data);
        $this->assertNotEmpty($result->getErrors());
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        // country_id が存在しない値
        $result = $this->Titles->newEntity([
            'country_id' => 999,
        ]);
        $this->assertFalse($this->Titles->checkRules($result));

        // 成功
        $result = $this->Titles->newEntity([
            'country_id' => 1,
        ]);
        $this->assertTrue($this->Titles->checkRules($result));
    }

    /**
     * リスト形式での取得
     *
     * @return void
     */
    public function testFindSortedList()
    {
        $titles = $this->Titles->findSortedList();
        $this->assertIsArray($titles->toArray());
        $this->assertNotNull($titles);

        $titles->all()->each(function ($value, $key) {
            $this->assertIsInt($key);
            $this->assertIsString($value);
        });
    }

    /**
     * タイトル検索（データ有り）
     *
     * @return void
     */
    public function testFindTitles()
    {
        $titles = $this->Titles->findTitles([
            'country_id' => 1,
        ]);
        $this->assertGreaterThan(0, $titles->count());
        $titles->all()->each(function ($item) {
            $this->assertEquals($item->country_id, 1);
            $this->assertFalse($item->is_closed);
            $this->assertTrue($item->is_output);
        });

        $titles = $this->Titles->findTitles([
            'search_closed' => true,
        ]);
        $this->assertGreaterThan(0, $titles->count());
        $titles->all()->each(function ($item) {
            $this->assertTrue(in_array($item->is_closed, [true, false], true));
        });

        $titles = $this->Titles->findTitles([
            'search_non_output' => true,
        ]);
        $this->assertGreaterThan(0, $titles->count());
        $titles->all()->each(function ($item) {
            $this->assertTrue(in_array($item->is_output, [true, false], true));
        });
    }

    /**
     * タイトル検索（データ無し）
     *
     * @return void
     */
    public function testFindTitlesNoData()
    {
        $titles = $this->Titles->findTitles([
            'country_id' => 99,
        ]);
        $this->assertEquals(0, $titles->count());
    }

    /**
     * エンティティの生成
     *
     * @return void
     */
    public function testCreateEntity()
    {
        $data = [
            'Name' => 'test',
            'CountryId' => 1,
            'Holding' => 1,
            'sort_order' => 1,
            'htmlFileModified' => FrozenDate::parse('2017-01-04'),
        ];
        /** @var \Gotea\Model\Entity\Title $entity */
        $entity = $this->Titles->createEntity(null, $data);
        foreach ($entity->getVisible() as $key) {
            $this->assertEquals(Inflector::underscore($key), $key);
        }
    }
}
