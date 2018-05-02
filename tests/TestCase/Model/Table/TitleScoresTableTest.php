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
        // 対局年
        $year = 2017;
        $scores = $this->TitleScores->findMatches([
            'target_year' => $year,
        ]);
        $this->assertGreaterThan(0, $scores->count());
        $scores->each(function ($item) use ($year) {
            $this->assertEquals($item->started->year, $year);
            $this->assertEquals($item->ended->year, $year);
        });

        // タイトル名
        $name = 'World';
        $scores = $this->TitleScores->findMatches([
            'title_name' => $name,
        ]);
        $this->assertGreaterThan(0, $scores->count());
        $scores->each(function ($item) use ($name) {
            $this->assertContains($name, $item->name);
        });

        // 対局日
        $start = '2017/12/01';
        $end = '2017/12/31';
        $scores = $this->TitleScores->findMatches([
            'started' => $start,
            'ended' => $end,
        ]);
        $this->assertGreaterThan(0, $scores->count());
        $scores->each(function ($item) use ($start, $end) {
            $this->assertGreaterThanOrEqual($start, $item->started->format('Y/m/d'));
            $this->assertLessThanOrEqual($end, $item->started->format('Y/m/d'));
        });
    }

    /**
     * タイトル成績検索（データ無し）
     *
     * @return void
     */
    public function testFindMatchesNoData()
    {
        $scores = $this->TitleScores->findMatches([
            'target_year' => 9999,
        ]);
        $this->assertEquals(0, $scores->count());
    }
}
