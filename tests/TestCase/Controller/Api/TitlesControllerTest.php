<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Controller\Api;

/**
 * Gotea\Controller\Api\TitlesController Test Case
 */
class TitlesControllerTest extends ApiTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Countries',
        'app.Titles',
        'app.Players',
        'app.Ranks',
        'app.PlayerRanks',
        'app.RetentionHistories',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->createSession();
    }

    /**
     * タイトル一覧取得
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/api/titles?country_id=1');
        $this->assertResponseSuccess();
        $this->assertNotEquals($this->getEmptyResponse(), $this->_getBodyAsString());
        collection(json_decode($this->_getBodyAsString())->response)->each(function ($item) {
            $this->assertEquals($item->countryId, 1);
            $this->assertFalse($item->isClosed);
        });
    }

    /**
     * タイトル一覧取得（非出力棋戦あり）
     *
     * @return void
     */
    public function testIndexWithNonOutput()
    {
        $this->get('/api/titles?search_non_output=1');
        $this->assertResponseSuccess();
        $this->assertNotEquals($this->getEmptyResponse(), $this->_getBodyAsString());
        collection(json_decode($this->_getBodyAsString())->response)->each(function ($item) {
            $this->assertTrue(in_array($item->isOutput, [true, false], true));
        });
    }

    /**
     * タイトル一覧取得（終了棋戦あり）
     *
     * @return void
     */
    public function testIndexWithClosed()
    {
        $this->get('/api/titles?search_closed=1');
        $this->assertResponseSuccess();
        $this->assertNotEquals($this->getEmptyResponse(), $this->_getBodyAsString());
        collection(json_decode($this->_getBodyAsString())->response)->each(function ($item) {
            $this->assertTrue(in_array($item->isClosed, [true, false], true));
        });
    }

    /**
     * データ登録（不正パラメータ）
     *
     * @return void
     */
    public function testCreateInvalidParameter()
    {
        $this->enableApiUser();
        $this->post('/api/titles', [
            'country_id' => 1,
            'name' => 'test',
            'holding' => 1,
        ]);
        $this->assertResponseCode(400);
    }

    /**
     * データ登録（成功）
     *
     * @return void
     */
    public function testCreate()
    {
        $this->enableApiUser();
        $this->post('/api/titles', [
            'country_id' => 1,
            'name' => 'test',
            'name_english' => 'test',
            'holding' => 1,
            'sort_order' => 1,
            'htmlFileName' => 'test',
            'htmlFileModified' => '2018/01/01',
        ]);
        $this->assertResponseSuccess();
    }
}
