<?php
namespace Gotea\Test\TestCase\Form;

use Cake\TestSuite\TestCase;
use Gotea\Form\TitleScoreForm;

/**
 * Gotea\Form\TitleScoreForm Test Case
 */
class TitleScoreFormTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Gotea\Form\TitleScoreForm
     */
    public $TitleScore;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->TitleScore = new TitleScoreForm();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TitleScore);

        parent::tearDown();
    }

    /**
     * Test validation fail
     *
     * @return void
     */
    public function testValidateFail()
    {
        $params = [
            'name' => '12345678901234567890',
            'country_id' => 1,
            'target_year' => 2017,
            'started' => '2018/01/01',
            'started' => '2018/12/31',
        ];

        // success
        $result = $this->TitleScore->validate($params);
        $this->assertTrue($result);
        $this->assertEmpty($this->TitleScore->errors());

        // integer
        $names = ['country_id', 'target_year'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = '1a';
            $result = $this->TitleScore->validate($data);
            $this->assertFalse($result);
            $this->assertNotEmpty($this->TitleScore->errors());

            $data[$name] = 'test';
            $result = $this->TitleScore->validate($data);
            $this->assertFalse($result);
            $this->assertNotEmpty($this->TitleScore->errors());

            $data[$name] = '0.5';
            $result = $this->TitleScore->validate($data);
            $this->assertFalse($result);
            $this->assertNotEmpty($this->TitleScore->errors());
        }

        // maxLength
        // name
        $data = $params;
        $data['name'] = '123456789012345678901';
        $result = $this->TitleScore->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->TitleScore->errors());

        // range
        // target_year
        $data = $params;
        $data['target_year'] = 0;
        $result = $this->TitleScore->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->TitleScore->errors());

        $data['target_year'] = 10000;
        $result = $this->TitleScore->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->TitleScore->errors());

        // date
        // started
        $data = $params;
        $data['started'] = '20180101';
        $result = $this->TitleScore->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->TitleScore->errors());

        $data['started'] = 'testtest';
        $result = $this->TitleScore->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->TitleScore->errors());

        // ended
        $data = $params;
        $data['ended'] = '20180101';
        $result = $this->TitleScore->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->TitleScore->errors());

        $data['ended'] = 'testtest';
        $result = $this->TitleScore->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->TitleScore->errors());
    }
}
