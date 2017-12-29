<?php

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
        'app.titles',
        'app.retention_histories',
        'app.players',
        'app.countries',
        'app.ranks',
        'app.organizations',
        'app.player_ranks',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->RetentionHistories = TableRegistry::get('RetentionHistories');
        $this->_createSession();
    }

    /**
     * テンプレートが存在しない
     *
     * @return void
     */
    public function testMissingTemplate()
    {
        $this->get('/histories/missing');

        $this->assertResponseError();
        $this->assertResponseContains('Error');
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
            'rank_id' => 1,
            'title_id' => 1,
            'name' => 'Test Title',
            'is_team' => false,
            'holding' => 100,
            'target_year' => 2017,
        ];
        $this->post(['_name' => 'save_histories', 1], $data);
        $this->assertRedirect(['_name' => 'view_title', 'tab' => 'histories', 1]);
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
            'rank_id' => 1,
            'title_id' => 1,
            'name' => 'Test Title',
            'is_team' => false,
            'holding' => 100,
            'target_year' => 2018,
        ];
        $this->post(['_name' => 'save_histories', 1], $data);
        $this->assertRedirect(['_name' => 'view_title', 'tab' => 'histories', 1]);
        $this->assertResponseNotContains('<nav class="nav">');

        // データが存在すること
        $this->assertEquals(1, $this->RetentionHistories->find()->where($data)->count());
    }
}
