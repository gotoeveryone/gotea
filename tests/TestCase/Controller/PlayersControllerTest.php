<?php

namespace Gotea\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;

/**
 * 棋士コントローラのテスト
 */
class PlayersControllerTest extends IntegrationTestCase
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
        'app.player_ranks',
        'app.titles',
        'app.title_scores',
        'app.title_score_details',
        'app.retention_histories',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->__createSession();
    }

    /**
     * 画面が見えるか
     *
     * @return void
     */
    public function testDisplay()
    {
        $this->get('/players/');
        $this->assertResponseOk();
        $this->assertTemplate('index');
        $this->assertResponseContains('<nav class="nav">');
    }

    /**
     * テンプレートが存在しない
     *
     * @return void
     */
    public function testMissingTemplate()
    {
        $this->get('/players/missing');

        $this->assertResponseError();
        $this->assertResponseContains('Error');
    }

    /**
     * 初期表示
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/players');

        $this->assertResponseOk();
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
            'sex' => '男性',
            'country_id' => '1',
            'joined_from' => '1987',
        ];
        $this->post('/players', $data);

        $this->assertResponseOk();
        $this->assertResponseContains('<nav class="nav">');
    }

    /**
     * 新規作成（パラメータ無し）
     *
     * @return void
     */
    public function testNewNotHasParameter()
    {
        $this->get('/players/new');
        $this->assertResponseError();
        $this->assertResponseCode(400);
    }

    /**
     * 新規作成（パラメータ有り）
     *
     * @return void
     */
    public function testNewHasParameter()
    {
        $this->get('/players/new?country_id=1');
        $this->assertResponseOk();

        // 詳細画面はナビゲーション非表示
        $this->assertResponseNotContains('<nav class="nav">');
    }

    /**
     * 詳細（データ無し）
     *
     * @return void
     */
    public function testViewNotFound()
    {
        $this->get('/players/99999');
        $this->assertResponseError();
        $this->assertResponseCode(404);
    }

    /**
     * 詳細（データ有り）
     *
     * @return void
     */
    public function testView()
    {
        $this->get('/players/1');
        $this->assertResponseOk();

        // 詳細画面はナビゲーション非表示
        $this->assertResponseNotContains('<nav class="nav">');
    }

    /**
     * セッションデータ生成
     *
     * @return void
     */
    private function __createSession()
    {
        $this->session([
            'Auth' => [
                'User' => [
                    'id' => 1,
                    'account' => env('TEST_USER'),
                    'name' => 'テスト',
                    'role' => '管理者',
                ],
            ],
        ]);
    }
}
