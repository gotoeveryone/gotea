<?php
declare(strict_types=1);

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
        $config = TableRegistry::getTableLocator()->exists('RetentionHistories') ? [] : ['className' => RetentionHistoriesTable::class];
        $this->RetentionHistories = TableRegistry::getTableLocator()->get('RetentionHistories', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->RetentionHistories);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->RetentionHistories->initialize([]);
        $this->assertEquals($this->RetentionHistories->getTable(), 'retention_histories');
        $this->assertEquals($this->RetentionHistories->getDisplayField(), 'name');
        $this->assertEquals($this->RetentionHistories->getPrimaryKey(), 'id');

        // Association
        $associations = collection($this->RetentionHistories->associations());
        $compare = ['Titles', 'Players', 'Countries'];
        $this->assertEquals($associations->count(), count($compare));
        $associations->each(function ($a) use ($compare) {
            $this->assertTrue(in_array($a->getName(), $compare, true));
        });
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
            'acquired' => '2019/04/01',
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

        // notEmpty
        $names = [
            'title_id',
            'holding',
            'target_year',
            'name',
            'is_team',
            'acquired',
            'is_official',
        ];
        foreach ($params as $name => $value) {
            $data = $params;
            unset($data[$name]);
            $result = $this->RetentionHistories->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // integer
        $names = ['title_id', 'player_id', 'country_id', 'holding', 'target_year'];
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

        // date
        $names = ['acquired'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = '20180101';
            $result = $this->RetentionHistories->newEntity($data);
            $this->assertNotEmpty($result->getErrors());

            $data[$name] = 'testtest';
            $result = $this->RetentionHistories->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // boolean
        $names = ['is_team', 'is_official'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = '0.5';
            $result = $this->RetentionHistories->newEntity($data);
            $this->assertNotEmpty($result->getErrors());

            $data[$name] = 'test';
            $result = $this->RetentionHistories->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // allowEmptyString
        $names = ['country_id'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = '';
            $result = $this->RetentionHistories->newEntity($data);
            $this->assertEmpty($result->getErrors());
        }

        // id
        $data = $params;
        $data['id'] = '';
        $exist = $this->RetentionHistories->get(1);
        $result = $this->RetentionHistories->patchEntity($exist, $data);
        $this->assertNotEmpty($result->getErrors());

        // custom
        $data = $params;
        $data['is_team'] = '1';
        $data['player_id'] = '1';
        $data['win_group_name'] = '';
        $result = $this->RetentionHistories->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        $data['is_team'] = '0';
        $data['player_id'] = '';
        $data['win_group_name'] = 'test';
        $result = $this->RetentionHistories->newEntity($data);
        $this->assertNotEmpty($result->getErrors());

        $data['is_team'] = '1';
        $data['player_id'] = '';
        $data['win_group_name'] = 'test';
        $result = $this->RetentionHistories->newEntity($data);
        $this->assertEmpty($result->getErrors());

        $data['is_team'] = '0';
        $data['player_id'] = '1';
        $data['win_group_name'] = '';
        $result = $this->RetentionHistories->newEntity($data);
        $this->assertEmpty($result->getErrors());
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $data = $this->RetentionHistories->find()->first();

        // title_id と holding が重複
        $result = $this->RetentionHistories->newEntity([
            'title_id' => $data->title_id,
            'holding' => $data->holding,
        ]);
        $this->assertNotEmpty($result->getErrors());

        // リレーション先に存在しないIDを設定
        $keys = ['title_id', 'player_id', 'country_id'];
        foreach ($keys as $key) {
            $result = $this->RetentionHistories->newEntity([
                $key => 999,
            ]);
            $this->assertFalse($this->RetentionHistories->checkRules($result));
        }

        // 成功
        $data = [];
        foreach ($keys as $key) {
            $data[$key] = 1;
        }
        $result = $this->RetentionHistories->newEntity($data);
        $this->assertTrue($this->RetentionHistories->checkRules($result));
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
            'acquired' => '2019/04/01',
            'player_id' => 1,
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
                $this->assertNotNull($history->win_group_name);
            } else {
                $this->assertNotNull($history->player_id);
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
                $this->assertNotNull($history->win_group_name);
            } else {
                $this->assertNotNull($history->player_id);
                $this->assertNull($history->win_group_name);
            }
            $this->assertNotNull($history->title);
        }
    }
}
