<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Model\Table;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Exception;
use Gotea\Model\Table\PlayersTable;

/**
 * 棋士モデルのテストケース
 *
 * @property \Gotea\Model\Table\PlayersTable $Players
 */
class PlayersTableTest extends TestCase
{
    /**
     * 棋士テーブルクラス
     *
     * @var \Gotea\Model\Table\PlayersTable
     */
    private $Players;

    /**
     * Fixtures
     *
     * @var array
     */
    public array $fixtures = [
        'app.Players',
        'app.Countries',
        'app.Ranks',
        'app.Organizations',
        'app.PlayerRanks',
        'app.PlayerScores',
        'app.RetentionHistories',
        'app.Titles',
        'app.TitleScoreDetails',
        'app.TitleScores',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Players') ? [] : ['className' => PlayersTable::class];
        $this->Players = TableRegistry::getTableLocator()->get('Players', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Players);

        parent::tearDown();
    }

    /**
     * バリデーション
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $params = [
            'country_id' => 1,
            'rank_id' => 1,
            'organization_id' => 1,
            'name' => '12345678901234567890',
            'name_english' => '1234567890123456789012345678901234567890',
            'sex' => '男性',
            'joined_year' => 2018,
            'joined_month' => 1,
            'joined_day' => 1,
        ];

        // success
        $result = $this->Players->newEntity($params);
        $this->assertEmpty($result->getErrors());

        // requirePresence
        $requiredNames = [
            'country_id',
            'rank_id',
            'organization_id',
            'name',
            'name_english',
            'sex',
            'joined_year',
        ];
        foreach ($requiredNames as $name) {
            $data = $params;
            unset($data[$name]);
            $result = $this->Players->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // integer
        $names = ['country_id', 'rank_id', 'organization_id'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = '1a';
            $result = $this->Players->newEntity($data);
            $this->assertNotEmpty($result->getErrors());

            $data[$name] = 'test';
            $result = $this->Players->newEntity($data);
            $this->assertNotEmpty($result->getErrors());

            $data[$name] = '0.5';
            $result = $this->Players->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // alphaNumeric
        $names = ['name_english'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = 'テスト';
            $result = $this->Players->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // integer
        $names = ['joined_year', 'joined_month', 'joined_day'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = 'test';
            $result = $this->Players->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // maxLength
        // name
        $data = $params;
        $data['name'] = '123456789012345678901';
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        // name_english
        $data = $params;
        $data['name_english'] = '12345678901234567890123456789012345678901';
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        // name_other
        $data = $params;
        $data['name_other'] = '123456789012345678901';
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        // range
        // joined_year
        $data = $params;
        $data['joined_year'] = 0;
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->getErrors());
        $data['joined_year'] = 10000;
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        // joined_month
        $data = $params;
        $data['joined_month'] = 0;
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->getErrors());
        $data['joined_month'] = 13;
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        // joined_day
        $data = $params;
        $data['joined_day'] = 0;
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->getErrors());
        $data['joined_day'] = 32;
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        // joined_day には joined_month が必要
        $data = $params;
        $data['joined_month'] = null;
        $data['joined_day'] = 1;
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        // joined_date の実在チェック
        $data = $params;
        $data['joined_year'] = 2026;
        $data['joined_month'] = 2;
        $data['joined_day'] = 31;
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        // うるう年の日付は許可
        $data = $params;
        $data['joined_year'] = 2024;
        $data['joined_month'] = 2;
        $data['joined_day'] = 29;
        $result = $this->Players->newEntity($data);
        $this->assertEmpty($result->getErrors());

        // date
        // birthday
        $data = $params;
        $data['birthday'] = '20180101';
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->getErrors());
        $data['birthday'] = 'testtest';
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        // date
        // retired
        $data = $params;
        $data['retired'] = '20180101';
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->getErrors());
        $data['retired'] = 'testtest';
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->getErrors());
    }

    /**
     * 棋士検索
     *
     * @return void
     */
    public function testFindPlayers()
    {
        $players = $this->Players->findPlayers([
            'sex' => '男性',
        ]);
        $this->assertGreaterThan(0, $players->count());
        $players->all()->each(function ($data) {
            $this->assertEquals('男性', $data->sex);
        });

        // ソート
        $players = $this->Players->findPlayers([
            'sort' => 'id',
            'direction' => 'desc',
        ]);
        $this->assertGreaterThan(0, $players->count());
        $tmpId = null;
        $players->all()->each(function ($data) use (&$tmpId) {
            if ($tmpId != null) {
                $this->assertLessThan($tmpId, $data->id);
            }
            $tmpId = $data->id;
        });
    }

    /**
     * 段位別棋士数取得
     *
     * @return void
     */
    public function testFindRanksCount()
    {
        $ranks = $this->Players->findRanksCount(1)->all();

        // 段位降順で並んでいる
        $ranksArray = $ranks->toArray();
        $ranks->each(function ($item, $idx) use ($ranksArray) {
            if ($idx > 0) {
                $this->assertLessThanOrEqual(
                    $ranksArray[$idx - 1]->rank_numeric,
                    $item->rank_numeric,
                );
            }
        });

        // 所属組織で絞り込み
        $organizations = TableRegistry::getTableLocator()->get('Organizations');
        $organization = $organizations->findByCountryId(1)->first();
        $ranks = $this->Players->findRanksCount(1, $organization->id)->all();
        $ranks->each(function ($item) use ($organization) {
            $playerCount = $this->Players->find()->where([
                'rank_id' => $item->id,
                'country_id' => 1,
                'organization_id' => $organization->id,
                'is_retired' => false,
            ])->count();
            $this->assertEquals($playerCount, $item->count);
        });

        // 退役者を含めて集計できる
        $ranks = $this->Players->findRanksCount(1, null, true)->all();
        $ranks->each(function ($item) {
            $playerCount = $this->Players->find()->where([
                'rank_id' => $item->id,
                'country_id' => 1,
            ])->count();
            $this->assertEquals($playerCount, $item->count);
        });
    }

    /**
     * 棋士1件取得
     *
     * @return void
     */
    public function testFindByIdWithRelation()
    {
        $player = $this->Players->findByIdWithRelation(1);
        $this->assertNotNull($player);
        $this->assertNotNull($player->country);
        $this->assertNotNull($player->rank);
        $this->assertGreaterThan(0, count($player->player_ranks));
        $this->assertGreaterThan(0, count($player->title_score_details));
    }

    /**
     * 棋士1件取得（データ無し）
     *
     * @return void
     */
    public function testFindByIdWithRelationNoData()
    {
        try {
            $this->Players->findByIdWithRelation(99999);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof RecordNotFoundException);
        }
    }

    /**
     * 棋士名・所属国から棋士1件取得
     *
     * @return void
     */
    public function testFindRankByNamesAndCountries()
    {
        $player = $this->Players->findRankByNamesAndCountries(['Test Player 1', 'Test Player 111'], 1);
        $this->assertStringContainsString('Test Player 1', $player->name);
        $this->assertStringNotContainsString('Test Player 2', $player->name);
    }

    /**
     * 棋士名・所属国から棋士1件取得（データなし）
     *
     * @return void
     */
    public function testFindRankByNamesAndCountriesNoData()
    {
        try {
            $this->Players->findRankByNamesAndCountries(['Test', 'Player 555'], 1);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof RecordNotFoundException);
        }
    }
}
