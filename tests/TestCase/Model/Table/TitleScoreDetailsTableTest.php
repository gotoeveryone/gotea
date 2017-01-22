<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TitleScoreDetailsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TitleScoreDetailsTable Test Case
 */
class TitleScoreDetailsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\TitleScoreDetailsTable
     */
    public $TitleScoreDetails;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.title_score_details',
        'app.title_scores',
        'app.titles',
        'app.retention_histories',
        'app.players',
        'app.countries',
        'app.ranks',
        'app.organizations',
        'app.player_scores'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('TitleScoreDetails') ? [] : ['className' => 'App\Model\Table\TitleScoreDetailsTable'];
        $this->TitleScoreDetails = TableRegistry::get('TitleScoreDetails', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TitleScoreDetails);

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
