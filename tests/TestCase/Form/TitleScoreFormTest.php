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
        // maxLength
        $data = [
            'name' => '123456789012345678901',
        ];
        $result = $this->TitleScore->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->TitleScore->errors());

        // numeric
        $data = [
            'country_id' => '1a',
        ];
        $result = $this->TitleScore->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->TitleScore->errors());

        $data = [
            'target_year' => '1a',
        ];
        $result = $this->TitleScore->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->TitleScore->errors());

        // range
        $data = [
            'target_year' => 0,
        ];
        $result = $this->TitleScore->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->TitleScore->errors());

        $data = [
            'target_year' => 10000,
        ];
        $result = $this->TitleScore->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->TitleScore->errors());

        // date
        $data = [
            'started' => '20180101',
        ];
        $result = $this->TitleScore->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->TitleScore->errors());

        $data = [
            'started' => 'testtest',
        ];
        $result = $this->TitleScore->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->TitleScore->errors());

        $data = [
            'ended' => '20181231',
        ];
        $result = $this->TitleScore->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->TitleScore->errors());

        $data = [
            'ended' => 'testtest',
        ];
        $result = $this->TitleScore->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->TitleScore->errors());
    }

    /**
     * Test validation success
     *
     * @return void
     */
    public function testValidateSuccess()
    {
        $data = [
            'name' => '12345678901234567890',
            'country_id' => 1,
            'target_year' => 2017,
            'started' => '2018/01/01',
            'started' => '2018/12/31',
        ];
        $result = $this->TitleScore->validate($data);
        $this->assertTrue($result);
        $this->assertEmpty($this->TitleScore->errors());
    }
}
