<?php
namespace Gotea\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Gotea\Model\Table\RanksTable;

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
        $config = TableRegistry::getTableLocator()->exists('Ranks') ? [] : ['className' => RanksTable::class];
        $this->Ranks = TableRegistry::getTableLocator()->get('Ranks', $config);
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

    /**
     * 指定段位のデータが取得できるかのテスト
     *
     * @return void
     */
    public function testFindByRank()
    {
        $rank = $this->Ranks->findByRank(5);
        $this->assertEquals(5, $rank->rank_numeric);

        // 段位指定なしの場合は初段
        $rank = $this->Ranks->findByRank();
        $this->assertEquals(1, $rank->rank_numeric);
    }
}
