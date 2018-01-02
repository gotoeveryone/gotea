<?php
namespace Gotea\Test\TestCase\Model\Table;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\ServerRequestFactory;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Gotea\Model\Table\PlayersTable;

/**
 * 棋士モデルのテストケース
 *
 * @property \Gotea\Model\Table\PlayersTable $Players
 */
class PlayersTableTest extends TestCase
{
    /**
     * 棋士モデル
     *
     * @var \Gotea\Model\Table\PlayersTable
     */
    public $Players;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.players',
        'app.countries',
        'app.ranks',
        'app.organizations',
        'app.player_ranks',
        'app.player_scores',
        'app.retention_histories',
        'app.titles',
        'app.title_score_details',
        'app.title_scores',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Players') ? [] : ['className' => PlayersTable::class];
        $this->Players = TableRegistry::get('Players', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
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
            'joined' => '20180101',
        ];

        // success
        $result = $this->Players->newEntity($params);
        $this->assertEmpty($result->errors());
        $this->assertEmpty($result->getErrors());

        // requirePresence
        foreach ($params as $name => $value) {
            // country_id
            $data = $params;
            unset($data[$name]);
            $result = $this->Players->newEntity($data);
            $this->assertNotEmpty($result->errors());
            $this->assertNotEmpty($result->getErrors());
        }

        // integer
        $names = ['country_id', 'rank_id', 'organization_id'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = '1a';
            $result = $this->Players->newEntity($data);
            $this->assertNotEmpty($result->errors());
            $this->assertNotEmpty($result->getErrors());

            $data[$name] = 'test';
            $result = $this->Players->newEntity($data);
            $this->assertNotEmpty($result->errors());
            $this->assertNotEmpty($result->getErrors());

            $data[$name] = '0.5';
            $result = $this->Players->newEntity($data);
            $this->assertNotEmpty($result->errors());
            $this->assertNotEmpty($result->getErrors());
        }

        // alphaNumeric
        $names = ['name_english'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = 'テスト';
            $result = $this->Players->newEntity($data);
            $this->assertNotEmpty($result->errors());
            $this->assertNotEmpty($result->getErrors());
        }

        // naturalNumber
        $names = ['joined'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = 'test';
            $result = $this->Players->newEntity($data);
            $this->assertNotEmpty($result->errors());
            $this->assertNotEmpty($result->getErrors());
        }

        // maxLength
        // name
        $data = $params;
        $data['name'] = '123456789012345678901';
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->errors());
        $this->assertNotEmpty($result->getErrors());

        // name_english
        $data = $params;
        $data['name_english'] = '12345678901234567890123456789012345678901';
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->errors());
        $this->assertNotEmpty($result->getErrors());

        // name_other
        $data = $params;
        $data['name_other'] = '123456789012345678901';
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->errors());
        $this->assertNotEmpty($result->getErrors());

        // lengthBetween
        // joined
        $data = $params;
        $data['joined'] = '201';
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->errors());
        $this->assertNotEmpty($result->getErrors());
        $data['joined'] = '201801010';
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->errors());
        $this->assertNotEmpty($result->getErrors());

        // date
        // birthday
        $data = $params;
        $data['birthday'] = '20180101';
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->errors());
        $this->assertNotEmpty($result->getErrors());
        $data['birthday'] = 'testtest';
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->errors());
        $this->assertNotEmpty($result->getErrors());

        // date
        // retired
        $data = $params;
        $data['retired'] = '20180101';
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->errors());
        $this->assertNotEmpty($result->getErrors());
        $data['retired'] = 'testtest';
        $result = $this->Players->newEntity($data);
        $this->assertNotEmpty($result->errors());
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
    }

    /**
     * 段位別棋士数取得
     *
     * @return void
     */
    public function testFindRanksCount()
    {
        $ranks = $this->Players->findRanksCount(1);

        $number = 0;
        $ranks->each(function ($item, $key) use (&$number) {
            if ($number) {
                $this->assertLessThanOrEqual($number, $item->rank);
            }
            $number = $item->rank;
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
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof RecordNotFoundException);
        }
    }
}
