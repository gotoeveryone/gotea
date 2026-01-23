<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\View\Cell;

use Cake\TestSuite\TestCase;
use Gotea\View\Cell\CountriesCell;

/**
 * Gotea\View\Cell\CountriesCell Test Case
 */
class CountriesCellTest extends TestCase
{
    /**
     * Request mock
     *
     * @var \Cake\Http\ServerRequest|\PHPUnit_Framework_MockObject_MockObject
     */
    public $request;

    /**
     * Response mock
     *
     * @var \Cake\Http\Response|\PHPUnit_Framework_MockObject_MockObject
     */
    public $response;

    /**
     * Test subject
     *
     * @var \Gotea\View\Cell\CountriesCell
     */
    public $Countries;

    /**
     * Fixtures
     *
     * @var array
     */
    public array $fixtures = [
        'app.Countries',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->request = $this->getMockBuilder('Cake\Http\ServerRequest')->getMock();
        $this->response = $this->getMockBuilder('Cake\Http\Response')->getMock();
        $this->Countries = new CountriesCell($this->request, $this->response);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Countries);

        parent::tearDown();
    }

    /**
     * Test display method
     *
     * @return void
     */
    public function testDisplay()
    {
        $this->Countries->display();
        $countries = $this->Countries->viewBuilder()->getVar('countries');
        $attributes = $this->Countries->viewBuilder()->getVar('attributes');
        $this->assertNotEmpty($countries);
        $this->assertEmpty($attributes);
    }
}
