<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Controller;

use Cake\I18n\FrozenDate;
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
    public array $fixtures = [
        'app.Countries',
        'app.Ranks',
        'app.Organizations',
        'app.Players',
        'app.PlayerScores',
        'app.PlayerRanks',
        'app.Titles',
        'app.TitleScores',
        'app.TitleScoreDetails',
        'app.RetentionHistories',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Players = TableRegistry::getTableLocator()->get('Players');
        $this->createSession();
    }

    /**
     * テンプレートが存在しない
     *
     * @return void
     */
    public function testMissingTemplate()
    {
        $this->get('/players/missing');
        $this->assertContainsError();
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
        $data = [
            'sex' => '男性',
            'country_id' => '1',
            'joined_from' => '1987',
        ];
        $this->get(['_name' => 'find_players', '?' => $data]);

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
        $data = [
            'name_english' => 'あああああ',
            'country_id' => '1',
            'joined_from' => 'testtest',
            'joined_to' => 'testtest',
        ];
        $this->get(['_name' => 'find_players', '?' => $data]);

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
        $this->get(['_name' => 'new_player']);
        $this->assertResponseOk();
        $this->assertTemplate('view');

        // 詳細画面はナビゲーション非表示
        $this->assertResponseNotContains('<nav class="nav">');
    }

    /**
     * 新規作成（パラメータ有り）
     *
     * @return void
     */
    public function testNewHasParameter()
    {
        $this->get(['_name' => 'new_player', '?' => ['country_id' => '1']]);
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
        $this->get(['_name' => 'view_player', 99999]);
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
        $this->get(['_name' => 'view_player', 1]);
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
        $now = FrozenDate::now();
        $name = '棋士新規作成' . $now->format('YmdHis');
        $data = [
            'name' => $name,
            'name_english' => '',
            'rank_id' => 1,
            'country_id' => 1,
            'organization_id' => 1,
            'sex' => '男性',
            'joined_year' => $now->year,
            'joined_month' => $now->month,
            'joined_day' => $now->day,
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
        $now = FrozenDate::now();
        $name = '棋士新規作成' . $now->format('YmdHis');
        $data = [
            'name' => $name,
            'name_english' => 'test',
            'rank_id' => 1,
            'country_id' => 1,
            'organization_id' => 1,
            'sex' => '男性',
            'joined_year' => $now->year,
            'joined_month' => $now->month,
            'joined_day' => $now->day,
            'birthday' => $now->format('Y/m/d'),
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
        $now = FrozenDate::now();
        $name = '棋士新規作成' . $now->format('YmdHis');
        $data = [
            'name' => $name,
            'name_english' => 'test',
            'rank_id' => 1,
            'country_id' => 1,
            'organization_id' => 1,
            'sex' => '男性',
            'joined_year' => $now->year,
            'joined_month' => $now->month,
            'joined_day' => $now->day,
            'birthday' => date('Y/m/d'),
            'is_continue' => true,
        ];
        $this->post(['_name' => 'create_player'], $data);
        $this->assertRedirect([
            '_name' => 'new_player',
            '?' => [
                'country_id' => 1,
                'sex' => '男性',
                'joined_year' => $now->year,
                'joined_month' => $now->month,
                'joined_day' => $now->day,
            ],
        ]);
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
        $now = FrozenDate::now();
        $name = '棋士更新' . $now->format('YmdHis');
        $data = [
            'id' => 1,
            'name' => $name,
            'name_english' => '',
            'rank_id' => 1,
            'country_id' => 1,
            'organization_id' => 1,
            'sex' => '男性',
            'joined_year' => $now->year,
            'joined_month' => $now->month,
            'joined_day' => $now->day,
            'birthday' => '',
        ];
        $this->put(['_name' => 'update_player', 1], $data);
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
        $now = FrozenDate::now();
        $name = '棋士更新' . $now->format('YmdHis');
        $data = [
            'id' => 1,
            'name' => $name,
            'name_english' => 'test',
            'rank_id' => 1,
            'country_id' => 1,
            'organization_id' => 1,
            'sex' => '男性',
            'joined_year' => $now->year,
            'joined_month' => $now->month,
            'joined_day' => $now->day,
            'birthday' => date('Y/m/d'),
        ];
        $this->put(['_name' => 'update_player', 1], $data);
        $this->assertRedirect(['_name' => 'view_player', 1]);
        $this->assertResponseNotContains('<nav class="nav">');

        // データが存在すること
        $this->assertEquals(1, $this->Players->findByName($name)->count());
    }
}
