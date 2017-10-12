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
        'title_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'タイトルID', 'precision' => null, 'autoIncrement' => null],
        'name' => ['type' => 'string', 'length' => 20, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '棋戦名', 'precision' => null, 'fixed' => null],
        'started' => ['type' => 'date', 'length' => null, 'null' => false, 'default' => null, 'comment' => '開始日', 'precision' => null],
        'ended' => ['type' => 'date', 'length' => null, 'null' => false, 'default' => null, 'comment' => '終了日', 'precision' => null],
        'is_world' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '0', 'comment' => '国際棋戦かどうか', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '初回登録日時', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '更新日時', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
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
            'name' => 'Lorem ipsum dolor ',
            'started' => '2017-01-21',
            'ended' => '2017-01-21',
            'is_world' => 1,
            'created' => '2017-01-21 04:40:33',
            'modified' => '2017-01-21 04:40:33'
        ],
    ];
}
