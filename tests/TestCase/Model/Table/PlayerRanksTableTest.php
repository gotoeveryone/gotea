<?php
namespace Gotea\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Gotea\Model\Table\PlayerRanksTable;

/**
 * 棋士昇段モデルのテストケース
 *
 * @property \Gotea\Model\Table\PlayerRanksTable $PlayerRanks
 */
class PlayerRanksTableTest extends TestCase
{

    /**
     * 棋士昇段
     *
     * @var \Gotea\Model\Table\PlayerRanksTable
     */
    public $PlayerRanks;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.player_ranks',
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
        $config = TableRegistry::exists('PlayerRanks') ? [] : ['className' => PlayerRanksTable::class];
        $this->PlayerRanks = TableRegistry::get('PlayerRanks', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PlayerRanks);

        parent::tearDown();
    }

    /**
     * 昇段情報取得（データ有り）
     *
     * @return void
     */
    public function testFindRanks()
    {
        $ranks = $this->PlayerRanks->findRanks(1);
        $this->assertGreaterThan(0, $ranks->count());
    }

    /**
     * 昇段情報取得（データ無し）
     *
     * @return void
     */
    public function testFindRanksNoData()
    {
        $ranks = $this->PlayerRanks->findRanks(2);
        $this->assertEquals(0, $ranks->count());
    }
}
