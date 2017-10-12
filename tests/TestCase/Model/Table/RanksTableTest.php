<?php
namespace Gotea\Test\TestCase\Model\Table;

use Gotea\Model\Table\RanksTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * Gotea\Model\Table\RanksTable Test Case
 */
class RanksTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Gotea\Model\Table\RanksTable
     */
    public $Ranks;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ranks'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Ranks') ? [] : ['className' => RanksTable::class];
        $this->Ranks = TableRegistry::get('Ranks', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Ranks);

        parent::tearDown();
    }

    /**
     * プロのみ抽出するテスト
     *
     * @return void
     */
    public function testFindProfessional()
    {
        $ranks = $this->Ranks->findProfessional();
        $ranks->each(function ($item, $key) {
            $this->assertNotNull($item->rank_numeric);
        });
    }

    /**
     * 降順に並んでいるかのテスト
     *
     * @return void
     */
    public function testSortedDesc()
    {
        $ranks = $this->Ranks->findProfessional();

        $number = 0;
        $ranks->each(function ($item, $key) use (&$number) {
            if ($number) {
                $this->assertLessThanOrEqual($number, $item->rank_numeric);
            }
            $number = $item->rank_numeric;
        });
    }
}
