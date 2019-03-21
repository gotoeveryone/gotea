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
        $this->get('/api/titles');
        $this->assertResponseSuccess();
        $this->assertNotEquals($this->getEmptyResponse(), $this->_response->getBody());
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
