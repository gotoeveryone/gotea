<?php
namespace Gotea\Test\TestCase\Model\Table;

use Cake\I18n\FrozenDate;
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
     * タイトル成績検索
     *
     * @return void
     */
    public function testFindScores()
    {
        $scores = $this->TitleScoreDetails->findScores(
            $this->TitleScoreDetails->query());

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
        $ranking = $this->TitleScoreDetails->findRanking($country, FrozenDate::now()->year, 1);

        $this->assertGreaterThan(0, $ranking->count());
    }

    /**
     * ランキングデータ取得（データ無し）
     *
     * @return void
     */
    public function testFindRankingNoData()
    {
        $country = $this->Countries->get(1);
        $ranking = $this->TitleScoreDetails->findRanking($country, FrozenDate::now()->addYears(1)->year, 1);

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
        $recent = $this->TitleScoreDetails->findRecent($country, FrozenDate::now()->year);

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
        $recent = $this->TitleScoreDetails->findRecent($country, FrozenDate::now()->addYears(1)->year);

        $this->assertEquals('', $recent);
    }
}
