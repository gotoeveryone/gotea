<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Controller;

use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * お知らせコントローラのテスト
 *
 * @property \Gotea\Model\Table\NotificationsTable $Notifications
 */
class NotificationsControllerTest extends AppTestCase
{
    use IntegrationTestTrait;

    /**
     * お知らせモデル
     *
     * @var \Gotea\Model\Table\NotificationsTable
     */
    public $Notifications;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Countries',
        'app.Ranks',
        'app.Organizations',
        'app.PlayerRanks',
        'app.Players',
        'app.Notifications',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Notifications = TableRegistry::getTableLocator()->get('Notifications');
        $this->createSession();
    }

    /**
     * テンプレートが存在しない
     *
     * @return void
     */
    public function testMissingTemplate()
    {
        $this->get('/notifications/missing');
        $this->assertContainsError();
    }

    /**
     * 画面が見えるか
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get(['_name' => 'notifications']);
        $this->assertResponseOk();
        $this->assertTemplate('index');
        $this->assertResponseContains(__('一覧'));
    }

    /**
     * 新規作成
     *
     * @return void
     */
    public function testNew()
    {
        $this->get(['_name' => 'new_notification']);
        $this->assertResponseOk();
        $this->assertTemplate('new');
        $this->assertResponseContains(__('新規登録'));
    }

    /**
     * コピー（コピー元不正）
     *
     * @return void
     */
    public function testNewWithFromIdInvalid()
    {
        // 通常の新規登録になる
        $this->get(['_name' => 'new_notification', 'from' => 999]);
        $this->assertResponseOk();
        $this->assertTemplate('new');
        $this->assertResponseContains(__('新規登録'));
    }

    /**
     * コピー
     *
     * @return void
     */
    public function testNewWithFromId()
    {
        $notification = $this->Notifications->find()->first();

        $this->get(['_name' => 'new_notification', '?' => ['from' => $notification->id]]);
        $this->assertResponseOk();
        $this->assertTemplate('new');
        $this->assertResponseContains(__('新規登録'));

        $this->assertResponseContains($notification->title);
        $this->assertResponseContains($notification->content);

        // 投稿日時は一致していないこと
        $viewNotification = $this->viewVariable('notification');
        $this->assertNotEquals($notification->published, $viewNotification->published);
    }

    /**
     * 編集（データ無し）
     *
     * @return void
     */
    public function testEditNotFound()
    {
        $this->get(['_name' => 'edit_notification', 999]);
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
        $this->get(['_name' => 'edit_notification', 1]);
        $this->assertResponseOk();
        $this->assertTemplate('edit');
        $this->assertResponseContains(__('編集'));
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
            'is_draft' => false,
            'published' => $now,
        ];
        $this->post(['_name' => 'create_notification'], $data);
        $this->assertResponseCode(400);
        $this->assertResponseContains(__('新規登録'));

        // データが存在しない
        $this->assertFalse($this->Notifications->exists(compact('title')));
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
            'is_draft' => false,
            'published' => $now,
        ];
        $this->post(['_name' => 'create_notification'], $data);
        $this->assertRedirect(['_name' => 'notifications']);

        // データが存在する
        $this->assertTrue($this->Notifications->exists(compact('title')));
    }

    /**
     * 更新（失敗）
     *
     * @return void
     */
    public function testUpdateFailed()
    {
        $notification = $this->Notifications->find()->first();

        $this->enableCsrfToken();
        $now = FrozenTime::now();
        $title = 'test_update_' . $now->format('YmdHis');
        $data = [
            'title' => $title,
            'content' => '',
            'is_draft' => false,
            'published' => $now,
        ];
        $this->put(['_name' => 'update_notification', $notification->id], $data);
        $this->assertResponseCode(400);
        $this->assertResponseContains(__('編集'));

        // データが存在しない
        $this->assertFalse($this->Notifications->exists(compact('title')));
    }

    /**
     * 更新
     *
     * @return void
     */
    public function testUpdate()
    {
        $notification = $this->Notifications->find()->first();

        $this->enableCsrfToken();
        $now = FrozenTime::now();
        $title = 'test_update_' . $now->format('YmdHis');
        $data = [
            'title' => $title,
            'content' => 'This is test content',
            'is_draft' => false,
            'published' => $now,
        ];
        $this->put(['_name' => 'update_notification', $notification->id], $data);
        $this->assertRedirect(['_name' => 'notifications']);

        // データが存在する
        $this->assertTrue($this->Notifications->exists(compact('title')));
    }

    /**
     * 削除（失敗）
     *
     * @return void
     */
    public function testDeleteNotFound()
    {
        $this->enableCsrfToken();
        $this->get(['_name' => 'delete_notification', 999]);
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
        $notification = $this->Notifications->find()->first();

        $this->enableCsrfToken();
        $this->delete(['_name' => 'delete_notification', $notification->id]);
        $this->assertRedirect(['_name' => 'notifications']);

        // データが存在しない
        $this->assertFalse($this->Notifications->exists(['id' => $notification->id]));
    }
}
