<?php

namespace Gotea\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * RetentionHistoriesFixture
 */
class RetentionHistoriesFixture extends TestFixture
{
    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'title_id' => 1,
            'player_id' => 1,
            'country_id' => 1,
            'holding' => 1,
            'target_year' => 1,
            'name' => 'Lorem ipsum dolor sit amet',
            'win_group_name' => null,
            'is_team' => 0,
            'acquired' => '2017-11-26',
            'created' => '2017-11-26 14:35:24',
            'created_by' => 'Lorem ip',
            'modified' => '2017-11-26 14:35:24',
            'modified_by' => 'Lorem ip',
        ],
        [
            'id' => 2,
            'title_id' => 1,
            'player_id' => null,
            'holding' => 2,
            'target_year' => 1,
            'name' => 'Lorem ipsum dolor sit amet',
            'win_group_name' => 'Lorem ipsum dolor sit amet',
            'is_team' => 1,
            'acquired' => '2017-11-26',
            'created' => '2017-11-26 14:35:24',
            'created_by' => 'Lorem ip',
            'modified' => '2017-11-26 14:35:24',
            'modified_by' => 'Lorem ip',
        ],
        [
            'id' => 3,
            'title_id' => 2,
            'player_id' => 1,
            'country_id' => 1,
            'holding' => 1,
            'target_year' => 1,
            'name' => 'Lorem ipsum dolor sit amet',
            'win_group_name' => null,
            'is_team' => 0,
            'acquired' => '2017-11-26',
            'created' => '2017-11-26 14:35:24',
            'created_by' => 'Lorem ip',
            'modified' => '2017-11-26 14:35:24',
            'modified_by' => 'Lorem ip',
        ],
        [
            'id' => 4,
            'title_id' => 2,
            'player_id' => null,
            'holding' => 2,
            'target_year' => 1,
            'name' => 'Lorem ipsum dolor sit amet',
            'win_group_name' => 'Lorem ipsum dolor sit amet',
            'is_team' => 1,
            'acquired' => '2017-11-26',
            'created' => '2017-11-26 14:35:24',
            'created_by' => 'Lorem ip',
            'modified' => '2017-11-26 14:35:24',
            'modified_by' => 'Lorem ip',
        ],
    ];
}
