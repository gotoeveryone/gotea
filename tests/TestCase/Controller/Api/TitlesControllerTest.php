<?php
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
        'app.RetentionHistories',
    ];

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/api/titles?country_id=1');
        $this->assertResponseSuccess();
        $this->assertNotEquals($this->getEmptyResponse(), $this->_response->getBody());
        collection(json_decode($this->_response->getBody())->response)->each(function ($item) {
            $this->assertEquals($item->countryId, 1);
            $this->assertFalse($item->isClosed);
        });
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testIndexAll()
    {
        $this->get('/api/titles?search_all=1');
        $this->assertResponseSuccess();
        $this->assertNotEquals($this->getEmptyResponse(), $this->_response->getBody());
        collection(json_decode($this->_response->getBody())->response)->each(function ($item) {
            $this->assertTrue($item->isClosed === true || $item->isClosed === false);
        });
    }

    /**
     * データ登録（不正パラメータ）
     *
     * @return void
     */
    public function testCreateInvalidParameter()
    {
        $this->enableCsrfToken();
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
        $this->enableCsrfToken();
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
