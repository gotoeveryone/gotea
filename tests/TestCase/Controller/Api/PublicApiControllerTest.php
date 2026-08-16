<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Controller\Api;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

/**
 * 公開 API コントローラのテスト。
 */
class PublicApiControllerTest extends ApiTestCase
{
    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Countries',
        'app.Notifications',
        'app.Players',
        'app.Ranks',
        'app.PlayerRanks',
        'app.RetentionHistories',
        'app.Titles',
        'app.Organizations',
        'app.TitleScores',
        'app.TitleScoreDetails',
        'app.UpdatedPoints',
    ];

    private mixed $originalApiKey;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->originalApiKey = Configure::read('App.publicApi.key');
        Configure::write('App.publicApi.key', 'test-public-key');
        $this->configRequest([
            'headers' => [
                'Authorization' => 'Bearer test-public-key',
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        Configure::write('App.publicApi.key', $this->originalApiKey);
        parent::tearDown();
    }

    /**
     * キーなしのアクセスを拒否します。
     *
     * @return void
     */
    public function testUnauthorized(): void
    {
        $this->configRequest([
            'headers' => [
                'Authorization' => 'Bearer invalid-key',
            ],
        ]);
        $this->get('/api/public/titles');

        $this->assertResponseCode(401);
        $this->assertJsonContentType();
    }

    /**
     * タイトル一覧は公開対象を返します。
     *
     * @return void
     */
    public function testTitles(): void
    {
        $this->get('/api/public/titles');

        $this->assertResponseSuccess();
        $this->assertJsonContentType();
        $titles = json_decode($this->_getBodyAsString())->response;
        $countries = array_map(static fn($title): string => $title->countryCode, $titles);

        $this->assertContains('jp', $countries);
        $this->assertContains('wr', $countries);
        $this->assertNotContains('cn', $countries);
    }

    /**
     * タイトル詳細は保持履歴を含みます。
     *
     * @return void
     */
    public function testTitle(): void
    {
        $this->get('/api/public/titles/jp/Lorem%20ip');

        $this->assertResponseSuccess();
        $this->assertJsonContentType();
        $title = json_decode($this->_getBodyAsString())->response;

        $this->assertSame(1, $title->id);
        $this->assertNotEmpty($title->histories);
    }

    /**
     * 非公開タイトルは存在を隠します。
     *
     * @return void
     */
    public function testTitleNotFoundForNonOutputTitle(): void
    {
        $this->get('/api/public/titles/cn/Lorem%20ip');

        $this->assertResponseCode(404);
        $this->assertJsonContentType();
    }

    /**
     * お知らせは公開済みを最大5件返します。
     *
     * @return void
     */
    public function testNotifications(): void
    {
        $notificationsTable = TableRegistry::getTableLocator()->get('Notifications');
        $notificationsTable->updateAll(
            ['is_draft' => false],
            [],
        );
        $notificationsTable->updateAll(
            ['is_permanent' => true],
            ['id' => 1],
        );

        $this->get('/api/public/notifications');

        $this->assertResponseSuccess();
        $this->assertJsonContentType();
        $notifications = json_decode($this->_getBodyAsString())->response;
        $this->assertCount(5, $notifications);
        $this->assertContains(true, array_map(
            static fn($notification): bool => $notification->isPermanent,
            $notifications,
        ));
    }

    /**
     * お知らせの常時表示・通常表示を切り替えられます。
     *
     * @return void
     */
    public function testNotificationsByPermanent(): void
    {
        $notificationsTable = TableRegistry::getTableLocator()->get('Notifications');
        $notificationsTable->updateAll(['is_draft' => false], []);
        $notificationsTable->updateAll(['is_permanent' => true], ['id' => 1]);

        $this->get('/api/public/notifications?permanent=true');
        $permanent = json_decode($this->_getBodyAsString())->response;
        $this->assertCount(1, $permanent);
        $this->assertTrue($permanent[0]->isPermanent);

        $this->configRequest([
            'headers' => [
                'Authorization' => 'Bearer test-public-key',
            ],
        ]);
        $this->get('/api/public/notifications?permanent=false');
        $regular = json_decode($this->_getBodyAsString())->response;
        $this->assertCount(4, $regular);
        $this->assertNotContains(true, array_map(
            static fn($notification): bool => $notification->isPermanent,
            $regular,
        ));
    }

    /**
     * お知らせの常時表示指定が不正な場合は拒否します。
     *
     * @return void
     */
    public function testNotificationsInvalidPermanent(): void
    {
        $this->get('/api/public/notifications?permanent=invalid');

        $this->assertResponseCode(400);
        $this->assertJsonContentType();
    }

    /**
     * ランキングを既存 API と同じ形式で返します。
     *
     * @return void
     */
    public function testRanking(): void
    {
        $this->get('/api/public/rankings/jp/2017/20');

        $this->assertResponseSuccess();
        $this->assertJsonContentType();
        $ranking = json_decode($this->_getBodyAsString())->response;
        $this->assertSame('jp', $ranking->countryCode);
        $this->assertObjectHasProperty('ranking', $ranking);
    }

    /**
     * ランキングの不正な集計種別を拒否します。
     *
     * @return void
     */
    public function testRankingInvalidType(): void
    {
        $this->get('/api/public/rankings/jp/2017/20?type=invalid');

        $this->assertResponseCode(400);
        $this->assertJsonContentType();
    }
}
