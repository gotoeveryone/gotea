<?php
namespace Gotea\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Gotea\Model\Table\PlayerRanksTable;

/**
 * 棋士昇段モデルのテストケース
 *
 * @property \Gotea\Model\Table\PlayerRanksTable $PlayerRanks
 */
class PlayerRanksTableTest extends TestCase
{

    /**
     * 棋士昇段
     *
     * @var \Gotea\Model\Table\PlayerRanksTable
     */
    public $PlayerRanks;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.player_ranks',
        'app.players',
        'app.countries',
        'app.ranks',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('PlayerRanks') ? [] : ['className' => PlayerRanksTable::class];
        $this->PlayerRanks = TableRegistry::get('PlayerRanks', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PlayerRanks);

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
            'player_id' => 1,
            'rank_id' => 1,
            'promoted' => '2018/01/01',
        ];

        // success
        $result = $this->PlayerRanks->newEntity($params);
        $this->assertEmpty($result->errors());
        $this->assertEmpty($result->getErrors());

        // requirePresence
        foreach ($params as $name => $value) {
            $data = $params;
            unset($data[$name]);
            $result = $this->PlayerRanks->newEntity($data);
            $this->assertNotEmpty($result->errors());
            $this->assertNotEmpty($result->getErrors());
        }

        // integer
        $names = ['player_id', 'rank_id'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = '1a';
            $result = $this->PlayerRanks->newEntity($data);
            $this->assertNotEmpty($result->errors());
            $this->assertNotEmpty($result->getErrors());

            $data[$name] = 'test';
            $result = $this->PlayerRanks->newEntity($data);
            $this->assertNotEmpty($result->errors());
            $this->assertNotEmpty($result->getErrors());

            $data[$name] = '0.5';
            $result = $this->PlayerRanks->newEntity($data);
            $this->assertNotEmpty($result->errors());
            $this->assertNotEmpty($result->getErrors());
        }

        // date
        // promoted
        $data = $params;
        $data['promoted'] = '20180101';
        $result = $this->PlayerRanks->newEntity($data);
        $this->assertNotEmpty($result->errors());
        $this->assertNotEmpty($result->getErrors());
        $data['promoted'] = 'testtest';
        $result = $this->PlayerRanks->newEntity($data);
        $this->assertNotEmpty($result->errors());
        $this->assertNotEmpty($result->getErrors());

        // allowEmpty
        // id
        $data = $params;
        $data['id'] = '';
        $exist = $this->PlayerRanks->get(1);
        $result = $this->PlayerRanks->patchEntity($exist, $data);
        $this->assertNotEmpty($result->errors());
        $this->assertNotEmpty($result->getErrors());
    }

    /**
     * 保存
     *
     * @return void
     */
    public function testSave()
    {
        $rank = $this->PlayerRanks->newEntity([
            'player_id' => 3,
            'rank_id' => 3,
            'promoted' => '2018/01/03',
        ]);

        $save = $this->PlayerRanks->save($rank, [
            'account' => env('TEST_USER'),
        ]);
        $this->assertNotEquals(false, $save);

        // 棋士情報の段位とは一致しない
        $saveRank = $this->PlayerRanks->get($save->id, [
            'contain' => 'Players',
        ]);
        $this->assertNotEquals($saveRank->rank_id, $saveRank->player->rank_id);

        // 最新として登録
        $rank = $this->PlayerRanks->newEntity([
            'player_id' => 3,
            'rank_id' => 4,
            'promoted' => '2018/01/03',
            'newest' => true,
        ]);

        $save = $this->PlayerRanks->save($rank, [
            'account' => env('TEST_USER'),
        ]);
        $this->assertNotEquals(false, $save);

        // 棋士情報の段位と一致
        $saveRank = $this->PlayerRanks->get($save->id, [
            'contain' => 'Players',
        ]);
        $this->assertEquals($saveRank->rank_id, $saveRank->player->rank_id);
    }

    /**
     * 昇段情報取得（データ有り）
     *
     * @return void
     */
    public function testFindRanks()
    {
        $ranks = $this->PlayerRanks->findRanks(1);
        $this->assertGreaterThan(0, $ranks->count());
    }

    /**
     * 昇段情報取得（データ無し）
     *
     * @return void
     */
    public function testFindRanksNoData()
    {
        $ranks = $this->PlayerRanks->findRanks(2);
        $this->assertEquals(0, $ranks->count());
    }

    /**
     * 最近の昇段者取得
     *
     * @return void
     */
    public function testFindRecentPromoted()
    {
        $ranks = $this->PlayerRanks->findRecentPromoted();
        $ranks->each(function ($item) {
            $this->assertGreaterThan(1, $item->rank->rank_numeric);
        });
    }
}
