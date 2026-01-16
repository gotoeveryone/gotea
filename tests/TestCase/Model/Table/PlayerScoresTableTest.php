<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Model\Table;

use Cake\TestSuite\TestCase;
use Gotea\Model\Table\CountriesTable;
use Gotea\Model\Table\PlayerScoresTable;

/**
 * Gotea\Model\Table\PlayerScoresTable Test Case
 */
class PlayerScoresTableTest extends TestCase
{
    /**
     * 所属国
     *
     * @var \Gotea\Model\Table\CountriesTable
     */
    public $Countries;

    /**
     * Test subject
     *
     * @var \Gotea\Model\Table\PlayerScoresTable
     */
    protected $PlayerScores;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.PlayerScores',
        'app.Players',
        'app.Countries',
        'app.Ranks',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Countries') ? [] : ['className' => CountriesTable::class];
        $this->Countries = $this->getTableLocator()->get('Countries', $config);

        $config = $this->getTableLocator()->exists('PlayerScores') ? [] : ['className' => PlayerScoresTable::class];
        $this->PlayerScores = $this->getTableLocator()->get('PlayerScores', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Countries);
        unset($this->PlayerScores);

        parent::tearDown();
    }

    /**
     * ランキングデータ取得（データ有り）
     *
     * @return void
     */
    public function testFindRankingByPoint()
    {
        $country = $this->Countries->get(1);
        $ranking = $this->PlayerScores->findRanking($country, 2013, 3, 'point')->all();

        $this->assertGreaterThan(0, $ranking->count());

        $rankingList = $ranking->toList();
        $ranking->each(function ($item, $idx) use ($rankingList) {
            $beforeWin = $idx > 0 ? $rankingList[$idx - 1]->win_point : null;
            $beforeLose = $idx > 0 ? $rankingList[$idx - 1]->lose_point : null;
            // 0勝は存在しない
            $this->assertNotEquals($item->win_point, 0);
            if ($beforeWin !== null) {
                $this->assertLessThanOrEqual($beforeWin, $item->win_point);
                // 勝数が同じ場合、敗数の昇順
                if ($beforeWin === $item->win_point) {
                    $this->assertGreaterThanOrEqual($beforeLose, $item->lose_point);
                }
            }
            $this->assertEquals(2013, $item->target_year);
        });
    }

    /**
     * ランキングデータ取得（データ有り）
     *
     * @return void
     */
    public function testFindRankingByPercent()
    {
        $country = $this->Countries->get(1);
        $ranking = $this->PlayerScores->findRanking($country, 2013, 3, 'percent')->all();

        $this->assertGreaterThan(0, $ranking->count());

        $rankingList = $ranking->toList();
        $ranking->each(function ($item, $idx) use ($rankingList) {
            $beforeWin = $idx > 0 ? $rankingList[$idx - 1]->win_point : null;
            $beforeLose = $idx > 0 ? $rankingList[$idx - 1]->lose_point : null;
            $beforePercentage = $idx > 0 ? $rankingList[$idx - 1]->win_percent : null;
            // 0%は存在しない
            $this->assertNotEquals($item->win_percent, 0);
            if ($beforePercentage !== null) {
                $this->assertLessThanOrEqual($beforePercentage, $item->win_percent);
                // 勝率が同じ場合、勝数の昇順
                if ($beforePercentage === $item->win_percent) {
                    $this->assertLessThanOrEqual($beforeWin, $item->win_point);
                    // 勝数が同じ場合、敗数の昇順
                    if ($beforeWin === $item->win_point) {
                        $this->assertGreaterThanOrEqual($beforeLose, $item->lose_point);
                    }
                }
            }
            $this->assertEquals(2013, $item->target_year);
        });
    }

    /**
     * ランキングデータ取得（データ無し）
     *
     * @return void
     */
    public function testFindRankingNoData()
    {
        $country = $this->Countries->get(1);
        $ranking = $this->PlayerScores->findRanking($country, 2014, 3);

        $this->assertEquals(0, $ranking->count());
    }
}
