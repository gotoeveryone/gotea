<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Model\Entity;

use Cake\I18n\FrozenDate;
use Cake\TestSuite\TestCase;
use Gotea\Model\Entity\RetentionHistory;

/**
 * Gotea\Model\Entity\RetentionHistory Test Case
 */
class RetentionHistoryTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Gotea\Model\Entity\RetentionHistory
     */
    public $RetentionHistory;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->RetentionHistory = new RetentionHistory();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->RetentionHistory);

        parent::tearDown();
    }

    /**
     * Test isRecent method
     *
     * @return void
     */
    public function testIsRecent()
    {
        $this->RetentionHistory->acquired = FrozenDate::now();
        $this->assertTrue($this->RetentionHistory->isRecent());

        $this->RetentionHistory->acquired = FrozenDate::now()->subDay(20);
        $this->assertTrue($this->RetentionHistory->isRecent());

        $this->RetentionHistory->acquired = FrozenDate::now()->subDay(21);
        $this->assertFalse($this->RetentionHistory->isRecent());
    }
}
