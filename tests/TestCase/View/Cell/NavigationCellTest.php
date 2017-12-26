<?php
namespace Gotea\Test\TestCase\View\Cell;

use Cake\TestSuite\TestCase;
use Gotea\View\Cell\NavigationCell;

/**
 * Gotea\View\Cell\NavigationCell Test Case
 */
class NavigationCellTest extends TestCase
{
    /**
     * Request mock
     *
     * @var \Cake\Network\Request|\PHPUnit_Framework_MockObject_MockObject
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
     * @var \Gotea\View\Cell\NavigationCell
     */
    public $Navigation;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->request = $this->getMockBuilder('Cake\Http\ServerRequest')->getMock();
        $this->response = $this->getMockBuilder('Cake\Http\Response')->getMock();
        $this->Navigation = new NavigationCell($this->request, $this->response);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Navigation);

        parent::tearDown();
    }

    /**
     * ビュー変数にデータが設定されていること
     *
     * @return void
     */
    public function testDisplay()
    {
        $this->Navigation->display();
        $this->assertNotNull($this->Navigation->viewVars['recents']);
    }
}
