<?php
namespace Gotea\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PlayerRanksFixture
 *
 */
class PlayerRanksFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'サロゲートキー', 'autoIncrement' => true, 'precision' => null],
        'player_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '棋士ID', 'precision' => null, 'autoIncrement' => null],
        'rank_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '段位ID', 'precision' => null, 'autoIncrement' => null],
        'promoted' => ['type' => 'date', 'length' => null, 'null' => false, 'default' => null, 'comment' => '昇段日', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '初回登録日時', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '更新日時', 'precision' => null],
        '_indexes' => [
            'player_id' => ['type' => 'index', 'columns' => ['player_id'], 'length' => []],
            'rank_id' => ['type' => 'index', 'columns' => ['rank_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'player_ranks_ibfk_1' => ['type' => 'foreign', 'columns' => ['player_id'], 'references' => ['players', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
            'player_ranks_ibfk_2' => ['type' => 'foreign', 'columns' => ['rank_id'], 'references' => ['ranks', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'player_id' => 1,
            'rank_id' => 1,
            'promoted' => '2017-11-26',
            'created' => '2017-11-26 15:09:51',
            'modified' => '2017-11-26 15:09:51'
        ],
    ];
}
