<?php
namespace Gotea\Test\TestCase\Controller\Api;

use Cake\I18n\FrozenDate;
use Cake\Routing\Exception\MissingRouteException;
use Cake\Utility\Hash;
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
        'app.player_ranks',
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
        $this->assertResponseEquals($this->getEmptyResponse());
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
        $this->assertResponseNotEquals($this->getEmptyResponse());
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
        $this->assertResponseEquals($this->getNotFoundResponse());
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
        $this->get('/api/players/ranking/jp/2000/20');
        $this->assertResponseCode(200);
        $this->assertResponseEquals($this->getCompareJsonResponse([
            'response' => [
                'countryCode' => 'jp',
                'countryName' => 'Japan',
                'year' => 2000,
                'lastUpdate' => '',
                'count' => 0,
                'ranking' => [],
            ],
        ]));
    }

    /**
     * ランキング取得（データあり）
     *
     * @return void
     */
    public function testSearchRankingSuccess()
    {
        $this->get('/api/players/ranking/jp/2017/20');
        $this->assertResponseCode(200);
        $this->assertResponseNotEquals($this->getCompareJsonResponse([
            'response' => [
                'countryCode' => 'jp',
                'countryName' => 'Japan',
                'year' => 2017,
                'lastUpdate' => '',
                'count' => 0,
                'ranking' => [],
            ],
        ]));
    }

    /**
     * ランキング取得（データあり・FROM指定）
     *
     * @return void
     */
    public function testSearchRankingSuccessWithFrom()
    {
        $targetFrom = FrozenDate::parseDate('2017-12-01', 'yyyy-MM-dd');

        $fromValue = $targetFrom->i18nFormat('yyyy-MM-dd');
        $this->get("/api/players/ranking/jp/2017/20?from=${fromValue}");
        $this->assertResponseCode(200);
        $lastUpdate = FrozenDate::parseDate(
            Hash::get($this->getResponseArray(), 'response.lastUpdate'),
            'yyyy-MM-dd'
        );
        $this->assertGreaterThanOrEqual($lastUpdate->diffInDays($targetFrom, false), 0);
    }

    /**
     * ランキング取得（データあり・TO指定）
     *
     * @return void
     */
    public function testSearchRankingSuccessWithTo()
    {
        $targetTo = FrozenDate::parseDate('2017-12-31', 'yyyy-MM-dd');

        $toValue = $targetTo->i18nFormat('yyyy-MM-dd');
        $this->get("/api/players/ranking/jp/2017/20?to=${toValue}");
        $this->assertResponseCode(200);
        $lastUpdate = FrozenDate::parseDate(
            Hash::get($this->getResponseArray(), 'response.lastUpdate'),
            'yyyy-MM-dd'
        );
        $this->assertLessThanOrEqual($lastUpdate->diffInDays($targetTo, false), 0);
    }

    /**
     * ランキング取得（データあり・FROM～TO指定）
     *
     * @return void
     */
    public function testSearchRankingSuccessWithFromTo()
    {
        $targetFrom = FrozenDate::parseDate('2017-12-01', 'yyyy-MM-dd');
        $targetTo = FrozenDate::parseDate('2017-12-31', 'yyyy-MM-dd');

        $fromValue = $targetFrom->i18nFormat('yyyy-MM-dd');
        $toValue = $targetTo->i18nFormat('yyyy-MM-dd');
        $this->get("/api/players/ranking/jp/2017/20?from=${fromValue}&to=${toValue}");
        $this->assertResponseCode(200);
        $lastUpdate = FrozenDate::parseDate(
            Hash::get($this->getResponseArray(), 'response.lastUpdate'),
            'yyyy-MM-dd'
        );
        $this->assertGreaterThanOrEqual($lastUpdate->diffInDays($targetFrom, false), 0);
        $this->assertLessThanOrEqual($lastUpdate->diffInDays($targetTo, false), 0);
    }

    /**
     * ランキング作成（不正パラメータ）
     *
     * @return void
     */
    public function testCreateRankingInvalidCountry()
    {
        // 所属国
        $this->enableCsrfToken();
        $this->post('/api/players/ranking/testtest/2017/20');
        $this->assertResponseCode(404);
        $this->assertResponseEquals($this->getNotFoundResponse());
    }

    /**
     * ランキング作成（不正パラメータ）
     *
     * @return void
     */
    public function testCreateRankingInvalidYear()
    {
        // 年
        $this->enableCsrfToken();
        $this->post('/api/players/ranking/jp/test/20');
        $this->assertResponseCode(404);
    }

    /**
     * ランキング作成（不正パラメータ）
     *
     * @return void
     */
    public function testCreateRankingInvalidRank()
    {
        // 順位
        $this->enableCsrfToken();
        $this->post('/api/players/ranking/jp/2017/te');
        $this->assertResponseCode(404);
    }

    /**
     * ランキング作成（データなし）
     *
     * @return void
     */
    public function testCreateRankingNoData()
    {
        $this->enableCsrfToken();
        $this->post('/api/players/ranking/jp/2000/20');
        $this->assertResponseCode(200);
        $this->assertResponseEquals($this->getCompareJsonResponse([
            'response' => [
                'countryCode' => 'jp',
                'countryName' => 'Japan',
                'year' => 2000,
                'lastUpdate' => '',
                'count' => 0,
                'ranking' => [],
            ],
        ]));
    }

    /**
     * ランキング作成（データあり）
     *
     * @return void
     */
    public function testCreateRankingSuccess()
    {
        $this->enableCsrfToken();
        $this->post('/api/players/ranking/jp/2017/20');
        $this->assertResponseCode(200);
        $this->assertResponseNotEquals($this->getCompareJsonResponse([
            'response' => [
                'countryCode' => 'jp',
                'countryName' => 'Japan',
                'year' => 2017,
                'lastUpdate' => '',
                'count' => 0,
                'ranking' => [],
            ],
        ]));
    }

    /**
     * ランキング作成（データあり・FROM指定）
     *
     * @return void
     */
    public function testCreateRankingSuccessWithFrom()
    {
        $targetFrom = FrozenDate::parseDate('2017-12-01', 'yyyy-MM-dd');
        $this->enableCsrfToken();
        $this->post('/api/players/ranking/jp/2017/20', [
            'from' => $targetFrom->i18nFormat('yyyy-MM-dd'),
        ]);
        $this->assertResponseCode(200);
        $lastUpdate = FrozenDate::parseDate(
            Hash::get($this->getResponseArray(), 'response.lastUpdate'),
            'yyyy-MM-dd'
        );
        $this->assertGreaterThanOrEqual($lastUpdate->diffInDays($targetFrom, false), 0);
    }

    /**
     * ランキング作成（データあり・TO指定）
     *
     * @return void
     */
    public function testCreateRankingSuccessWithTo()
    {
        $targetTo = FrozenDate::parseDate('2017-12-31', 'yyyy-MM-dd');

        $this->enableCsrfToken();
        $this->post('/api/players/ranking/jp/2017/20', [
            'to' => $targetTo->i18nFormat('yyyy-MM-dd'),
        ]);
        $this->assertResponseCode(200);
        $lastUpdate = FrozenDate::parseDate(
            Hash::get($this->getResponseArray(), 'response.lastUpdate'),
            'yyyy-MM-dd'
        );
        $this->assertLessThanOrEqual($lastUpdate->diffInDays($targetTo, false), 0);
    }

    /**
     * ランキング作成（データあり・FROM～TO指定）
     *
     * @return void
     */
    public function testCreateRankingSuccessWithFromTo()
    {
        $targetFrom = FrozenDate::parseDate('2017-12-01', 'yyyy-MM-dd');
        $targetTo = FrozenDate::parseDate('2017-12-31', 'yyyy-MM-dd');

        $this->enableCsrfToken();
        $this->post('/api/players/ranking/jp/2017/20', [
            'from' => $targetFrom->i18nFormat('yyyy-MM-dd'),
            'to' => $targetTo->i18nFormat('yyyy-MM-dd'),
        ]);
        $this->assertResponseCode(200);
        $lastUpdate = FrozenDate::parseDate(
            Hash::get($this->getResponseArray(), 'response.lastUpdate'),
            'yyyy-MM-dd'
        );
        $this->assertGreaterThanOrEqual($lastUpdate->diffInDays($targetFrom, false), 0);
        $this->assertLessThanOrEqual($lastUpdate->diffInDays($targetTo, false), 0);
    }
}
