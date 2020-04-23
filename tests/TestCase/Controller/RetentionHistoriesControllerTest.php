<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;

/**
 * Gotea\Controller\RetentionHistoriesController Test Case
 *
 * @property \Gotea\Model\Table\RetentionHistoriesTable $RetentionHistories
 */
class RetentionHistoriesControllerTest extends AppTestCase
{
    /**
     * 棋士昇段モデル
     *
     * @var \Gotea\Model\Table\RetentionHistoriesTable
     */
    public $RetentionHistories;

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
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->RetentionHistories = TableRegistry::getTableLocator()->get('RetentionHistories');
        $this->createSession();
    }

    /**
     * テンプレートが存在しない
     *
     * @return void
     */
    public function testMissingTemplate()
    {
        $this->get('/histories/missing');
        $this->assertContainsError();
    }

    /**
     * 保存（新規作成）
     *
     * @return void
     */
    public function testSaveWithCreate()
    {
        $this->enableCsrfToken();
        $data = [
            'player_id' => 1,
            'title_id' => 1,
            'name' => 'Test Title',
            'is_team' => false,
            'acquired' => '2019/04/01',
            'holding' => 100,
            'target_year' => 2017,
        ];
        $this->post(['_name' => 'save_histories', 1], $data);
        $this->assertRedirect(['_name' => 'view_title', 'tab' => 'retention_histories', 1]);
        $this->assertResponseNotContains('<nav class="nav">');

        // データが存在すること
        $this->assertEquals(1, $this->RetentionHistories->find()->where($data)->count());
    }

    /**
     * 保存（更新）
     *
     * @return void
     */
    public function testSaveWithUpdate()
    {
        $this->enableCsrfToken();
        $data = [
            'player_id' => 1,
            'title_id' => 1,
            'name' => 'Test Title',
            'is_team' => false,
            'acquired' => '2019/04/01',
            'holding' => 100,
            'target_year' => 2018,
        ];
        $this->post(['_name' => 'save_histories', 1], $data);
        $this->assertRedirect(['_name' => 'view_title', 'tab' => 'retention_histories', 1]);
        $this->assertResponseNotContains('<nav class="nav">');

        // データが存在すること
        $this->assertEquals(1, $this->RetentionHistories->find()->where($data)->count());
    }
}
