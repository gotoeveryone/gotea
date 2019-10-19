<?php
namespace Gotea\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * RetentionHistoriesFixture
 *
 */
class RetentionHistoriesFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'サロゲートキー', 'autoIncrement' => true, 'precision' => null],
        'title_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'タイトルID', 'precision' => null, 'autoIncrement' => null],
        'player_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '優勝棋士ID', 'precision' => null, 'autoIncrement' => null],
        'country_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '優勝棋士出場国ID', 'precision' => null, 'autoIncrement' => null],
        'rank_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '優勝棋士段位ID', 'precision' => null, 'autoIncrement' => null],
        'holding' => ['type' => 'integer', 'length' => 3, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '期', 'precision' => null, 'autoIncrement' => null],
        'target_year' => ['type' => 'integer', 'length' => 4, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '対象年', 'precision' => null, 'autoIncrement' => null],
        'name' => ['type' => 'string', 'length' => 30, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'タイトル名', 'precision' => null, 'fixed' => null],
        'win_group_name' => ['type' => 'string', 'length' => 30, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '優勝チーム名', 'precision' => null, 'fixed' => null],
        'is_team' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '0', 'comment' => '団体戦判定', 'precision' => null],
        'acquired' => ['type' => 'date', 'length' => null, 'null' => false, 'default' => null, 'comment' => '取得日', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '初回登録日時', 'precision' => null],
        'created_by' => ['type' => 'string', 'length' => 10, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '初回登録者', 'precision' => null, 'fixed' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '更新日時', 'precision' => null],
        'modified_by' => ['type' => 'string', 'length' => 10, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '最終更新者', 'precision' => null, 'fixed' => null],
        '_indexes' => [
            'fk_histories_to_player' => ['type' => 'index', 'columns' => ['player_id'], 'length' => []],
            'fk_histories_to_country' => ['type' => 'index', 'columns' => ['country_id'], 'length' => []],
            'fk_histories_to_rank' => ['type' => 'index', 'columns' => ['rank_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'uq_retention_histories' => ['type' => 'unique', 'columns' => ['title_id', 'holding'], 'length' => []],
            'fk_histories_to_player' => ['type' => 'foreign', 'columns' => ['player_id'], 'references' => ['players', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
            'fk_histories_to_country' => ['type' => 'foreign', 'columns' => ['country_id'], 'references' => ['countries', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
            'fk_histories_to_rank' => ['type' => 'foreign', 'columns' => ['rank_id'], 'references' => ['ranks', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
            'fk_histories_to_title' => ['type' => 'foreign', 'columns' => ['title_id'], 'references' => ['titles', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
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
            'title_id' => 1,
            'player_id' => 1,
            'rank_id' => 1,
            'holding' => 1,
            'target_year' => 1,
            'name' => 'Lorem ipsum dolor sit amet',
            'win_group_name' => null,
            'is_team' => 0,
            'acquired' => '2017-11-26',
            'created' => '2017-11-26 14:35:24',
            'created_by' => 'Lorem ip',
            'modified' => '2017-11-26 14:35:24',
            'modified_by' => 'Lorem ip'
        ],
        [
            'id' => 2,
            'title_id' => 1,
            'player_id' => null,
            'rank_id' => null,
            'holding' => 2,
            'target_year' => 1,
            'name' => 'Lorem ipsum dolor sit amet',
            'win_group_name' => 'Lorem ipsum dolor sit amet',
            'is_team' => 1,
            'acquired' => '2017-11-26',
            'created' => '2017-11-26 14:35:24',
            'created_by' => 'Lorem ip',
            'modified' => '2017-11-26 14:35:24',
            'modified_by' => 'Lorem ip'
        ],
        [
            'id' => 3,
            'title_id' => 2,
            'player_id' => 1,
            'rank_id' => 1,
            'holding' => 1,
            'target_year' => 1,
            'name' => 'Lorem ipsum dolor sit amet',
            'win_group_name' => null,
            'is_team' => 0,
            'acquired' => '2017-11-26',
            'created' => '2017-11-26 14:35:24',
            'created_by' => 'Lorem ip',
            'modified' => '2017-11-26 14:35:24',
            'modified_by' => 'Lorem ip'
        ],
        [
            'id' => 4,
            'title_id' => 2,
            'player_id' => null,
            'rank_id' => null,
            'holding' => 2,
            'target_year' => 1,
            'name' => 'Lorem ipsum dolor sit amet',
            'win_group_name' => 'Lorem ipsum dolor sit amet',
            'is_team' => 1,
            'acquired' => '2017-11-26',
            'created' => '2017-11-26 14:35:24',
            'created_by' => 'Lorem ip',
            'modified' => '2017-11-26 14:35:24',
            'modified_by' => 'Lorem ip'
        ],
    ];
}
