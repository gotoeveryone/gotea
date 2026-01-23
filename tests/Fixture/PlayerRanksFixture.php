<?php
namespace Gotea\Test\Fixture;

use Cake\I18n\FrozenDate;
use Cake\TestSuite\Fixture\TestFixture;

/**
 * PlayerRanksFixture
 */
class PlayerRanksFixture extends TestFixture
{
    /**
     * Records
     *
     * @var array
     */
    public array $records = [
        [
            'id' => 1,
            'player_id' => 1,
            'rank_id' => 1,
            'promoted' => '2017-11-26',
            'created' => '2017-11-26 15:09:51',
            'modified' => '2017-11-26 15:09:51',
        ],
    ];

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        // 最近の昇段情報
        $now = FrozenDate::now();
        $this->records[] = [
            'player_id' => 1,
            'rank_id' => 2,
            'promoted' => $now->format('Y/m/d'),
            'created' => '2017-11-26 15:09:51',
            'modified' => '2017-11-26 15:09:51',
        ];
        $this->records[] = [
            'player_id' => 1,
            'rank_id' => 3,
            'promoted' => $now->format('Y/m/d'),
            'created' => '2017-11-26 15:09:51',
            'modified' => '2017-11-26 15:09:51',
        ];

        parent::init();
    }
}
