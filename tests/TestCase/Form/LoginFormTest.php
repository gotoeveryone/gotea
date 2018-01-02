<?php
namespace Gotea\Test\TestCase\Form;

use Cake\TestSuite\TestCase;
use Gotea\Form\LoginForm;

/**
 * Gotea\Form\LoginForm Test Case
 */
class LoginFormTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Gotea\Form\LoginForm
     */
    public $Login;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Login = new LoginForm();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Login);

        parent::tearDown();
    }

    /**
     * Test validation fail
     *
     * @return void
     */
    public function testValidateFail()
    {
        // requirePresence
        $data = [
            'password' => 'password',
        ];
        $result = $this->Login->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Login->errors());

        $data = [
            'account' => 'test',
        ];
        $result = $this->Login->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Login->errors());

        // requirePresence with notEmpty
        $data = [
            'account' => '',
            'password' => 'password',
        ];
        $result = $this->Login->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Login->errors());

        $data = [
            'account' => 'test',
            'password' => '',
        ];
        $result = $this->Login->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Login->errors());

        // maxLength
        $data = [
            'account' => '12345678901',
            'password' => 'password'
        ];
        $result = $this->Login->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Login->errors());

        $data = [
            'account' => 'test',
            'password' => '123456789012345678901',
        ];
        $result = $this->Login->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Login->errors());

        // alphaNumeric
        $data = [
            'account' => 'テスト',
            'password' => 'password',
        ];
        $result = $this->Login->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Login->errors());

        $data = [
            'account' => 'test',
            'password' => 'テスト',
        ];
        $result = $this->Login->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Login->errors());
    }

    /**
     * Test validation success
     *
     * @return void
     */
    public function testValidateSuccess()
    {
        $data = [
            'account' => '1234567890',
            'password' => '12345678901234567890',
        ];
        $result = $this->Login->validate($data);
        $this->assertTrue($result);
        $this->assertEmpty($this->Login->errors());
    }
}
