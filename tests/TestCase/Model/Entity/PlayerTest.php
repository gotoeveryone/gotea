<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Model\Entity;

use Cake\I18n\FrozenDate;
use Cake\TestSuite\TestCase;
use Gotea\Model\Entity\Country;
use Gotea\Model\Entity\Player;
use Gotea\Model\Entity\PlayerRank;
use Gotea\Model\Entity\Rank;

/**
 * Gotea\Model\Entity\Player Test Case
 */
class PlayerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Gotea\Model\Entity\Player
     */
    protected $Player;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Player = new Player();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Player);

        parent::tearDown();
    }

    /**
     * Test getRankByDate method
     *
     * @return void
     */
    public function testGetRankByDate(): void
    {
        $rank = new Rank();

        $this->Player->rank = $rank;

        $this->assertEquals($rank, $this->Player->getRankByDate(FrozenDate::now()));

        $playerRank1 = new PlayerRank();
        $rank1 = new Rank();
        $playerRank1->promoted = FrozenDate::parseDate('2021-01-01');
        $playerRank1->rank = $rank1;
        $rank2 = new Rank();
        $playerRank2 = new PlayerRank();
        $playerRank2->promoted = FrozenDate::parseDate('2021-02-01');
        $playerRank2->rank = $rank2;
        $rank3 = new Rank();
        $playerRank3 = new PlayerRank();
        $playerRank3->promoted = FrozenDate::parseDate('2021-03-01');
        $playerRank3->rank = $rank3;

        $this->Player->player_ranks = [
            $playerRank1,
            $playerRank2,
            $playerRank3,
        ];

        $this->assertEquals($rank1, $this->Player->getRankByDate(FrozenDate::parse('2021-01-31')));
        $this->assertEquals($rank2, $this->Player->getRankByDate(FrozenDate::parse('2021-02-28')));
        $this->assertEquals($rank3, $this->Player->getRankByDate(FrozenDate::parse('2021-03-01')));
    }

    /**
     * Test toArray method
     *
     * @return void
     */
    public function testToArray(): void
    {
        $country = new Country();
        $country->id = 1;
        $country->name = 'test country';

        $rank = new Rank();
        $rank->id = 2;
        $rank->name = 'test rank';

        $this->Player->id = 3;
        $this->Player->name = 'name';
        $this->Player->name_english = 'english name';
        $this->Player->name_other = 'other name';
        $this->Player->sex = '男性';
        $this->Player->birthday = FrozenDate::parse('1988-01-01');
        $this->Player->country = $country;
        $this->Player->rank = $rank;
        $this->Player->is_retired = true;
        $this->Player->retired = FrozenDate::parse('2020-12-31');

        $result = $this->Player->toArray();
        $this->assertEquals($result['name'], $this->Player->name);
        $this->assertEquals($result['nameEnglish'], $this->Player->name_english);
        $this->assertEquals($result['nameOther'], $this->Player->name_other);
        $this->assertEquals($result['sex'], $this->Player->sex);
        $this->assertEquals($result['birthday'], $this->Player->birthday->format('Y/m/d'));
        $this->assertEquals($result['countryId'], $this->Player->country->id);
        $this->assertEquals($result['countryName'], $this->Player->country->name);
        $this->assertEquals($result['rankId'], $this->Player->rank->id);
        $this->assertEquals($result['rankName'], $this->Player->rank->name);
        $this->assertEquals($result['isRetired'], $this->Player->is_retired);
        $this->assertEquals($result['retired'], $this->Player->retired);
    }

    /**
     * Test isMale method
     *
     * @return void
     */
    public function testIsMale(): void
    {
        $this->Player->sex = '男性';
        $this->assertTrue($this->Player->isMale());

        $this->Player->sex = '女性';
        $this->assertFalse($this->Player->isMale());

        $this->Player->sex = '不明';
        $this->assertFalse($this->Player->isMale());
    }

    /**
     * Test isFemale method
     *
     * @return void
     */
    public function testIsFemale(): void
    {
        $this->Player->sex = '男性';
        $this->assertFalse($this->Player->isFemale());

        $this->Player->sex = '女性';
        $this->assertTrue($this->Player->isFemale());

        $this->Player->sex = '不明';
        $this->assertFalse($this->Player->isFemale());
    }
}
