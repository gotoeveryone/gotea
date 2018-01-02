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
     * Test validation
     *
     * @return void
     */
    public function testValidate()
    {
        $params = [
            'name' => 'test',
            'name_english' => 'test',
            'name_other' => '',
            'joined_from' => '',
            'joined_to' => '',
        ];

        // success
        $result = $this->Player->validate($params);
        $this->assertTrue($result);
        $this->assertEmpty($this->Player->errors());

        $params['joined_from'] = 2001;
        $params['joined_to'] = 2017;
        $result = $this->Player->validate($params);
        $this->assertTrue($result);
        $this->assertEmpty($this->Player->errors());

        // integer
        $names = [
            'country_id', 'rank_id', 'organization_id',
            'is_retired', 'joined_from', 'joined_to',
        ];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = '1a';
            $result = $this->Player->validate($data);
            $this->assertFalse($result);
            $this->assertNotEmpty($this->Player->errors());

            $data[$name] = 'test';
            $result = $this->Player->validate($data);
            $this->assertFalse($result);
            $this->assertNotEmpty($this->Player->errors());

            $data[$name] = '0.5';
            $result = $this->Player->validate($data);
            $this->assertFalse($result);
            $this->assertNotEmpty($this->Player->errors());
        }

        // alphaNumeric
        $names = ['name_english'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = 'テスト';
            $result = $this->Player->validate($data);
            $this->assertFalse($result);
            $this->assertNotEmpty($this->Player->errors());
        }

        // maxLength
        $data = [
            'name' => '123456789012345678901',
        ];
        $result = $this->Player->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Player->errors());

        $data = [
            'name_english' => '12345678901234567890123456789012345678901',
        ];
        $result = $this->Player->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Player->errors());

        $data = [
            'name_other' => '123456789012345678901',
        ];
        $result = $this->Player->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Player->errors());

        // range
        $data = [
            'joined_from' => 0,
        ];
        $result = $this->Player->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Player->errors());

        $data = [
            'joined_from' => 10000,
        ];
        $result = $this->Player->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Player->errors());

        $data = [
            'joined_to' => 0,
        ];
        $result = $this->Player->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Player->errors());

        $data = [
            'joined_to' => 10000,
        ];
        $result = $this->Player->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Player->errors());
    }
}
