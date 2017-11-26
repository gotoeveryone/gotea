<?php
namespace Gotea\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PlayerScoresFixture
 *
 */
class PlayerScoresFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'サロゲートキー', 'autoIncrement' => true, 'precision' => null],
        'player_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '棋士ID', 'precision' => null, 'autoIncrement' => null],
        'rank_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '段位ID', 'precision' => null, 'autoIncrement' => null],
        'target_year' => ['type' => 'integer', 'length' => 4, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '対象年', 'precision' => null, 'autoIncrement' => null],
        'win_point' => ['type' => 'integer', 'length' => 3, 'unsigned' => false, 'null' => true, 'default' => '0', 'comment' => '勝数', 'precision' => null, 'autoIncrement' => null],
        'lose_point' => ['type' => 'integer', 'length' => 3, 'unsigned' => false, 'null' => true, 'default' => '0', 'comment' => '敗数', 'precision' => null, 'autoIncrement' => null],
        'draw_point' => ['type' => 'integer', 'length' => 3, 'unsigned' => false, 'null' => true, 'default' => '0', 'comment' => '引分数', 'precision' => null, 'autoIncrement' => null],
        'win_point_world' => ['type' => 'integer', 'length' => 3, 'unsigned' => false, 'null' => true, 'default' => '0', 'comment' => '勝数（国際棋戦）', 'precision' => null, 'autoIncrement' => null],
        'lose_point_world' => ['type' => 'integer', 'length' => 3, 'unsigned' => false, 'null' => true, 'default' => '0', 'comment' => '敗数（国際棋戦）', 'precision' => null, 'autoIncrement' => null],
        'draw_point_world' => ['type' => 'integer', 'length' => 3, 'unsigned' => false, 'null' => true, 'default' => '0', 'comment' => '引分数（国際棋戦）', 'precision' => null, 'autoIncrement' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '初回登録日時', 'precision' => null],
        'created_by' => ['type' => 'string', 'length' => 10, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '初回登録者', 'precision' => null, 'fixed' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '更新日時', 'precision' => null],
        'modified_by' => ['type' => 'string', 'length' => 10, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '最終更新者', 'precision' => null, 'fixed' => null],
        '_indexes' => [
            'fk_scores_to_rank' => ['type' => 'index', 'columns' => ['rank_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'uq_player_scores' => ['type' => 'unique', 'columns' => ['player_id', 'target_year'], 'length' => []],
            'fk_scores_to_player' => ['type' => 'foreign', 'columns' => ['player_id'], 'references' => ['players', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
            'fk_scores_to_rank' => ['type' => 'foreign', 'columns' => ['rank_id'], 'references' => ['ranks', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
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
            'target_year' => 1,
            'win_point' => 1,
            'lose_point' => 1,
            'draw_point' => 1,
            'win_point_world' => 1,
            'lose_point_world' => 1,
            'draw_point_world' => 1,
            'created' => '2017-11-26 15:09:52',
            'created_by' => 'Lorem ip',
            'modified' => '2017-11-26 15:09:52',
            'modified_by' => 'Lorem ip'
        ],
    ];
}
