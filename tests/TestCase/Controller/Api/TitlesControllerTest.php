<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Controller\Api;

use Cake\ORM\TableRegistry;

/**
 * Gotea\Controller\Api\TitlesController Test Case
 *
 * @property \Gotea\Model\Table\TitlesTable $Titles
 */
class TitlesControllerTest extends ApiTestCase
{
    /**
     * タイトルモデル
     *
     * @var \Gotea\Model\Table\TitlesTable
     */
    public $Titles;

    /**
     * Fixtures
     *
     * @var array
     */
    public array $fixtures = [
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
        $this->Titles = TableRegistry::getTableLocator()->get('Titles');
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
        $this->assertJsonContentType();
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
        $this->assertJsonContentType();
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
        $this->assertJsonContentType();
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
        $this->post('/api/titles', [
            'country_id' => 1,
            'name' => 'test',
            'holding' => 1,
        ]);
        $this->assertResponseCode(400);
        $this->assertJsonContentType();
    }

    /**
     * データ登録（成功）
     *
     * @return void
     */
    public function testCreate()
    {
        $this->post('/api/titles', [
            'country_id' => 1,
            'name' => 'test',
            'name_english' => 'test',
            'holding' => 1,
            'sort_order' => 1,
            'htmlFileName' => 'test',
            'htmlFileModified' => '2018-01-01',
        ]);
        $this->assertResponseSuccess();
        $this->assertJsonContentType();
    }

    /**
     * データ登録（管理者以外）
     *
     * @return void
     */
    public function testCreateForbiddenForNonAdmin()
    {
        $count = $this->Titles->find()->count();
        $this->createSession(false);

        $this->post('/api/titles', [
            'country_id' => 1,
            'name' => 'test',
            'name_english' => 'test',
            'holding' => 1,
            'sort_order' => 1,
            'htmlFileName' => 'test',
            'htmlFileModified' => '2018-01-01',
        ]);
        $this->assertResponseCode(302);
        $this->assertSame($count, $this->Titles->find()->count());
    }

    /**
     * データ更新（不正パラメータ）
     *
     * @return void
     */
    public function testUpdateInvalidParameter()
    {
        $this->put('/api/titles/1', [
            'country_id' => 1,
            'name' => 'test',
            'nameEnglish' => '',
            'holding' => 1,
            'sort_order' => 1,
            'htmlFileName' => '',
            'htmlFileModified' => '',
        ]);
        $this->assertResponseCode(400);
        $this->assertJsonContentType();
    }

    /**
     * データ更新（成功）
     *
     * @return void
     */
    public function testUpdate()
    {
        $this->put('/api/titles/1', [
            'country_id' => 1,
            'name' => 'test update',
            'name_english' => 'test update',
            'holding' => 2,
            'sort_order' => 2,
            'htmlFileName' => 'test-update',
            'htmlFileModified' => '2018-01-01',
        ]);
        $this->assertResponseSuccess();
        $this->assertJsonContentType();

        $title = $this->Titles->get(1);
        $this->assertEquals('test update', $title->name);
        $this->assertEquals(2, $title->holding);
        $this->assertEquals('test-update', $title->html_file_name);
    }

    /**
     * データ更新（管理者以外）
     *
     * @return void
     */
    public function testUpdateForbiddenForNonAdmin()
    {
        $this->createSession(false);

        $this->put('/api/titles/1', [
            'country_id' => 1,
            'name' => 'test update',
            'name_english' => 'test update',
            'holding' => 2,
            'sort_order' => 2,
            'htmlFileName' => 'test-update',
            'htmlFileModified' => '2018-01-01',
        ]);
        $this->assertResponseCode(302);

        $title = $this->Titles->get(1);
        $this->assertNotEquals('test update', $title->name);
        $this->assertNotEquals('test-update', $title->html_file_name);
    }
}
