<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Controller;

use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;

/**
 * タイトル成績コントローラのテスト
 */
class TitleScoresControllerTest extends AppTestCase
{
    /**
     * タイトルモデル
     *
     * @var \Gotea\Model\Table\TitleScoresTable
     */
    public $TitleScores;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.TitleScores',
        'app.TitleScoreDetails',
        'app.Players',
        'app.Titles',
        'app.Countries',
        'app.Ranks',
        'app.PlayerRanks',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->TitleScores = TableRegistry::getTableLocator()->get('TitleScores');
        $this->createSession();
    }

    /**
     * テンプレートが存在しない
     *
     * @return void
     */
    public function testMissingTemplate()
    {
        $this->get('/scores/missing');
        $this->assertContainsError();
    }

    /**
     * 画面が見えるか
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get(['_name' => 'scores']);
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
            'started' => 'testtest',
            'ended' => 'testtest',
        ];
        $this->get(['_name' => 'find_scores', '?' => $data]);

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
        $data = [
            'started' => '2017/01/01',
            'ended' => '2017/12/31',
        ];
        $this->get(['_name' => 'find_scores', '?' => $data]);

        $this->assertResponseOk();
        $this->assertTemplate('index');
        $this->assertResponseContains('<nav class="nav">');
    }

    /**
     * 棋士・年を絞った抽出処理
     *
     * @return void
     */
    public function testSearchByPlayer()
    {
        $id = 1;
        $year = 2017;

        $this->enableCsrfToken();
        $this->get(['_name' => 'find_player_scores', $id, $year]);

        $this->assertResponseOk();
        $this->assertTemplate('player');
        $this->assertResponseNotContains('<nav class="nav">');

        $scores = $this->viewVariable('titleScores');
        $this->assertNotNull($scores);
        foreach ($scores as $score) {
            $this->assertTrue($score->win_detail->player->id === $id
                || $score->lose_detail->player->id === $id);
            $this->assertEquals($year, $score->started->format('Y'));
            $this->assertEquals($year, $score->ended->format('Y'));
        }
    }

    /**
     * 更新失敗
     *
     * @return void
     */
    public function testUpdateFailed()
    {
        $this->enableCsrfToken();
        $now = FrozenTime::now();
        $name = 'test_update_' . $now->format('YmdHis');
        $data = [
            'id' => 1,
            'country_id' => 1,
            'started' => 'test',
            'ended' => '2019-01-02',
            'name' => $name,
        ];
        $this->put(['_name' => 'update_score', 1], $data);
        $this->assertResponseCode(400);
        $this->assertResponseNotContains('<nav class="nav">');

        // データが存在しないこと
        $this->assertEquals(0, $this->TitleScores->findByName($name)->count());
    }

    /**
     * 更新成功
     *
     * @return void
     */
    public function testUpdate()
    {
        $this->enableCsrfToken();
        $now = FrozenTime::now();
        $name = 'test_update_' . $now->format('YmdHis');
        $data = [
            'id' => 1,
            'country_id' => 1,
            'started' => '2019-01-02',
            'ended' => '2019-01-02',
            'name' => $name,
        ];
        $this->put(['_name' => 'update_score', 1], $data);
        $this->assertRedirect(['_name' => 'view_score', 1]);
        $this->assertResponseNotContains('<nav class="nav">');

        // データが存在すること
        $this->assertEquals(1, $this->TitleScores->findByName($name)->count());
    }

    /**
     * 勝敗変更
     *
     * @return void
     */
    public function testSwtichDivision()
    {
        $this->enableCsrfToken();
        $data = [
            'action' => 'switchDivision',
        ];
        $this->put(['_name' => 'update_score', 1], $data);

        $this->assertRedirect(['_name' => 'view_score', 1]);
        $this->assertResponseNotContains('<nav class="nav">');
    }

    /**
     * 削除
     *
     * @return void
     */
    public function testDelete()
    {
        $this->enableCsrfToken();
        $this->delete(['_name' => 'delete_score', 1]);

        $this->assertRedirect(['_name' => 'find_scores']);
        $this->assertResponseNotContains('<nav class="nav">');
    }
}
