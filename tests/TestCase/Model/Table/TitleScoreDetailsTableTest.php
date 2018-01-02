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
        $ranking = $this->TitleScoreDetails->findRanking($country, 2017, 1);

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
        $ranking = $this->TitleScoreDetails->findRanking($country, 2018, 1);

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
        $recent = $this->TitleScoreDetails->findRecent($country, 2017);

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
        $recent = $this->TitleScoreDetails->findRecent($country, 2018);

        $this->assertEquals('', $recent);
    }
}
