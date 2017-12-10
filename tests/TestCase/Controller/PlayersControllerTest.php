<?php

namespace Gotea\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;

/**
 * 棋士コントローラのテスト
 *
 * @property \Gotea\Model\Table\PlayersTable $Players
 */
class PlayersControllerTest extends AppTestCase
{
    /**
     * 棋士モデル
     *
     * @var \Gotea\Model\Table\PlayersTable
     */
    public $Players;

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
        $this->Players = TableRegistry::get('Players');
        $this->_createSession();
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
     * 画面が見えるか
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get(['_name' => 'players']);
        $this->assertResponseOk();
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
            'sex' => '男性',
            'country_id' => '1',
            'joined_from' => '1987',
        ];
        $this->post('/players', $data);

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
            'name_english' => 'あああああ',
            'country_id' => '1',
            'joined_from' => 'testtest',
            'joined_to' => 'testtest',
        ];
        $this->post('/players', $data);

        $this->assertResponseCode(400);
        $this->assertTemplate('index');
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
        $this->assertTemplate('view');

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
        $this->assertTemplate('view');

        // 詳細画面はナビゲーション非表示
        $this->assertResponseNotContains('<nav class="nav">');
    }

    /**
     * 新規作成（失敗）
     *
     * @return void
     */
    public function testCreateFailed()
    {
        $this->enableCsrfToken();
        $name = '棋士新規作成' . date('YmdHis');
        $data = [
            'name' => $name,
            'name_english' => '',
            'rank_id' => 1,
            'country_id' => 1,
            'organization_id' => 1,
            'sex' => '男性',
            'joined' => [
                'year' => date('Y'),
                'month' => date('m'),
                'day' => date('d'),
            ],
            'birthday' => '',
        ];
        $this->post(['_name' => 'create_player'], $data);
        $this->assertResponseCode(400);
        $this->assertResponseNotContains('<nav class="nav">');

        // データが存在すること
        $this->assertEquals(0, $this->Players->findByName($name)->count());
    }

    /**
     * 新規作成
     *
     * @return void
     */
    public function testCreate()
    {
        $this->enableCsrfToken();
        $name = '棋士新規作成' . date('YmdHis');
        $data = [
            'name' => $name,
            'name_english' => 'test',
            'rank_id' => 1,
            'country_id' => 1,
            'organization_id' => 1,
            'sex' => '男性',
            'joined' => [
                'year' => date('Y'),
                'month' => date('m'),
                'day' => date('d'),
            ],
            'birthday' => date('Y/m/d'),
        ];
        $this->post(['_name' => 'create_player'], $data);
        $this->assertResponseSuccess();
        $this->assertResponseNotContains('<nav class="nav">');

        // データが存在すること
        $this->assertEquals(1, $this->Players->findByName($name)->count());
    }

    /**
     * 新規作成（引き続き作成）
     *
     * @return void
     */
    public function testCreateWithContinue()
    {
        $this->enableCsrfToken();
        $name = '棋士新規作成' . date('YmdHis');
        $data = [
            'name' => $name,
            'name_english' => 'test',
            'rank_id' => 1,
            'country_id' => 1,
            'organization_id' => 1,
            'sex' => '男性',
            'joined' => [
                'year' => date('Y'),
                'month' => date('m'),
                'day' => date('d'),
            ],
            'birthday' => date('Y/m/d'),
            'is_continue' => true,
        ];
        $this->post(['_name' => 'create_player'], $data);
        $this->assertRedirect(['_name' => 'new_player', 'country_id' => 1]);
        $this->assertResponseNotContains('<nav class="nav">');

        // データが存在すること
        $this->assertEquals(1, $this->Players->findByName($name)->count());
    }

    /**
     * 更新
     *
     * @return void
     */
    public function testUpdateFailed()
    {
        $this->enableCsrfToken();
        $before = $this->Players->get(1);
        $name = '棋士更新' . date('YmdHis');
        $data = [
            'id' => 1,
            'name' => $name,
            'name_english' => '',
            'rank_id' => 1,
            'country_id' => 1,
            'organization_id' => 1,
            'joined' => [
                'year' => date('Y'),
                'month' => date('m'),
                'day' => date('d'),
            ],
            'birthday' => '',
        ];
        $this->put('/players/save/1', $data);
        $this->assertResponseCode(400);
        $this->assertResponseNotContains('<nav class="nav">');

        // データが存在すること
        $this->assertEquals(0, $this->Players->findByName($name)->count());
    }

    /**
     * 更新
     *
     * @return void
     */
    public function testUpdate()
    {
        $this->enableCsrfToken();
        $before = $this->Players->get(1);
        $name = '棋士更新' . date('YmdHis');
        $data = [
            'id' => 1,
            'name' => $name,
            'name_english' => 'test',
            'rank_id' => 1,
            'country_id' => 1,
            'organization_id' => 1,
            'joined' => [
                'year' => date('Y'),
                'month' => date('m'),
                'day' => date('d'),
            ],
            'birthday' => date('Y/m/d'),
        ];
        $this->put('/players/save/1', $data);
        $this->assertRedirect(['_name' => 'view_player', 1]);
        $this->assertResponseNotContains('<nav class="nav">');

        // データが存在すること
        $this->assertEquals(1, $this->Players->findByName($name)->count());
    }
}
