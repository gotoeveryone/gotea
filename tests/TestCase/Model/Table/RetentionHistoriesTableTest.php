<?php
namespace Gotea\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Gotea\Model\Entity\RetentionHistory;
use Gotea\Model\Table\RetentionHistoriesTable;

/**
 * Gotea\Model\Table\RetentionHistoriesTable Test Case
 */
class RetentionHistoriesTableTest extends TestCase
{

    /**
     * Test subject
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
        'app.RetentionHistories',
        'app.Titles',
        'app.Countries',
        'app.Players',
        'app.Ranks',
        'app.Organizations',
        'app.PlayerRanks',
        'app.PlayerScores',
        'app.TitleScoreDetails',
        'app.TitleScores'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('RetentionHistories') ? [] : ['className' => RetentionHistoriesTable::class];
        $this->RetentionHistories = TableRegistry::getTableLocator()->get('RetentionHistories', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->RetentionHistories);

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
            'title_id' => 1,
            'holding' => 1,
            'target_year' => 2017,
            'name' => 'test',
            'player_id' => 1,
            'rank_id' => 1,
        ];

        // success
        $result = $this->RetentionHistories->newEntity($params);
        $this->assertEmpty($result->getErrors());

        // requirePresence
        foreach ($params as $name => $value) {
            $data = $params;
            unset($data[$name]);
            $result = $this->RetentionHistories->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // integer
        $names = ['player_id', 'rank_id', 'holding', 'target_year'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = '1a';
            $result = $this->RetentionHistories->newEntity($data);
            $this->assertNotEmpty($result->getErrors());

            $data[$name] = 'test';
            $result = $this->RetentionHistories->newEntity($data);
            $this->assertNotEmpty($result->getErrors());

            $data[$name] = '0.5';
            $result = $this->RetentionHistories->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // notEmpty
        // player_id
        $data = $params;
        $data['player_id'] = '';
        $result = $this->RetentionHistories->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        // rank_id
        $data = $params;
        $data['rank_id'] = '';
        $result = $this->RetentionHistories->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        // win_group_name
        $data = $params;
        $data['is_team'] = '1';
        $data['win_group_name'] = '';
        $result = $this->RetentionHistories->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        // id
        $data = $params;
        $data['id'] = '';
        $exist = $this->RetentionHistories->get(1);
        $result = $this->RetentionHistories->patchEntity($exist, $data);
        $this->assertNotEmpty($result->getErrors());

        // date
        // acquired
        $data = $params;
        $data['acquired'] = '20180101';
        $result = $this->RetentionHistories->newEntity($data);
        $this->assertNotEmpty($result->getErrors());
    }

    /**
     * Test save method
     *
     * @return void
     */
    public function testSave()
    {
        // 保存失敗
        $entity = $this->RetentionHistories->newEntity([
            'title_id' => 1,
            'holding' => 3,
            'target_year' => 2017,
            'name' => 'test title',
            'is_team' => 1,
            'player_id' => 1,
            'rank_id' => 1,
        ]);
        $result = $this->RetentionHistories->save($entity, [
            'account' => 'test',
        ]);
        $this->assertFalse($result);

        // 保存成功
        $entity = $this->RetentionHistories->newEntity([
            'title_id' => 1,
            'holding' => 3,
            'target_year' => 2017,
            'name' => 'test title',
            'is_team' => 0,
            'player_id' => 1,
            'rank_id' => 1,
        ]);
        $result = $this->RetentionHistories->save($entity, [
            'account' => 'test',
        ]);
        $this->assertInstanceOf(RetentionHistory::class, $result);
    }

    /**
     * Test findHistoriesByPlayer method
     *
     * @return void
     */
    public function testFindHistoriesByPlayer()
    {
        $histories = $this->RetentionHistories->findHistoriesByPlayer(1);
        foreach ($histories as $history) {
            if ($history->is_team) {
                $this->assertNull($history->player_id);
                $this->assertNull($history->rank_id);
                $this->assertNotNull($history->win_group_name);
            } else {
                $this->assertNotNull($history->player_id);
                $this->assertNotNull($history->rank_id);
                $this->assertNull($history->win_group_name);
            }
            $this->assertNotNull($history->title);
            $this->assertNotNull($history->title->country);
        }
    }

    /**
     * Test findHistoriesByTitle method
     *
     * @return void
     */
    public function testFindHistoriesByTitle()
    {
        $histories = $this->RetentionHistories->findHistoriesByTitle(1);
        foreach ($histories as $history) {
            if ($history->is_team) {
                $this->assertNull($history->player_id);
                $this->assertNull($history->rank_id);
                $this->assertNotNull($history->win_group_name);
            } else {
                $this->assertNotNull($history->player_id);
                $this->assertNotNull($history->rank_id);
                $this->assertNull($history->win_group_name);
            }
            $this->assertNotNull($history->title);
        }
    }
}
