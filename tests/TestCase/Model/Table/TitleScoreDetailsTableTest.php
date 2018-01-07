<?php
namespace Gotea\Test\TestCase\Model\Table;

use Cake\I18n\Date;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Gotea\Model\Table\TitleScoreDetailsTable;

/**
 * タイトル成績詳細モデルのテストケース
 *
 * @property \Gotea\Model\Table\CountriesTable $Countries
 * @property \Gotea\Model\Table\TitleScoreDetailsTable $TitleScoreDetails
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
        'app.title_score_details',
        'app.title_scores',
        'app.titles',
        'app.retention_histories',
        'app.players',
        'app.player_scores',
        'app.countries',
        'app.ranks',
        'app.player_ranks',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Countries') ? [] : ['className' => 'Gotea\Model\Table\CountriesTable'];
        $this->Countries = TableRegistry::get('Countries', $config);

        $config = TableRegistry::exists('TitleScoreDetails') ? [] : ['className' => 'Gotea\Model\Table\TitleScoreDetailsTable'];
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
     * バリデーション
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $params = [
            'title_score_id' => 1,
            'player_id' => 1,
            'rank_id' => 1,
            'division' => 'win',
        ];

        // success
        $result = $this->TitleScoreDetails->newEntity($params);
        $this->assertEmpty($result->errors());
        $this->assertEmpty($result->getErrors());

        // requirePresence
        foreach ($params as $name => $value) {
            $data = $params;
            unset($data[$name]);
            $result = $this->TitleScoreDetails->newEntity($data);
            $this->assertNotEmpty($result->errors());
            $this->assertNotEmpty($result->getErrors());
        }

        // integer
        $names = ['title_score_id', 'player_id', 'rank_id'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = '1a';
            $result = $this->TitleScoreDetails->newEntity($data);
            $this->assertNotEmpty($result->errors());
            $this->assertNotEmpty($result->getErrors());

            $data[$name] = 'test';
            $result = $this->TitleScoreDetails->newEntity($data);
            $this->assertNotEmpty($result->errors());
            $this->assertNotEmpty($result->getErrors());

            $data[$name] = '0.5';
            $result = $this->TitleScoreDetails->newEntity($data);
            $this->assertNotEmpty($result->errors());
            $this->assertNotEmpty($result->getErrors());
        }

        // allowEmpty
        // id
        $data = $params;
        $data['id'] = '';
        $exist = $this->TitleScoreDetails->get(1);
        $result = $this->TitleScoreDetails->patchEntity($exist, $data);
        $this->assertNotEmpty($result->errors());
        $this->assertNotEmpty($result->getErrors());
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
     * ランキングデータ取得（データ有り）
     *
     * @return void
     */
    public function testFindRanking()
    {
        $country = $this->Countries->get(1);
        $from = Date::createFromDate(2017, 1, 1);
        $to = Date::createFromDate(2017, 12, 31);
        $ranking = $this->TitleScoreDetails->findRanking($country, 20, $from, $to);

        $this->assertGreaterThan(0, $ranking->count());

        $win = 0;
        $lose = 0;
        $ranking->each(function ($item) use ($win, $lose) {
            if (!$win) {
                $this->assertGreaterThanOrEqual($win, $item->win_point);
                // 勝数が同じ場合、敗数は昇順
                if ($win === $item->win_point) {
                    $this->assertLessThanOrEqual($lose, $item->lose_point);
                }
            }
            $win = $item->win_point;
            $lose = $item->lose_point;
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
        $from = Date::createFromDate(2018, 1, 1);
        $to = Date::createFromDate(2018, 12, 31);
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
        $from = Date::createFromDate(2017, 1, 1);
        $to = Date::createFromDate(2017, 12, 31);
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
        $from = Date::createFromDate(2018, 1, 1);
        $to = Date::createFromDate(2018, 12, 31);
        $recent = $this->TitleScoreDetails->findRecent($country, $from, $to);

        $this->assertEquals('', $recent);
    }
}
