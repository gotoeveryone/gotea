<?php

namespace Gotea\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;

/**
 * Gotea\Controller\PlayerRanksController Test Case
 *
 * @property \Gotea\Model\Table\PlayerRanksTable $PlayerRanks
 */
class PlayerRanksControllerTest extends AppTestCase
{
    /**
     * 棋士昇段モデル
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
        'app.players',
        'app.ranks',
        'app.player_ranks',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->PlayerRanks = TableRegistry::get('PlayerRanks');
        $this->_createSession();
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/ranks');
        $this->assertResponseOk();
        $this->assertTemplate('index');
        $this->assertResponseContains('<nav class="nav">');
    }

    /**
     * Test create method
     *
     * @return void
     */
    public function testCreateFailed()
    {
        $this->enableCsrfToken();
        $conditions = [
            'player_id' => 1,
            'rank_id' => 2,
        ];
        // データがすでに存在すること
        $this->assertEquals(1, $this->PlayerRanks->find()->where($conditions)->count());

        $data = $conditions;
        $data['promoted'] = '2017/12/10';
        $this->post(['_name' => 'create_ranks', 1], $data);
        $this->assertRedirect(['_name' => 'view_player', 'tab' => 'ranks', 1]);

        // 1件のまま
        $this->assertEquals(1, $this->PlayerRanks->find()->where($conditions)->count());
    }

    /**
     * Test create method
     *
     * @return void
     */
    public function testCreate()
    {
        $this->enableCsrfToken();
        $conditions = [
            'player_id' => 1,
            'rank_id' => 4,
        ];
        // データが存在しないこと
        $this->assertEquals(0, $this->PlayerRanks->find()->where($conditions)->count());

        $data = $conditions;
        $data['promoted'] = '2017/12/10';
        $this->post(['_name' => 'create_ranks', 1], $data);
        $this->assertRedirect(['_name' => 'view_player', 'tab' => 'ranks', 1]);
        $this->assertResponseNotContains('<nav class="nav">');

        // データが存在すること
        $this->assertEquals(1, $this->PlayerRanks->find()->where($conditions)->count());
    }

    /**
     * Test update method
     *
     * @return void
     */
    public function testUpdate()
    {
        // データが存在すること
        $rank = $this->PlayerRanks->get(1);
        $this->assertNotNull($rank);

        $this->enableCsrfToken();
        $this->put(['_name' => 'update_ranks', $rank->player_id, $rank->id], $rank->toArray());
        $this->assertRedirect(['_name' => 'view_player', 'tab' => 'ranks', 1]);
        $this->assertResponseNotContains('<nav class="nav">');

        // データが存在すること
        $this->assertEquals(1, $this->PlayerRanks->find()->where($rank->toArray())->count());
    }
}
