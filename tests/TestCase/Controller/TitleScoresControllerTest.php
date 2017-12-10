<?php
namespace Gotea\Test\TestCase\Controller;

/**
 * タイトル成績コントローラのテスト
 */
class TitleScoresControllerTest extends AppTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.title_scores',
        'app.title_score_details',
        'app.players',
        'app.countries',
        'app.ranks',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->_createSession();
    }

    /**
     * テンプレートが存在しない
     *
     * @return void
     */
    public function testMissingTemplate()
    {
        $this->get('/scores/missing');

        $this->assertResponseError();
        $this->assertResponseContains('Error');
    }

    /**
     * 画面が見えるか
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/scores/');
        $this->assertResponseOk();
        $this->assertTemplate('index');
        $this->assertResponseContains('<nav class="nav">');
    }

    /**
     * 検索
     *
     * @return void
     */
    public function testSearchInvalid()
    {
        $this->enableCsrfToken();
        $data = [
            'started' => 'testtest',
            'ended' => 'testtest',
        ];
        $this->post('/scores', $data);

        $this->assertResponseCode(400);
        $this->assertTemplate('index');
        $this->assertResponseContains('<nav class="nav">');
    }

    /**
     * 検索
     *
     * @return void
     */
    public function testSearch()
    {
        $this->enableCsrfToken();
        $data = [
            'started' => '2017/01/01',
            'ended' => '2017/12/31',
        ];
        $this->post('/scores', $data);

        $this->assertResponseOk();
        $this->assertTemplate('index');
        $this->assertResponseContains('<nav class="nav">');
    }

    /**
     * 更新
     *
     * @return void
     */
    public function testUpdate()
    {
        $this->enableCsrfToken();
        $this->put('/scores/1');

        $this->assertResponseOk();
        $this->assertTemplate('index');
        $this->assertResponseContains('<nav class="nav">');
    }

    /**
     * 削除
     *
     * @return void
     */
    public function testDelete()
    {
        $this->enableCsrfToken();
        $this->delete('/scores/1');

        $this->assertResponseOk();
        $this->assertTemplate('index');
        $this->assertResponseContains('<nav class="nav">');
    }
}
