<?php
namespace Gotea\Test\TestCase\Controller\Api;

use Cake\Routing\Exception\MissingRouteException;
use Gotea\Controller\Api\PlayersController;

/**
 * Gotea\Controller\Api\PlayersController Test Case
 */
class PlayersControllerTest extends ApiTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.countries',
        'app.ranks',
        'app.organizations',
        'app.players',
        'app.player_scores',
        'app.title_scores',
        'app.title_score_details',
        'app.updated_points',
    ];

    /**
     * 棋士と段位の一覧取得（不正パラメータ）
     *
     * @return void
     */
    public function testSearchRanksInvalidParameter()
    {
        $this->get('/api/players/ranks/aa');
        $this->assertResponseCode(404);
    }

    /**
     * 棋士と段位の一覧取得（データなし）
     *
     * @return void
     */
    public function testSearchRanksNoData()
    {
        $this->get('/api/players/ranks/0');
        $this->assertResponseSuccess();
        $this->assertEquals($this->getEmptyResponse(), $this->_response->body());
    }

    /**
     * 棋士と段位の一覧取得（データあり）
     *
     * @return void
     */
    public function testSearchRanksSuccess()
    {
        $this->get('/api/players/ranks/1');
        $this->assertResponseSuccess();
        $this->assertNotEquals($this->getEmptyResponse(), $this->_response->body());
    }

    /**
     * ランキング取得（不正パラメータ）
     *
     * @return void
     */
    public function testSearchRankingInvalidCountry()
    {
        // 所属国
        $this->get('/api/players/ranking/testtest/2017/20');
        $this->assertResponseCode(404);
        $this->assertEquals($this->getNotFoundResponse(), $this->_response->body());
    }

    /**
     * ランキング取得（不正パラメータ）
     *
     * @return void
     */
    public function testSearchRankingInvalidYear()
    {
        // 年
        $this->get('/api/players/ranking/jp/test/20');
        $this->assertResponseCode(404);
    }

    /**
     * ランキング取得（不正パラメータ）
     *
     * @return void
     */
    public function testSearchRankingInvalidRank()
    {
        // 順位
        $this->get('/api/players/ranking/jp/2017/te');
        $this->assertResponseCode(404);
    }

    /**
     * ランキング取得（データなし）
     *
     * @return void
     */
    public function testSearchRankingNoData()
    {
        // 順位
        $this->get('/api/players/ranking/jp/2000/20');
        $this->assertResponseCode(200);
        $this->assertNotEquals($this->getCompareJsonResponse([
            'countryCode' => 'jp',
            'countryName' => 'Japan',
            'year' => 2000,
            'lastUpdate' => '',
            'count' => 0,
            'ranking' => [],
        ]), $this->_response->body());
    }

    /**
     * ランキング取得（データあり）
     *
     * @return void
     */
    public function testSearchRankingSuccess()
    {
        // 順位
        $this->get('/api/players/ranking/jp/2017/20');
        $this->assertResponseCode(200);
        $this->assertNotEquals($this->getCompareJsonResponse([
            'countryCode' => 'jp',
            'countryName' => 'Japan',
            'year' => 2017,
            'lastUpdate' => '',
            'count' => 0,
            'ranking' => [],
        ]), $this->_response->body());
    }
}
