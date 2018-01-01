<?php
namespace Gotea\Test\TestCase\Model\Table;

use Cake\I18n\FrozenDate;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Gotea\Model\Table\TitleScoresTable;

/**
 * タイトル成績モデルのテストケース
 *
 * @property \Gotea\Model\Table\TitleScoresTable $TitleScores
 */
class TitleScoresTableTest extends TestCase
{
    /**
     * タイトル成績
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
        'app.title_score_details',
        'app.players',
        'app.countries',
        'app.ranks',
        'app.organizations',
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
     * タイトル成績検索（データ有り）
     *
     * @return void
     */
    public function testFindMatches()
    {
        $scores = $this->TitleScores->findMatches([
            'target_year' => 2017,
        ]);
        $this->assertGreaterThan(0, $scores->count());
    }

    /**
     * タイトル成績検索（データ無し）
     *
     * @return void
     */
    public function testFindMatchesNoData()
    {
        $scores = $this->TitleScores->findMatches([
            'target_year' => 2018,
        ]);
        $this->assertEquals(0, $scores->count());
    }
}
