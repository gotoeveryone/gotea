<?php
namespace Gotea\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Gotea\Model\Table\PlayerRanksTable;

/**
 * Gotea\Model\Table\PlayerRanksTable Test Case
 */
class PlayerRanksTableTest extends TestCase
{

    /**
     * Test subject
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
        'app.organizations',
        'app.player_scores',
        'app.retention_histories',
        'app.titles',
        'app.win_details',
        'app.title_scores',
        'app.title_score_details',
        'app.winner',
        'app.lose_details',
        'app.loser',
        'app.draw_details',
        'app.world_win_details',
        'app.world_lose_details',
        'app.world_draw_details'
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
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
