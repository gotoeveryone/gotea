<?php

namespace Gotea\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;

/**
 * タイトルコントローラのテスト
 *
 * @property \Gotea\Model\Table\TitlesTable $Titles
 */
class TitlesControllerTest extends AppTestCase
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
    public $fixtures = [
        'app.Titles',
        'app.RetentionHistories',
        'app.Players',
        'app.Countries',
        'app.Ranks',
        'app.Organizations',
        'app.PlayerRanks',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->Titles = TableRegistry::getTableLocator()->get('Titles');
        $this->createSession();
    }

    /**
     * テンプレートが存在しない
     *
     * @return void
     */
    public function testMissingTemplate()
    {
        $this->get('/titles/missing');
        $this->assertContainsError();
    }

    /**
     * 画面が見えるか
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/titles/');
        $this->assertResponseOk();
        $this->assertTemplate('index');
        $this->assertResponseContains('<nav class="nav">');
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
        $this->assertTemplate('view');

        // 詳細画面はナビゲーション非表示
        $this->assertResponseNotContains('<nav class="nav">');
    }

    /**
     * 更新
     *
     * @return void
     */
    public function testSaveFailed()
    {
        $this->enableCsrfToken();
        $name = 'タイトル更新' . date('YmdHis');
        $data = [
            'id' => 1,
            'country_id' => 1,
            'name' => $name,
            'name_english' => '',
            'holding' => 1,
            'sort_order' => 1,
            'html_file_name' => '',
            'html_file_modified' => '',
        ];
        $this->put(['_name' => 'update_title', 1], $data);
        $this->assertResponseCode(400);
        $this->assertResponseNotContains('<nav class="nav">');

        // データが存在すること
        $this->assertEquals(0, $this->Titles->findByName($name)->count());
    }

    /**
     * 更新
     *
     * @return void
     */
    public function testSave()
    {
        $this->enableCsrfToken();
        $name = 'タイトル更新' . date('YmdHis');
        $data = [
            'id' => 1,
            'country_id' => 1,
            'name' => $name,
            'name_english' => 'Test',
            'holding' => 1,
            'sort_order' => 1,
            'html_file_name' => 'test',
            'html_file_modified' => date('Y/m/d'),
        ];
        $this->put(['_name' => 'update_title', 1], $data);
        $this->assertRedirect(['_name' => 'view_title', 1]);
        $this->assertResponseNotContains('<nav class="nav">');

        // データが存在すること
        $this->assertEquals(1, $this->Titles->findByName($name)->count());
    }
}
