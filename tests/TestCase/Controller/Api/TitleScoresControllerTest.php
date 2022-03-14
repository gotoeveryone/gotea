<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Controller\Api;

use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;

/**
 * Gotea\Controller\Api\TitleScoresController Test Case
 *
 * @uses \Gotea\Controller\Api\TitleScoresController
 */
class TitleScoresControllerTest extends ApiTestCase
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
    protected $fixtures = [
        'app.TitleScores',
        'app.Players',
        'app.Titles',
        'app.Countries',
        'app.TitleScoreDetails',
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
     * 取得
     *
     * @return void
     */
    public function testView()
    {
        $this->get(['_name' => 'api_view_score', 1]);
        $this->assertResponseSuccess();
        $this->assertResponseNotEquals($this->getEmptyResponse());
    }

    /**
     * 更新失敗
     *
     * @return void
     */
    public function testEditFailed()
    {
        $now = FrozenTime::now();
        $name = 'test_edit_' . $now->format('YmdHis');
        $data = [
            'id' => 1,
            'country_id' => 1,
            'started' => 'test',
            'ended' => '2019-01-02',
            'name' => $name,
        ];
        $this->put(['_name' => 'api_edit_score', 1], $data);
        $this->assertResponseCode(400);

        // データが存在しないこと
        $this->assertEquals(0, $this->TitleScores->findByName($name)->count());
    }

    /**
     * 更新成功
     *
     * @return void
     */
    public function testEdit()
    {
        $now = FrozenTime::now();
        $name = 'test_edit_' . $now->format('YmdHis');
        $data = [
            'id' => 1,
            'country_id' => 1,
            'started' => '2019-01-02',
            'ended' => '2019-01-02',
            'name' => $name,
        ];
        $this->put(['_name' => 'api_edit_score', 1], $data);
        $this->assertResponseSuccess();
        $titleScore = $this->TitleScores->findByName($name)->first();
        $this->assertEquals($this->_getBodyAsString(), $titleScore->toArray());
    }

    /**
     * 勝敗変更
     *
     * @return void
     */
    public function testSwtichDivision()
    {
        $this->put(['_name' => 'api_edit_score_division', 1]);
        $this->assertResponseSuccess();
        $titleScore = $this->TitleScores->get(1);
        $this->assertEquals($this->_getBodyAsString(), $titleScore->toArray());
    }
}
