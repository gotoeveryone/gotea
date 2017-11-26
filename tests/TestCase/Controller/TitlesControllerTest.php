<?php
namespace Gotea\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;

/**
 * タイトルコントローラのテスト
 */
class TitlesControllerTest extends IntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.titles',
        'app.retention_histories',
        'app.players',
        'app.countries',
        'app.ranks',
        'app.organizations',
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
        $this->get('/titles/');
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
        $this->get('/titles/missing');

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
        $this->get('/titles');

        $this->assertResponseOk();
    }

    /**
     * 詳細（データ無し）
     *
     * @return void
     */
    public function testViewNotFound()
    {
        $this->get('/titles/99999');
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
        $this->get('/titles/1');
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
