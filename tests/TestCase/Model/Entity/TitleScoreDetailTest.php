<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Model\Entity;

use Cake\I18n\FrozenDate;
use Cake\TestSuite\TestCase;
use Gotea\Model\Entity\Country;
use Gotea\Model\Entity\Player;
use Gotea\Model\Entity\PlayerRank;
use Gotea\Model\Entity\Rank;
use Gotea\Model\Entity\TitleScoreDetail;

/**
 * Gotea\Model\Entity\TitleScoreDetail Test Case
 */
class TitleScoreDetailTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Gotea\Model\Entity\TitleScoreDetail
     */
    protected $TitleScoreDetail;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->TitleScoreDetail = new TitleScoreDetail();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->TitleScoreDetail);

        parent::tearDown();
    }

    /**
     * Test getPlayerNameWithRank method
     *
     * @return void
     */
    public function testGetPlayerNameWithRank(): void
    {
        $rank = new Rank();
        $rank->name = 'test';

        $player = new Player();
        $player->rank = $rank;

        $playerRank = new PlayerRank();
        $playerRank->promoted = FrozenDate::parseDate('2021-01-01');
        $playerRank->rank = $rank;

        $this->TitleScoreDetail->player = $player;
        $this->TitleScoreDetail->player_name = 'test player';

        $name = $this->TitleScoreDetail->getPlayerNameWithRank(FrozenDate::parseDate('2021-02-01'));
        $this->assertStringContainsString($this->TitleScoreDetail->player_name, $name);
        $this->assertStringContainsString($rank->name, $name);
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
        $player->rank = $rank;
        $this->TitleScoreDetail->player = $player;

        // name + country
        $rankingName = $this->TitleScoreDetail->getRankingName(true, false);
        $this->assertStringContainsString($player->name_english, $rankingName);
        $this->assertStringContainsString($country->name_english, $rankingName);

        // name + country + showJp
        $rankingName = $this->TitleScoreDetail->getRankingName(true, true);
        $this->assertStringContainsString($player->name, $rankingName);
        $this->assertStringContainsString($player->country->name, $rankingName);

        // name + rank
        $rankingName = $this->TitleScoreDetail->getRankingName(false, false);
        $this->assertStringContainsString($player->name_english, $rankingName);
        $this->assertStringContainsString((string)$rank->rank_numeric, $rankingName);

        // name + rank + showJp
        $rankingName = $this->TitleScoreDetail->getRankingName(false, true);
        $this->assertStringContainsString($player->name, $rankingName);
        $this->assertStringContainsString($rank->name, $rankingName);
    }
}
