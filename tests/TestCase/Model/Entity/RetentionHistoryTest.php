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
    public function setUp(): void
    {
        parent::setUp();
        $this->RetentionHistory = new RetentionHistory();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->RetentionHistory);

        parent::tearDown();
    }

    /**
     * Test isRecent method if `broadcasted` field has value
     *
     * @return void
     */
    public function testIsRecentHasBroadcasted()
    {
        $this->RetentionHistory->broadcasted = FrozenDate::now();
        $this->assertTrue($this->RetentionHistory->isRecent());

        $this->RetentionHistory->broadcasted = FrozenDate::now()->subDay(20);
        $this->assertTrue($this->RetentionHistory->isRecent());

        $this->RetentionHistory->broadcasted = FrozenDate::now()->subDay(21);
        $this->assertFalse($this->RetentionHistory->isRecent());
    }

    /**
     * Test isRecent method if `broadcasted` field not has value
     *
     * @return void
     */
    public function testIsRecentNoHasBroadcasted()
    {
        $this->RetentionHistory->acquired = FrozenDate::now();
        $this->assertTrue($this->RetentionHistory->isRecent());

        $this->RetentionHistory->acquired = FrozenDate::now()->subDay(20);
        $this->assertTrue($this->RetentionHistory->isRecent());

        $this->RetentionHistory->acquired = FrozenDate::now()->subDay(21);
        $this->assertFalse($this->RetentionHistory->isRecent());
    }
}
