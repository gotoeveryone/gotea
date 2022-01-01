<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Model\Entity;

use Cake\TestSuite\TestCase;
use Gotea\Model\Entity\Country;
use Gotea\Model\Entity\Player;
use Gotea\Model\Entity\PlayerScore;
use Gotea\Model\Entity\Rank;

/**
 * Gotea\Model\Entity\PlayerScore Test Case
 */
class PlayerScoreTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Gotea\Model\Entity\PlayerScore
     */
    protected $PlayerScore;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PlayerScore = new PlayerScore();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->PlayerScore);

        parent::tearDown();
    }

    /**
     * Test getRankingName method
     *
     * @return void
     */
    public function testGetRankingName(): void
    {
        $player = new Player();
        $player->name = 'テスト';
        $player->name_english = 'test english';

        $country = new Country();
        $country->name = '日本';
        $country->name_english = 'Japan';

        $rank = new Rank();
        $rank->name = '初段';
        $rank->rank_numeric = 1;

        $player->country = $country;
        $this->PlayerScore->player = $player;
        $this->PlayerScore->rank = $rank;

        // name + country
        $rankingName = $this->PlayerScore->getRankingName(true, false);
        $this->assertStringContainsString($player->name_english, $rankingName);
        $this->assertStringContainsString($country->name_english, $rankingName);

        // name + country + showJp
        $rankingName = $this->PlayerScore->getRankingName(true, true);
        $this->assertStringContainsString($player->name, $rankingName);
        $this->assertStringContainsString($player->country->name, $rankingName);

        // name + rank
        $rankingName = $this->PlayerScore->getRankingName(false, false);
        $this->assertStringContainsString($player->name_english, $rankingName);
        $this->assertStringContainsString((string)$rank->rank_numeric, $rankingName);

        // name + rank + showJp
        $rankingName = $this->PlayerScore->getRankingName(false, true);
        $this->assertStringContainsString($player->name, $rankingName);
        $this->assertStringContainsString($rank->name, $rankingName);
    }
}
