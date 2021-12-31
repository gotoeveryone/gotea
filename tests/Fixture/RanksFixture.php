<?php
namespace Gotea\Test\Fixture;

use Cake\I18n\FrozenTime;
use Cake\TestSuite\Fixture\TestFixture;

/**
 * RanksFixture
 */
class RanksFixture extends TestFixture
{
    /**
     * @inheritDoc
     */
    public function init(): void
    {
        $ranks = [
            [
                'id' => 1,
                'number' => 1,
                'name' => '初段',
            ],
            [
                'id' => 2,
                'number' => 2,
                'name' => '二段',
            ],
            [
                'id' => 3,
                'number' => 3,
                'name' => '三段',
            ],
            [
                'id' => 4,
                'number' => 4,
                'name' => '四段',
            ],
            [
                'id' => 5,
                'number' => 5,
                'name' => '五段',
            ],
            [
                'id' => 6,
                'number' => 6,
                'name' => '六段',
            ],
            [
                'id' => 7,
                'number' => 7,
                'name' => '七段',
            ],
            [
                'id' => 8,
                'number' => 8,
                'name' => '八段',
            ],
            [
                'id' => 9,
                'number' => 9,
                'name' => '九段',
            ],
            [
                'number' => null,
                'name' => 'アマ',
            ],
        ];
        $now = FrozenTime::now();

        foreach ($ranks as $rank) {
            $this->records[] = [
                'name' => $rank['name'],
                'rank_numeric' => $rank['number'],
                'created' => $now,
                'modified' => $now,
            ];
        }

        parent::init();
    }
}
