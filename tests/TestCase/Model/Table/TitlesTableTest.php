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
        'app.titles',
        'app.retention_histories',
        'app.players',
        'app.countries',
        'app.ranks',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Titles') ? [] : ['className' => TitlesTable::class];
        $this->Titles = TableRegistry::get('Titles', $config);
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
        foreach ($entity->visibleProperties() as $key => $value) {
            $this->assertEquals(Inflector::underscore($key), $key);
        }
    }
}
