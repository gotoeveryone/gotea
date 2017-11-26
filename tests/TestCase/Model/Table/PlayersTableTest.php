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
     * 棋士検索
     *
     * @return void
     */
    public function testFindPlayers()
    {
        $request = ServerRequestFactory::fromGlobals();
        $request = $request->withData('sex', '男性');
        $players = $this->Players->findPlayers($request);
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
        $this->assertGreaterThan(0, iterator_count($player->player_ranks));
        $this->assertGreaterThan(0, iterator_count($player->title_score_details));
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
