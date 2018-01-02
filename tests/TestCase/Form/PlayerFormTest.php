<?php
namespace Gotea\Test\TestCase\Form;

use Cake\TestSuite\TestCase;
use Gotea\Form\PlayerForm;

/**
 * Gotea\Form\PlayerForm Test Case
 */
class PlayerFormTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Gotea\Form\PlayerForm
     */
    public $Player;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Player = new PlayerForm();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Player);

        parent::tearDown();
    }

    /**
     * Test validation fail
     *
     * @return void
     */
    public function testValidateFail()
    {
        // alphaNumeric
        $data = [
            'name_english' => 'テスト',
        ];
        $result = $this->Player->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Player->errors());

        // numeric
        $data = [
            'country_id' => '1a',
        ];
        $result = $this->Player->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Player->errors());

        $data = [
            'rank_id' => '1a',
        ];
        $result = $this->Player->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Player->errors());

        $data = [
            'organization_id' => '1a',
        ];
        $result = $this->Player->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Player->errors());

        $data = [
            'is_retired' => '1a',
        ];
        $result = $this->Player->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Player->errors());

        // maxLength
        $data = [
            'name' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        ];
        $result = $this->Player->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Player->errors());

        $data = [
            'name_english' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        ];
        $result = $this->Player->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Player->errors());

        $data = [
            'name_other' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        ];
        $result = $this->Player->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Player->errors());

        // numeric
        $data = [
            'joined_from' => 'test',
        ];
        $result = $this->Player->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Player->errors());

        $data = [
            'joined_to' => 'test',
        ];
        $result = $this->Player->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Player->errors());

        // range
        $data = [
            'joined_from' => 0,
            'joined_to' => 2007,
        ];
        $result = $this->Player->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Player->errors());

        $data = [
            'joined_from' => 2001,
            'joined_to' => 10000,
        ];
        $result = $this->Player->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Player->errors());
    }

    /**
     * Test validation success
     *
     * @return void
     */
    public function testValidateSuccess()
    {
        $data = [
            'name' => 'test',
            'name_english' => 'test',
            'name_other' => '',
            'joined_from' => '',
            'joined_to' => '',
        ];
        $result = $this->Player->validate($data);
        $this->assertTrue($result);
        $this->assertEmpty($this->Player->errors());

        $data['joined_from'] = 2001;
        $data['joined_to'] = 2017;
        $result = $this->Player->validate($data);
        $this->assertTrue($result);
        $this->assertEmpty($this->Player->errors());
    }
}
