<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Model\Table;

use Cake\I18n\FrozenDate;
use Cake\TestSuite\TestCase;

/**
 * タイトル成績詳細モデルのテストケース
 */
class TitleScoreDetailsTableTest extends TestCase
{
    /**
     * 所属国
     *
     * @var \Gotea\Model\Table\CountriesTable
     */
    public $Countries;

    /**
     * タイトル成績詳細
     *
     * @var \Gotea\Model\Table\TitleScoreDetailsTable
     */
    public $TitleScoreDetails;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.TitleScoreDetails',
        'app.TitleScores',
        'app.Titles',
        'app.RetentionHistories',
        'app.Players',
        'app.PlayerScores',
        'app.Countries',
        'app.Ranks',
        'app.PlayerRanks',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Countries') ? [] : ['className' => 'Gotea\Model\Table\CountriesTable'];
        $this->Countries = $this->getTableLocator()->get('Countries', $config);

        $config = $this->getTableLocator()->exists('TitleScoreDetails') ? [] : ['className' => 'Gotea\Model\Table\TitleScoreDetailsTable'];
        $this->TitleScoreDetails = $this->getTableLocator()->get('TitleScoreDetails', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Countries);
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
        $this->TitleScoreDetails->initialize([]);
        $this->assertEquals($this->TitleScoreDetails->getTable(), 'title_score_details');
        $this->assertEquals($this->TitleScoreDetails->getDisplayField(), 'division');
        $this->assertEquals($this->TitleScoreDetails->getPrimaryKey(), 'id');

        // Association
        $associations = collection($this->TitleScoreDetails->associations());
        $compare = ['TitleScores', 'Players'];
        $this->assertEquals($associations->count(), count($compare));
        $associations->each(function ($a) use ($compare) {
            $this->assertTrue(in_array($a->getName(), $compare, true));
        });
    }

    /**
     * バリデーション
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $params = [
            'player_id' => 1,
            'division' => 'win',
        ];

        // success
        $result = $this->TitleScoreDetails->newEntity($params);
        $this->assertEmpty($result->getErrors());

        // requirePresence
        foreach ($params as $name => $value) {
            $data = $params;
            unset($data[$name]);
            $result = $this->TitleScoreDetails->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // integer
        $names = ['title_score_id', 'player_id'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = '1a';
            $result = $this->TitleScoreDetails->newEntity($data);
            $this->assertNotEmpty($result->getErrors());

            $data[$name] = 'test';
            $result = $this->TitleScoreDetails->newEntity($data);
            $this->assertNotEmpty($result->getErrors());

            $data[$name] = '0.5';
            $result = $this->TitleScoreDetails->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // allowEmpty
        $names = ['id', 'title_score_id', 'player_id'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = '';
            $exist = $this->TitleScoreDetails->get(1);
            $result = $this->TitleScoreDetails->patchEntity($exist, $data);
            $this->assertNotEmpty($result->getErrors());
        }
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        // title_score_id が存在しない値
        $result = $this->TitleScoreDetails->newEntity([
            'title_score_id' => 999,
        ]);
        $this->assertFalse($this->TitleScoreDetails->checkRules($result));

        // player_id が存在しない値
        $result = $this->TitleScoreDetails->newEntity([
            'player_id' => 999,
        ]);
        $this->assertFalse($this->TitleScoreDetails->checkRules($result));

        // 成功
        $result = $this->TitleScoreDetails->newEntity([
            'title_score_id' => 1,
            'player_id' => 1,
        ]);
        $this->assertTrue($this->TitleScoreDetails->checkRules($result));
    }

    /**
     * タイトル成績検索
     *
     * @return void
     */
    public function testFindScores()
    {
        $scores = $this->TitleScoreDetails->findScores($this->TitleScoreDetails->query());

        $this->assertGreaterThanOrEqual(0, $scores->count());
    }

    /**
     * 棋士・年指定検索
     *
     * @return void
     */
    public function testFindByPlayerAtYear()
    {
        // 対象なし
        $detail = $this->TitleScoreDetails->findByPlayerAtYear(1, 9999);
        $this->assertNull($detail);

        // 対象あり
        $detail = $this->TitleScoreDetails->findByPlayerAtYear(1, 2017);
        $this->assertNotNull($detail);
        $this->assertEquals(1, $detail->player_id);
        $this->assertEquals(2017, $detail->target_year);
    }

    /**
     * ランキングデータ取得（データ有り）
     *
     * @return void
     */
    public function testFindRankingByPoint()
    {
        $country = $this->Countries->get(1);
        $from = FrozenDate::createFromDate(2017, 1, 1);
        $to = FrozenDate::createFromDate(2017, 12, 31);
        $ranking = $this->TitleScoreDetails->findRanking($country, 20, $from, $to, 'point');

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
            $this->assertEquals(2017, $item->target_year);
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
        $from = FrozenDate::createFromDate(2017, 1, 1);
        $to = FrozenDate::createFromDate(2017, 12, 31);
        $ranking = $this->TitleScoreDetails->findRanking($country, 20, $from, $to, 'percent');

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
            $this->assertEquals(2017, $item->target_year);
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
        $from = FrozenDate::createFromDate(2018, 1, 1);
        $to = FrozenDate::createFromDate(2018, 12, 31);
        $ranking = $this->TitleScoreDetails->findRanking($country, 20, $from, $to);

        $this->assertEquals(0, $ranking->count());
    }

    /**
     * 最新の対局日検索（データ有り）
     *
     * @return void
     */
    public function testRecent()
    {
        $country = $this->Countries->get(1);
        $from = FrozenDate::createFromDate(2017, 1, 1);
        $to = FrozenDate::createFromDate(2017, 12, 31);
        $recent = $this->TitleScoreDetails->findRecent($country, $from, $to);

        $this->assertNotEquals('', $recent);
    }

    /**
     * 最新の対局日検索（データ無し）
     *
     * @return void
     */
    public function testRecentNoData()
    {
        $country = $this->Countries->get(1);
        $from = FrozenDate::createFromDate(2018, 1, 1);
        $to = FrozenDate::createFromDate(2018, 12, 31);
        $recent = $this->TitleScoreDetails->findRecent($country, $from, $to);

        $this->assertEquals('', $recent);
    }
}
