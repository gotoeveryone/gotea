<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;

/**
 * 各種情報クエリ更新用コントローラのテスト
 */
class NativeQueryControllerTest extends AppTestCase
{
    use IntegrationTestTrait;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->createSession();
    }

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Countries',
        'app.Players',
        'app.Ranks',
        'app.PlayerRanks',
    ];

    /**
     * テンプレートが存在しない
     *
     * @return void
     */
    public function testMissingTemplate()
    {
        $this->get('/queries/missing');
        $this->assertContainsError();
    }

    /**
     * 画面が見えるか
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get(['_name' => 'queries']);
        $this->assertResponseOk();
        $this->assertTemplate('index');
        $this->assertResponseContains('<nav class="nav">');
    }

    /**
     * クエリ実行（不正なクエリ）
     *
     * @return void
     */
    public function testExecuteInvalidQueries()
    {
        $this->enableCsrfToken();
        $data = [
            'queries' => 'update hoge',
        ];
        $this->post(['_name' => 'execute_queries'], $data);
        $this->assertResponseCode(400);
        $this->assertTemplate('index');
        $this->assertResponseContains('<nav class="nav">');
    }

    /**
     * クエリ実行（実行結果が1件にならない）
     *
     * @return void
     */
    public function testExecuteNotOneResults()
    {
        $this->enableCsrfToken();
        $data = [
            'queries' => 'delete from players',
        ];
        $this->post(['_name' => 'execute_queries'], $data);
        $this->assertResponseCode(400);
        $this->assertTemplate('index');
        $this->assertResponseContains('<nav class="nav">');
    }

    /**
     * クエリ実行
     *
     * @return void
     */
    public function testExecute()
    {
        $this->enableCsrfToken();
        $data = [
            'queries' => 'update players set modified = now() where id = 1',
        ];
        $this->post(['_name' => 'execute_queries'], $data);
        $this->assertRedirect(['_name' => 'execute_queries']);
    }
}
