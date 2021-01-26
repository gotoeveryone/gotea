<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Model\Entity;

use Cake\I18n\FrozenDate;
use Cake\TestSuite\TestCase;
use Gotea\Model\Entity\TitleScore;

/**
 * Gotea\Model\Entity\RetentionHistory Test Case
 */
class TitleScoreTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Gotea\Model\Entity\TitleScore
     */
    public $TitleScore;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->TitleScore = new TitleScore();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->TitleScore);

        parent::tearDown();
    }

    /**
     * Test is_same_started property
     *
     * @return void
     */
    public function testIsSameStarted()
    {
        $this->TitleScore->started = FrozenDate::now();
        $this->TitleScore->ended = FrozenDate::now()->addDay(1);
        $this->assertFalse($this->TitleScore->is_same_started);

        $this->TitleScore->started = FrozenDate::now()->addDay(1);
        $this->TitleScore->ended = FrozenDate::now();
        $this->assertFalse($this->TitleScore->is_same_started);

        $this->TitleScore->started = FrozenDate::now();
        $this->TitleScore->ended = FrozenDate::now();
        $this->assertTrue($this->TitleScore->is_same_started);
    }
}
