<?php
namespace Gotea\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Security;

/**
 * タイトル成績モデルのテストケース
 *
 * @property \Gotea\Model\Table\TitleScoresTable $TitleScores
 */
class TitleScoresTableTest extends TestCase
{
    /**
     * タイトル成績
     *
     * @var \Gotea\Model\Table\TitleScoresTable
     */
    public $TitleScores;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.TitleScores',
        'app.TitleScoreDetails',
        'app.Players',
        'app.Titles',
        'app.Countries',
        'app.Ranks',
        'app.Organizations',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('TitleScores') ? [] : ['className' => 'Gotea\Model\Table\TitleScoresTable'];
        $this->TitleScores = TableRegistry::getTableLocator()->get('TitleScores', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TitleScores);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->TitleScores->initialize([]);
        $this->assertEquals($this->TitleScores->getTable(), 'title_scores');
        $this->assertEquals($this->TitleScores->getDisplayField(), 'name');
        $this->assertEquals($this->TitleScores->getPrimaryKey(), 'id');

        // Association
        $associations = collection($this->TitleScores->associations());
        $compare = ['Titles', 'Countries', 'TitleScoreDetails'];
        $this->assertEquals($associations->count(), count($compare));
        $associations->each(function ($a) use ($compare) {
            $this->assertTrue(in_array($a->getName(), $compare, true));
        });
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $params = [
            'country_id' => 1,
            'started' => '2019-01-02',
            'ended' => '2019-01-02',
        ];

        // success
        $result = $this->TitleScores->newEntity($params);
        $this->assertEmpty($result->getErrors());

        // requirePresence
        $names = array_keys($params);
        foreach ($names as $name) {
            $data = $params;
            unset($data[$name]);
            $result = $this->TitleScores->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // integer
        $names = ['id', 'title_id', 'country_id'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = '1a';
            $result = $this->TitleScores->newEntity($data);
            $this->assertNotEmpty($result->getErrors());

            $data[$name] = 'test';
            $result = $this->TitleScores->newEntity($data);
            $this->assertNotEmpty($result->getErrors());

            $data[$name] = '0.5';
            $result = $this->TitleScores->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // maxLength
        $names = [
            'name' => 100,
            'result' => 30,
        ];
        foreach ($names as $name => $length) {
            $data = $params;
            $data[$name] = Security::randomString($length + 1);
            $result = $this->TitleScores->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // date
        $names = ['started', 'ended'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = '20180101';
            $result = $this->TitleScores->newEntity($data);
            $this->assertNotEmpty($result->getErrors());

            $data[$name] = 'testtest';
            $result = $this->TitleScores->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // boolean
        $names = ['is_world', 'is_official'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = '0.5';
            $result = $this->TitleScores->newEntity($data);
            $this->assertNotEmpty($result->getErrors());

            $data[$name] = 'test';
            $result = $this->TitleScores->newEntity($data);
            $this->assertNotEmpty($result->getErrors());
        }

        // allowEmptyString
        $names = ['title_id', 'name', 'result'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = '';
            $result = $this->TitleScores->newEntity($data);
            $this->assertEmpty($result->getErrors());
        }

        // id (update)
        $data = $params;
        $data['id'] = '';
        $exist = $this->TitleScores->get(1);
        $result = $this->TitleScores->patchEntity($exist, $data);
        $this->assertNotEmpty($result->getErrors());

        // id (create)
        $result = $this->TitleScores->newEntity($data);
        $this->assertEmpty($result->getErrors());
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        // title_id が存在しない値
        $result = $this->TitleScores->newEntity([
            'title_id' => 999,
        ]);
        $this->assertFalse($this->TitleScores->checkRules($result));

        // country_id が存在しない値
        $result = $this->TitleScores->newEntity([
            'country_id' => 999,
        ]);
        $this->assertFalse($this->TitleScores->checkRules($result));

        // 成功
        $result = $this->TitleScores->newEntity([
            'title_id' => 1,
            'country_id' => 1,
        ]);
        $this->assertTrue($this->TitleScores->checkRules($result));
    }

    /**
     * 1件取得
     *
     * @return void
     */
    public function testFindByIdWithRelation()
    {
        $score = $this->TitleScores->findByIdWithRelation(1);
        $this->assertNotNull($score);
        $this->assertNotNull($score->country);
        $this->assertNotNull($score->title);
        $this->assertGreaterThan(0, count($score->title_score_details));
    }

    /**
     * タイトル成績検索（データ有り）
     *
     * @return void
     */
    public function testFindMatches()
    {
        // 対局年
        $year = 2017;
        $scores = $this->TitleScores->findMatches([
            'target_year' => $year,
        ]);
        $this->assertGreaterThan(0, $scores->count());
        $scores->each(function ($item) use ($year) {
            $this->assertEquals($item->started->year, $year);
            $this->assertEquals($item->ended->year, $year);
        });

        // タイトル名
        $name = 'World';
        $scores = $this->TitleScores->findMatches([
            'title_name' => $name,
        ]);
        $this->assertGreaterThan(0, $scores->count());
        $scores->each(function ($item) use ($name) {
            $this->assertContains($name, $item->name);
        });

        // 対局日
        $start = '2017/12/01';
        $end = '2017/12/31';
        $scores = $this->TitleScores->findMatches([
            'started' => $start,
            'ended' => $end,
        ]);
        $this->assertGreaterThan(0, $scores->count());
        $scores->each(function ($item) use ($start, $end) {
            $this->assertGreaterThanOrEqual($start, $item->started->format('Y/m/d'));
            $this->assertLessThanOrEqual($end, $item->started->format('Y/m/d'));
        });
    }

    /**
     * タイトル成績検索（データ無し）
     *
     * @return void
     */
    public function testFindMatchesNoData()
    {
        $scores = $this->TitleScores->findMatches([
            'target_year' => 9999,
        ]);
        $this->assertEquals(0, $scores->count());
    }
}
