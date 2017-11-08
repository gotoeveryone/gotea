<?php
namespace Gotea\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Gotea\Model\Table\TitleScoresTable;

/**
 * Gotea\Model\Table\TitleScoresTable Test Case
 */
class TitleScoresTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Gotea\Model\Table\TitleScoresTable
     */
    public $TitleScores;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.title_scores',
        'app.titles',
        'app.retention_histories',
        'app.players',
        'app.countries',
        'app.ranks',
        'app.organizations',
        'app.player_scores',
        'app.title_score_details'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('TitleScores') ? [] : ['className' => 'Gotea\Model\Table\TitleScoresTable'];
        $this->TitleScores = TableRegistry::get('TitleScores', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TitleScores);

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
