<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Controller;

use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Gotea\Controller\TableTemplatesController Test Case
 *
 * @uses \Gotea\Controller\TableTemplatesController
 * @property \Gotea\Model\Table\TableTemplatesTable $TableTemplates
 */
class TableTemplatesControllerTest extends AppTestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Countries',
        'app.Ranks',
        'app.Organizations',
        'app.PlayerRanks',
        'app.Players',
        'app.TableTemplates',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->TableTemplates = TableRegistry::getTableLocator()->get('TableTemplates');
        $this->createSession();
    }

    /**
     * テンプレートが存在しない
     *
     * @return void
     */
    public function testMissingTemplate()
    {
        $this->get('/table-templates/missing');
        $this->assertContainsError();
    }

    /**
     * 画面が見えるか
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get(['_name' => 'table_templates']);
        $this->assertResponseOk();
        $this->assertTemplate('index');
    }

    /**
     * 新規作成
     *
     * @return void
     */
    public function testNew()
    {
        $this->get(['_name' => 'new_table_template']);
        $this->assertResponseOk();
        $this->assertTemplate('new');
        $this->assertResponseContains(__('Add'));
    }

    /**
     * 編集（データ無し）
     *
     * @return void
     */
    public function testEditNotFound()
    {
        $this->get(['_name' => 'edit_table_template', 999]);
        $this->assertResponseError();
        $this->assertResponseCode(404);
    }

    /**
     * 編集（データ有り）
     *
     * @return void
     */
    public function testEdit()
    {
        $this->get(['_name' => 'edit_table_template', 1]);
        $this->assertResponseOk();
        $this->assertTemplate('edit');
        $this->assertResponseContains(__('Edit'));
    }

    /**
     * 新規作成（失敗）
     *
     * @return void
     */
    public function testCreateFailed()
    {
        $this->enableCsrfToken();
        $now = FrozenTime::now();
        $title = 'test_create_' . $now->format('YmdHis');
        $data = [
            'title' => $title,
            'content' => '',
        ];
        $this->post(['_name' => 'create_table_template'], $data);
        $this->assertResponseCode(400);
        $this->assertResponseContains(__('Add'));

        // データが存在しない
        $this->assertFalse($this->TableTemplates->exists(compact('title')));
    }

    /**
     * 新規作成
     *
     * @return void
     */
    public function testCreate()
    {
        $this->enableCsrfToken();
        $now = FrozenTime::now();
        $title = 'test_create_' . $now->format('YmdHis');
        $data = [
            'title' => $title,
            'content' => 'This is test content',
        ];
        $this->post(['_name' => 'create_table_template'], $data);
        $this->assertRedirect(['_name' => 'table_templates']);

        // データが存在する
        $this->assertTrue($this->TableTemplates->exists(compact('title')));
    }

    /**
     * 更新（失敗）
     *
     * @return void
     */
    public function testUpdateFailed()
    {
        $tableTemplate = $this->TableTemplates->find()->first();

        $this->enableCsrfToken();
        $now = FrozenTime::now();
        $title = 'test_update_' . $now->format('YmdHis');
        $data = [
            'title' => $title,
            'content' => '',
        ];
        $this->put(['_name' => 'update_table_template', $tableTemplate->id], $data);
        $this->assertResponseCode(400);
        $this->assertResponseContains(__('Edit'));

        // データが存在しない
        $this->assertFalse($this->TableTemplates->exists(compact('title')));
    }

    /**
     * 更新
     *
     * @return void
     */
    public function testUpdate()
    {
        $tableTemplate = $this->TableTemplates->find()->first();

        $this->enableCsrfToken();
        $now = FrozenTime::now();
        $title = 'test_update_' . $now->format('YmdHis');
        $data = [
            'title' => $title,
            'content' => 'This is test content',
        ];
        $this->put(['_name' => 'update_table_template', $tableTemplate->id], $data);
        $this->assertRedirect(['_name' => 'table_templates']);

        // データが存在する
        $this->assertTrue($this->TableTemplates->exists(compact('title')));
    }

    /**
     * 削除（失敗）
     *
     * @return void
     */
    public function testDeleteNotFound()
    {
        $this->enableCsrfToken();
        $this->get(['_name' => 'delete_table_template', 999]);
        $this->assertResponseError();
        $this->assertResponseCode(404);
    }

    /**
     * 削除
     *
     * @return void
     */
    public function testDelete()
    {
        $tableTemplate = $this->TableTemplates->find()->first();

        $this->enableCsrfToken();
        $this->delete(['_name' => 'delete_table_template', $tableTemplate->id]);
        $this->assertRedirect(['_name' => 'table_templates']);

        // データが存在しない
        $this->assertFalse($this->TableTemplates->exists(['id' => $tableTemplate->id]));
    }
}
