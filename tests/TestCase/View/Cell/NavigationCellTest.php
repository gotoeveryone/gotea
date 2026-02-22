<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\View\Cell;

use Cake\TestSuite\TestCase;
use Gotea\View\AppView;
use Gotea\View\Cell\NavigationCell;

/**
 * Gotea\View\Cell\NavigationCell Test Case
 */
class NavigationCellTest extends TestCase
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
     * @var \Gotea\View\Cell\NavigationCell
     */
    public $Navigation;

    /**
     * @var \Gotea\View\AppView
     */
    public $View;

    /**
     * Fixtures
     *
     * @var array
     */
    public array $fixtures = [
        'app.PlayerRanks',
        'app.Players',
        'app.Countries',
        'app.Ranks',
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
        $this->Navigation = new NavigationCell($this->request, $this->response);
        $this->View = new AppView($this->request, $this->response);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
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
        $recents = $this->Navigation->viewBuilder()->getVar('recents');
        $this->assertNotNull($recents);
        foreach ($recents as $items) {
            foreach ($items as $item) {
                $this->assertGreaterThan(1, $item->rank->rank_numeric);
                $this->assertNotEquals($item->player->joined_ymd, $item->promoted->format('Ymd'));
            }
        }
    }
}
