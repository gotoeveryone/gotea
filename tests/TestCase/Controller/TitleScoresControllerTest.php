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
        'app.player_ranks',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
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
        $this->post(['_name' => 'scores'], $data);

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
        $this->post(['_name' => 'scores'], $data);

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
     * 更新
     *
     * @return void
     */
    public function testUpdate()
    {
        $this->enableCsrfToken();
        $this->put(['_name' => 'update_scores', 1]);

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
        $this->delete(['_name' => 'delete_scores', 1]);

        $this->assertResponseOk();
        $this->assertTemplate('index');
        $this->assertResponseContains('<nav class="nav">');
    }
}
