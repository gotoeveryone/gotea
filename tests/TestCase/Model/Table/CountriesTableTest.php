<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CountriesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CountriesTable Test Case
 */
class CountriesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CountriesTable
     */
    public $Country;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.countries',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Countries') ? [] : ['className' => CountriesTable::class];
        $this->Countries = TableRegistry::get('Countries', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Countries);

        parent::tearDown();
    }

    /**
     * タイトル保持所属国のみ
     *
     * @return void
     */
    public function testFindAllHasCode()
    {
        $countries = $this->Countries->findAllHasCode(true);
        $countries->each(function($item, $key) {
            $this->assertTrue($item->has_title);
        });
    }

    /**
     * 全件
     *
     * @return void
     */
    public function testFindAll()
    {
        $countries = $this->Countries->findAllHasCode();
        $countries->each(function($item, $key) {
            $this->assertContains($item->has_title, [true, false]);
        });
    }
}
