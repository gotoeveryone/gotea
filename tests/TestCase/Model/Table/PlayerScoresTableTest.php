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
    protected $fixtures = [
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
        $ranking = $this->PlayerScores->findRanking($country, 2013, 3, 'point');

        $this->assertGreaterThan(0, $ranking->count());

        $win = null;
        $lose = null;
        $ranking->each(function ($item) use ($win, $lose) {
            // 0勝は存在しない
            $this->assertNotEquals($item->win_point, 0);
            if ($win !== null) {
                $this->assertGreaterThanOrEqual($win, $item->win_point);
                // 勝数が同じ場合、敗数の昇順
                if ($win === $item->win_point) {
                    $this->assertLessThanOrEqual($lose, $item->lose_point);
                    $lose = $item->lose_point;
                } else {
                    // 勝数が変わった場合は敗数を0に
                    $lose = 0;
                }
            }
            $win = $item->win_point;
            $lose = $item->lose_point;
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
        $ranking = $this->PlayerScores->findRanking($country, 2013, 3, 'percent');

        $this->assertGreaterThan(0, $ranking->count());

        $percentage = null;
        $win = null;
        $lose = null;
        $ranking->each(function ($item) use ($percentage, $win, $lose) {
            // 0%は存在しない
            $this->assertNotEquals($item->win_percent, 0);
            if ($percentage !== null) {
                $this->assertGreaterThanOrEqual($percentage, $item->win_percent);
                // 勝率が同じ場合、勝数の昇順
                if ($percentage === $item->win_percent) {
                    $this->assertGreaterThanOrEqual($win, $item->win_point);
                    // 勝数が同じ場合、敗数の昇順
                    if ($win === $item->win_point) {
                        $this->assertLessThanOrEqual($lose, $item->lose_point);
                        $lose = $item->lose_point;
                    } else {
                        // 勝数が変わった場合は敗数を0に
                        $lose = 0;
                    }
                } else {
                    // 勝率が変わった場合は勝数をnullに
                    $win = null;
                }
            }
            $percentage = $item->win_percent;
            $win = $item->win_point;
            $lose = $item->lose_point;
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
