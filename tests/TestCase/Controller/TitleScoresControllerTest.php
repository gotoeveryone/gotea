<?php
namespace Gotea\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;

/**
 * タイトル成績コントローラのテスト
 */
class TitleScoresControllerTest extends IntegrationTestCase
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
        $this->__createSession();
    }

    /**
     * 画面が見えるか
     *
     * @return void
     */
    public function testDisplay()
    {
        $this->get('/scores/');
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
        $this->get('/scores/missing');

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
        $this->get('/scores');

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
            'started' => '2017/01/01',
            'ended' => '2017/12/31',
        ];
        $this->post('/scores', $data);

        $this->assertResponseOk();
        $this->assertResponseContains('<nav class="nav">');
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
