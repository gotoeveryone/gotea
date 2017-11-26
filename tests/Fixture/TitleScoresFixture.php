<?php
namespace Gotea\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TitleScoresFixture
 *
 */
class TitleScoresFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'サロゲートキー', 'autoIncrement' => true, 'precision' => null],
        'country_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '所属国ID', 'precision' => null, 'autoIncrement' => null],
        'title_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'タイトルID', 'precision' => null, 'autoIncrement' => null],
        'name' => ['type' => 'string', 'length' => 20, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '棋戦名', 'precision' => null, 'fixed' => null],
        'started' => ['type' => 'date', 'length' => null, 'null' => false, 'default' => null, 'comment' => '開始日', 'precision' => null],
        'ended' => ['type' => 'date', 'length' => null, 'null' => false, 'default' => null, 'comment' => '終了日', 'precision' => null],
        'is_world' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '0', 'comment' => '国際棋戦かどうか', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '初回登録日時', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '更新日時', 'precision' => null],
        '_indexes' => [
            'fk_title_scores_to_country' => ['type' => 'index', 'columns' => ['country_id'], 'length' => []],
            'idx_started' => ['type' => 'index', 'columns' => ['started'], 'length' => []],
            'idx_ended' => ['type' => 'index', 'columns' => ['ended'], 'length' => []],
            'idx_is_world' => ['type' => 'index', 'columns' => ['is_world'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_title_scores_to_country' => ['type' => 'foreign', 'columns' => ['country_id'], 'references' => ['countries', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
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
            'country_id' => 1,
            'title_id' => 1,
            'name' => 'Lorem ipsum dolor ',
            'started' => '2017-11-26',
            'ended' => '2017-11-26',
            'is_world' => 1,
            'created' => '2017-11-26 15:09:49',
            'modified' => '2017-11-26 15:09:49'
        ],
    ];
}
