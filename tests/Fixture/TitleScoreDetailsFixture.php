<?php
namespace Gotea\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TitleScoreDetailsFixture
 *
 */
class TitleScoreDetailsFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'サロゲートキー', 'autoIncrement' => true, 'precision' => null],
        'title_score_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'タイトル成績ID', 'precision' => null, 'autoIncrement' => null],
        'player_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '棋士ID', 'precision' => null, 'autoIncrement' => null],
        'rank_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '段位ID', 'precision' => null, 'autoIncrement' => null],
        'player_name' => ['type' => 'string', 'length' => 20, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '棋士名', 'precision' => null, 'fixed' => null],
        'division' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '成績区分', 'precision' => null, 'fixed' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '初回登録日時', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '更新日時', 'precision' => null],
        '_indexes' => [
            'fk_details_to_player' => ['type' => 'index', 'columns' => ['player_id'], 'length' => []],
            'idx_division' => ['type' => 'index', 'columns' => ['division'], 'length' => []],
            'rank_id' => ['type' => 'index', 'columns' => ['rank_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'uq_title_score_details' => ['type' => 'unique', 'columns' => ['title_score_id', 'player_id'], 'length' => []],
            'fk_details_to_player' => ['type' => 'foreign', 'columns' => ['player_id'], 'references' => ['players', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
            'fk_details_to_title_score' => ['type' => 'foreign', 'columns' => ['title_score_id'], 'references' => ['title_scores', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
            'title_score_details_ibfk_1' => ['type' => 'foreign', 'columns' => ['rank_id'], 'references' => ['ranks', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
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
            'title_score_id' => 1,
            'player_id' => 1,
            'rank_id' => 1,
            'name' => 'Test1',
            'division' => '勝',
            'created' => '2017-11-26 15:09:50',
            'modified' => '2017-11-26 15:09:50'
        ],
        [
            'title_score_id' => 1,
            'player_id' => 2,
            'rank_id' => 2,
            'name' => 'Test2',
            'division' => '敗',
            'created' => '2017-11-26 15:09:50',
            'modified' => '2017-11-26 15:09:50'
        ],
        [
            'title_score_id' => 2,
            'player_id' => 2,
            'rank_id' => 3,
            'name' => 'Test3',
            'division' => '勝',
            'created' => '2017-11-26 15:09:50',
            'modified' => '2017-11-26 15:09:50'
        ],
        [
            'title_score_id' => 2,
            'player_id' => 1,
            'rank_id' => 4,
            'name' => 'Test4',
            'division' => '敗',
            'created' => '2017-11-26 15:09:50',
            'modified' => '2017-11-26 15:09:50'
        ],
    ];
}
