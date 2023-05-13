<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Controller;

use Cake\I18n\FrozenTime;
use Laminas\Diactoros\UploadedFile;
use Psr\Http\Message\UploadedFileInterface;

/**
 * タイトル成績コントローラのテスト
 */
class TitleScoresControllerTest extends AppTestCase
{
    /**
     * タイトル成績
     *
     * @var \Gotea\Model\Table\TitleScoresTable
     */
    public $TitleScores;

    /**
     * タイトル成績詳細
     *
     * @var \Gotea\Model\Table\TitleScoreDetailsTable
     */
    public $TitleScoreDetails;

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
        $this->TitleScores = $this->getTableLocator()->get('TitleScores');
        $this->TitleScoreDetails = $this->getTableLocator()->get('TitleScoreDetails');
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
     * アップロード失敗（メディアタイプ不正）
     *
     * @return void
     */
    public function testUploadGet()
    {
        $this->enableCsrfToken();
        $this->get(['_name' => 'upload_scores']);
        $this->assertResponseSuccess();
        $this->assertResponseNotContains('<nav class="nav">');
    }

    /**
     * アップロード失敗（メディアタイプ不正）
     *
     * @return void
     */
    public function testUploadFailureForInvalidMediaType()
    {
        $uploadedFile = $this->createFile('title_scores_success.csv', 'text/plain');

        $this->enableCsrfToken();
        $this->post(['_name' => 'execute_upload_scores'], ['file' => $uploadedFile]);
        $this->assertResponseCode(400);
        $this->assertResponseNotContains('<nav class="nav">');
    }

    /**
     * アップロード失敗（データ無し）
     *
     * @return void
     */
    public function testUploadFailureForNoData()
    {
        $uploadedFile = $this->createFile('title_scores_no_data.csv', 'text/csv');

        $this->enableCsrfToken();
        $this->post(['_name' => 'execute_upload_scores'], ['file' => $uploadedFile]);
        $this->assertResponseCode(400);
        $this->assertResponseNotContains('<nav class="nav">');
    }

    /**
     * アップロード失敗（カラム不足）
     *
     * @return void
     */
    public function testUploadFailureForInsufficient()
    {
        $uploadedFile = $this->createFile('title_scores_insufficient.csv', 'text/csv');

        $this->enableCsrfToken();
        $this->post(['_name' => 'execute_upload_scores'], ['file' => $uploadedFile]);
        $this->assertResponseCode(400);
        $this->assertResponseNotContains('<nav class="nav">');
    }

    /**
     * アップロード成功
     *
     * @return void
     */
    public function testUploadSuccess()
    {
        $uploadedFile = $this->createFile('title_scores_success.csv', 'text/csv');

        $this->enableCsrfToken();
        $this->post(['_name' => 'execute_upload_scores'], ['file' => $uploadedFile]);
        $this->assertResponseSuccess();
        $this->assertResponseNotContains('<nav class="nav">');

        // データが存在すること
        $titleScores = $this->TitleScores->findByStarted('2023-05-01')->all();
        $titleScoreIds = $titleScores->extract('id')->toList();
        $this->assertEquals(3, $titleScores->count());
        $this->assertEquals(6, $this->TitleScoreDetails->find()->whereInList('title_score_id', $titleScoreIds)->count());
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

    private function createFile(string $filename, string $mediaType): UploadedFileInterface
    {
        $testFile = TESTS . 'Fixture' . DS . 'files' . DS . $filename;
        $uploadedFile = new UploadedFile(
            $testFile,
            10,
            UPLOAD_ERR_OK,
            $testFile,
            $mediaType
        );
        $this->configRequest([
            'files' => [
                'file' => [
                    'error' => $uploadedFile->getError(),
                    'name' => $uploadedFile->getClientFilename(),
                    'size' => $uploadedFile->getSize(),
                    'tmp_name' => $testFile,
                    'type' => $uploadedFile->getClientMediaType(),
                ],
            ],
        ]);

        return $uploadedFile;
    }
}
