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
     * Test validation
     *
     * @return void
     */
    public function testValidate()
    {
        $params = [
            'account' => '1234567890',
            'password' => '12345678901234567890',
        ];

        // success
        $result = $this->Login->validate($params);
        $this->assertTrue($result);
        $this->assertEmpty($this->Login->errors());

        // requirePresence
        foreach ($params as $name => $value) {
            $data = $params;
            unset($data[$name]);
            $result = $this->Login->validate($data);
            $this->assertFalse($result);
            $this->assertNotEmpty($this->Login->errors());

            $data[$name] = '';
            $result = $this->Login->validate($data);
            $this->assertFalse($result);
            $this->assertNotEmpty($this->Login->errors());
        }

        // alphaNumeric
        $names = ['account', 'password'];
        foreach ($names as $name) {
            $data = $params;
            $data[$name] = 'テスト';
            $result = $this->Login->validate($data);
            $this->assertFalse($result);
            $this->assertNotEmpty($this->Login->errors());
        }

        // maxLength
        $data = $params;
        $data['account'] = '12345678901';
        $result = $this->Login->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Login->errors());

        $data = $params;
        $data['password'] = '123456789012345678901';
        $result = $this->Login->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($this->Login->errors());
    }
}
