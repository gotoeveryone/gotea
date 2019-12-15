<?php

namespace Gotea\Test\TestCase\Model\Table;

use Cake\I18n\FrozenDate;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Inflector;
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
    public $fixtures = [
        'app.Titles',
        'app.RetentionHistories',
        'app.Players',
        'app.Countries',
        'app.Ranks',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
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
    public function tearDown()
    {
        unset($this->Titles);

        parent::tearDown();
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
            'html_file_name' => 'test/1',
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

        // integer
        $names = ['holding', 'sort_order'];
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
        $data = $params;
        $data['name_english'] = 'テスト';
        $result = $this->Titles->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        $data['name_english'] = 'test/1';
        $result = $this->Titles->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        // custom (ascii value only)
        $data = $params;
        $data['html_file_name'] = 'テスト';
        $result = $this->Titles->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        // maxLength
        // name
        $data = $params;
        $data['name'] = substr(bin2hex(random_bytes(31)), 0, 31);
        $result = $this->Titles->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        // name_english
        $data = $params;
        $data['name_english'] = substr(bin2hex(random_bytes(61)), 0, 61);
        $result = $this->Titles->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        // html_file_name
        $data = $params;
        $data['html_file_name'] = substr(bin2hex(random_bytes(31)), 0, 31);
        $result = $this->Titles->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        // date
        // html_file_modified
        $data = $params;
        $data['html_file_modified'] = '20180101';
        $result = $this->Titles->newEntity($data);
        $this->assertNotEmpty($result->getErrors());
        $data['html_file_modified'] = 'testtest';
        $result = $this->Titles->newEntity($data);
        $this->assertNotEmpty($result->getErrors());
    }

    /**
     * リスト形式での取得
     *
     * @return void
     */
    public function testFindSortedList()
    {
        $titles = $this->Titles->findSortedList();
        $this->assertInternalType('array', $titles->toArray());
        $this->assertNotNull($titles);

        $titles->each(function ($value, $key) {
            $this->assertInternalType('int', $key);
            $this->assertInternalType('string', $value);
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
        $titles->each(function ($item) {
            $this->assertEquals($item->country_id, 1);
            $this->assertFalse($item->is_closed);
            $this->assertTrue($item->is_output);
        });

        $titles = $this->Titles->findTitles([
            'search_closed' => true,
        ]);
        $this->assertGreaterThan(0, $titles->count());
        $titles->each(function ($item) {
            $this->assertTrue(in_array($item->is_closed, [true, false], true));
        });

        $titles = $this->Titles->findTitles([
            'search_non_output' => true,
        ]);
        $this->assertGreaterThan(0, $titles->count());
        $titles->each(function ($item) {
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
        $entity = $this->Titles->createEntity(null, $data);
        foreach ($entity->getVisible() as $key => $value) {
            $this->assertEquals(Inflector::underscore($key), $key);
        }
    }
}
