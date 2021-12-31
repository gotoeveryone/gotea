<?php
namespace Gotea\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TitleScoresFixture
 */
class TitleScoresFixture extends TestFixture
{
    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'country_id' => 1,
            'title_id' => 1,
            'name' => 'Test Score 1',
            'started' => '2017-11-20',
            'ended' => '2017-11-20',
            'is_world' => 0,
            'created' => '2017-11-26 15:09:49',
            'modified' => '2017-11-26 15:09:49',
        ],
        [
            'id' => 2,
            'country_id' => 1,
            'title_id' => 1,
            'name' => 'Test Score 2',
            'started' => '2017-12-23',
            'ended' => '2017-12-23',
            'is_world' => 0,
            'created' => '2017-11-26 15:09:49',
            'modified' => '2017-11-26 15:09:49',
        ],
        [
            'id' => 3,
            'country_id' => 5,
            'title_id' => 2,
            'name' => 'Test World Score 1',
            'started' => '2017-11-23',
            'ended' => '2017-11-23',
            'is_world' => 1,
            'created' => '2017-11-26 15:09:49',
            'modified' => '2017-11-26 15:09:49',
        ],
        [
            'id' => 4,
            'country_id' => 5,
            'title_id' => 2,
            'name' => 'Test World Score 2',
            'started' => '2017-12-31',
            'ended' => '2017-12-31',
            'is_world' => 1,
            'created' => '2017-11-26 15:09:49',
            'modified' => '2017-11-26 15:09:49',
        ],
        [
            'id' => 5,
            'country_id' => 5,
            'title_id' => 2,
            'name' => 'Test World Score 3',
            'started' => '2018-01-01',
            'ended' => '2018-01-01',
            'is_world' => 1,
            'created' => '2017-11-26 15:09:49',
            'modified' => '2017-11-26 15:09:49',
        ],
    ];
}
